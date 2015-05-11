<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.0
 * @build     370
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advr_Model_Resource_Order_Collection extends Mirasvit_Advr_Model_Resource_Collection_Abstract
{
    protected $_entityIdField = 'main_table.entity_id';
    protected $_internalSelect = null;

    protected function _construct()
    {
        $this->_init('sales/order');
    }

    protected function _initSelect()
    {
        $this->_internalSelect = $this->getConnection()->select();
        $this->_internalSelect
            ->from(array('order_table' => $this->getTable('sales/order')));

        $this->getSelect()->from(
            array('main_table' => $this->_internalSelect),
            array()
        );

        $this->getSelect()->columns(array('quantity' => 'COUNT(main_table.entity_id)'));
        $this->getSelect()->columns(array('sum_gross_profit' => 'SUM(main_table.base_subtotal_invoiced - main_table.base_total_invoiced_cost)'));

        $this
            ->addSumColumn('main_table', 'total_qty_ordered')
            ->addSumColumn('main_table', 'base_discount_amount')
            ->addSumColumn('main_table', 'base_shipping_amount')
            ->addSumColumn('main_table', 'base_tax_amount')
            ->addSumColumn('main_table', 'base_shipping_tax_amount')
            ->addSumColumn('main_table', 'base_subtotal')
            ->addSumColumn('main_table', 'base_total_paid')
            ->addSumColumn('main_table', 'base_grand_total')
            ->addSumColumn('main_table', 'base_total_invoiced')
            ->addSumColumn('main_table', 'base_total_refunded')
            ->addSumColumn('main_table', 'base_total_invoiced_cost')

            ->addAvgColumn('main_table', 'total_qty_ordered')
            ->addAvgColumn('main_table', 'base_discount_amount')
            ->addAvgColumn('main_table', 'base_shipping_amount')
            ->addAvgColumn('main_table', 'base_tax_amount')
            ->addAvgColumn('main_table', 'base_shipping_tax_amount')
            ->addAvgColumn('main_table', 'base_subtotal')
            ->addAvgColumn('main_table', 'base_total_paid')
            ->addAvgColumn('main_table', 'base_grand_total')
            ->addAvgColumn('main_table', 'base_total_invoiced')
            ->addAvgColumn('main_table', 'base_total_refunded')
            ->addAvgColumn('main_table', 'base_total_invoiced_cost')
            ;

        $statuses = Mage::getSingleton('advr/config')->getProcessOrderStatuses();

        $this->_internalSelect
            ->where('order_table.status IN(?)', $statuses);

        return $this;
    }

    public function groupByPeriod()
    {
        $expression = $this->_getRangeExpressionForAttribute(
            $this->getFilterData()->getRange(),
            $this->getTZDate('main_table.created_at')
        );

        $this->getSelect()
            ->group($expression)
            ->columns(array('period' => $expression))
            ;

        return $this;
    }

    public function groupByStatus()
    {
        $expression = 'main_table.status';


        $this->_internalSelect
            ->joinLeft(
                array('status_table' => $this->getTable('sales/order_status')),
                'status_table.status = order_table.status',
                array())
            ->columns(array(
                    'status_label' => "status_table.label",
                )
            )
            ->setPart('WHERE', null)
            ;

        $this->getSelect()
            ->group($expression)
            ->columns(array('status' => $expression))
            ->columns(array('status_label' => 'status_label'))
            ;

        return $this;
    }

    public function groupByHourOfDay()
    {
        $expression = new Zend_Db_Expr('HOUR('.$this->getTZDate('main_table.created_at').')');

        $this->getSelect()
            ->group($expression)
            ->columns(array('hour_of_day' => $expression))
            ;

        return $this;
    }

    public function groupByZIP()
    {
        $expression = new Zend_Db_Expr('postcode');

        $this->getSelect()
            ->group($expression)
            ->columns(array('postcode' => $expression))
            ;

        return $this;
    }

    public function groupByDayOfWeek()
    {
        $expression = new Zend_Db_Expr('WEEKDAY('.$this->getTZDate('main_table.created_at').')');

        $this->getSelect()
            ->group($expression)
            ->columns(array('day_of_week' => $expression))
            ;

        return $this;
    }

    public function groupByCountry()
    {
        $this->joinBillingAddress();
        $expression = new Zend_Db_Expr('main_table.country_id');

        $this->getSelect()
            ->group($expression)
            ->columns(array('country' => $expression))
            ;

        return $this;
    }

    public function groupByState()
    {
        $this->joinBillingAddress();
        $expression = new Zend_Db_Expr('main_table.region_id');

        $this->getSelect()
            ->group($expression)
            ->columns(array('region_id' => 'main_table.region_id'))
            ;

        return $this;
    }

    public function groupByCity()
    {
        $this->joinBillingAddress();
        $expression = new Zend_Db_Expr('main_table.city');

        $this->getSelect()
            ->group($expression)
            ->columns(array('city' => 'main_table.city'))
            ;

        return $this;
    }

    public function groupByPaymentMethod()
    {
        $this->joinPayment();
        $expression = new Zend_Db_Expr('main_table.payment_method');

        $this->getSelect()
            ->group($expression)
            ->columns(array('payment_method' => $expression))
            ;

        return $this;
    }

    public function groupByCustomerGroup()
    {
        $this->joinCustomerGroup();
        $expression = new Zend_Db_Expr('main_table.customer_group_code');

        $this->getSelect()
            ->group($expression)
            ;

        return $this;
    }

    public function groupByCouponCode()
    {
        $expression = new Zend_Db_Expr('main_table.coupon_code');

        $this->_internalSelect
            ->where('order_table.coupon_code IS NOT NULL');

        $this->getSelect()
            ->group($expression)
            ->columns(array('coupon_code' => $expression))
            ;

        return $this;
    }

    public function groupByCustomer()
    {
        $expression = new Zend_Db_Expr('main_table.customer_id');

        $this->getSelect()
            ->group($expression)
            ->columns(
                array(
                    'customer_id' => $expression,
                    'customer_email',
                    'last_order_date' => new Zend_Db_Expr('MAX(main_table.created_at)'),
                    'customer_name' => 'CONCAT(main_table.customer_firstname, " ", main_table.customer_lastname)'
                )
            )
            ;

        return $this;
    }

    public function joinPayment()
    {
        $this->_internalSelect
            ->joinLeft(
                array('payment_table' => $this->getTable('sales/order_payment')),
                'payment_table.parent_id = order_table.entity_id',
                array('payment_method' => 'payment_table.method'));

        return $this;
    }

    public function joinBillingAddress()
    {
         $this->_internalSelect
            ->joinLeft(
                array('address' => $this->getTable('sales/order_address')),
                'address.entity_id = order_table.billing_address_id',
                array(
                    'country_id' => 'address.country_id',
                    'region_id'  => 'IFNULL(address.region_id, address.region)',
                    'city'       => 'address.city',
                    'postcode'   => 'address.postcode',
                    'postcode'   => 'address.postcode',
                    'company'    => 'address.company',
                )
            )
            ;

        $this->getSelect()
            ->columns(array('postcode', 'company'))
            ;

        return $this;
    }

    public function joinCustomerGroup()
    {
        $this->_internalSelect
            ->joinLeft(
                array('customer_group' => $this->getTable('customer/customer_group')),
                'customer_group.customer_group_id = order_table.customer_group_id',
                array('customer_group_code' => 'customer_group.customer_group_code'));

        $this->getSelect()
            ->columns(array('customer_group_code' => new Zend_Db_Expr('main_table.customer_group_code')))
            ;

        return $this;
    }
    
    public function joinCustomerType()
    {
        $expr = "(DATE_FORMAT(`order_table`.`created_at`, '%Y-%m-%d') = DATE_FORMAT(`customer`.`created_at`, '%Y-%m-%d'))";
        $this->_internalSelect
            ->joinLeft(
                array('customer' => $this->getTable('customer/entity')),
                'customer.entity_id = order_table.customer_id',
                array())
            ->columns(array(
                    'is_new'                => "IF ($expr, 1, 0)",
                    'is_returning'          => "IF ($expr, 0, 1)",
                    'grand_total_new'       => "IF ($expr, grand_total, 0)",
                    'grand_total_returning' => "IF ($expr, 0, grand_total)",
                )
            )
            ;

        $this->getSelect()
            ->columns(array('is_new' => 'SUM(is_new)'))
            ->columns(array('is_returning' => 'SUM(is_returning)'))
            ->columns(array('sum_grand_total_new' => 'SUM(grand_total_new)'))
            ->columns(array('sum_grand_total_returning' => 'SUM(grand_total_returning)'))
            ;

        return $this;
    }

    public function joinShippingTime()
    {
        $expr = new Zend_Db_Expr('unix_timestamp(shipment_table.created_at) - unix_timestamp(order_table.created_at)');

        $this->_internalSelect
            ->joinLeft(
                array('shipment_table' => $this->getTable('sales/shipment')),
                'shipment_table.order_id = order_table.entity_id',
                array())
            ->columns(array(
                    'shipped_at'    => 'shipment_table.created_at',
                    'shipping_time' => $expr,
                    'time_0_1'      => "IF($expr <= 3600, 1, 0)",
                    'time_1_24'     => "IF($expr > 3600 AND $expr <= 86400, 1, 0)",
                    'time_24_48'    => "IF($expr > 86400 AND $expr <= 172800, 1, 0)",
                    'time_48_72'    => "IF($expr > 172800 AND $expr <= 259200, 1, 0)",
                    'time_72_'      => "IF($expr > 259200, 1, 0)",
                )
            )
            ;
        $this->getSelect()
            ->columns(array('avg_shipping_time' => new Zend_Db_Expr('AVG(shipping_time)')))
            ->columns(array('sum_time_0_1'   => new Zend_Db_Expr('SUM(time_0_1)')))
            ->columns(array('sum_time_1_24'  => new Zend_Db_Expr('SUM(time_1_24)')))
            ->columns(array('sum_time_24_48' => new Zend_Db_Expr('SUM(time_24_48)')))
            ->columns(array('sum_time_48_72' => new Zend_Db_Expr('SUM(time_48_72)')))
            ->columns(array('sum_time_72_'   => new Zend_Db_Expr('SUM(time_72_)')))
            ;

        return $this;
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);        
        $countSelect->columns();
        
        $select = 'SELECT COUNT(*) FROM ('.$countSelect->__toString().') as cnt';
        
        return $select;
    }

    public function setFilterData($data)
    {
        $this->_filterData = $data;

        if ($this->_filterData->getFrom()) {
            $this->_internalSelect
                ->where($this->getTZDate('order_table.created_at')." >= '".$this->_filterData->getFrom()."'");
        }

        if ($this->_filterData->getTo()) {
            $this->_internalSelect
                ->where($this->getTZDate('order_table.created_at')." < '".$this->_filterData->getTo()."'");
        }

        if (count($this->_filterData->getStoreIds())) {
            $this->_internalSelect
                ->where('order_table.store_id IN('.implode(',', $this->_filterData->getStoreIds()).')');
        }

        return $this;
    }
}
