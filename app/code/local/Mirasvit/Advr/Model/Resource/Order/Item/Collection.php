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


class Mirasvit_Advr_Model_Resource_Order_Item_Collection extends Mirasvit_Advr_Model_Resource_Collection_Abstract
{
    protected $_entityIdField = 'main_table.item_id';
    protected $_internalSelect = null;
    
    protected $_orderJoined = false;

    protected function _construct()
    {
        $this->_init('sales/order_item');
    }

    protected function _initSelect()
    {
        $this->_internalSelect = $this->getConnection()->select();
        $this->_internalSelect
            ->from(array('item_table' => $this->getTable('sales/order_item')));

        $this->getSelect()->from(
            array('main_table' => $this->_internalSelect),
            array()
        );

        $this->_internalSelect
            ->joinLeft(
                array('order_table' => $this->getTable('sales/order')),
                'order_table.entity_id = item_table.order_id',
                array('coupon_code', 'customer_id', 'customer_firstname', 'customer_lastname', 'customer_email'));

        $this->_orderJoined = true;

        $statuses = Mage::getSingleton('advr/config')->getProcessOrderStatuses();

        $this->_internalSelect
            ->where('order_table.status IN(?)', $statuses);

        $this->getSelect()
            ->columns(array('quantity'              => 'COUNT(DISTINCT(main_table.order_id))'))
            ->columns(array('sum_total_qty_ordered' => 'COUNT(main_table.qty_ordered)'))
            ;

        $this
            ->addSumColumn('main_table', 'base_discount_amount')
            ->addSumColumn('main_table', 'base_tax_amount')
            ->addSumColumn('main_table', 'base_row_total')
            ->addSumColumn('main_table', new Zend_Db_Expr('base_row_total - base_discount_amount'), 'sum_grand_total')
            ->addSumColumn('main_table', 'base_row_invoiced')
            ->addSumColumn('main_table', 'base_amount_refunded')

            ->addAvgColumn('main_table', 'base_discount_amount')
            ->addAvgColumn('main_table', 'base_tax_amount')
            ->addAvgColumn('main_table', 'base_row_total')
            ->addAvgColumn('main_table', new Zend_Db_Expr('base_row_total - base_discount_amount'), 'avg_grand_total')
            ->addAvgColumn('main_table', 'base_row_invoiced')
            ->addAvgColumn('main_table', 'base_amount_refunded')
            ;

        return $this;
    }

    public function joinOrder()
    {
        return $this;
    }

    public function joinBillingAddress()
    {
        $this->joinOrder();

        $this->_internalSelect
            ->joinLeft(
                array('address_table' => $this->getTable('sales/order_address')),
                'address_table.entity_id = order_table.billing_address_id',
                array(
                    'country_id' => 'address_table.country_id',
                    'region_id'  => 'IFNULL(address_table.region_id, address_table.region)',
                    'city'       => 'address_table.city',
                    'postcode'   => 'address_table.postcode',
                    'postcode'   => 'address_table.postcode',
                    'company'    => 'address_table.company',
                )
            )
            ;

        return $this;
    }

    public function joinPayment()
    {
        $this->joinOrder();

        $this->_internalSelect
            ->joinLeft(
                array('payment_table' => $this->getTable('sales/order_payment')),
                'payment_table.parent_id = order_table.entity_id',
                array('payment_method' => 'payment_table.method'));

        return $this;
    }

    public function joinCustomerGroup()
    {
        $this->joinOrder();

        $this->_internalSelect
            ->joinLeft(
                array('customer_group' => $this->getTable('customer/customer_group')),
                'customer_group.customer_group_id = order_table.customer_group_id',
                array('customer_group_code' => 'customer_group.customer_group_code'));

        $this->getSelect()
            ->columns(array('customer_group_code' => new Zend_Db_Expr('main_table.customer_group_code')));

        return $this;
    }

    public function joinCustomerType()
    {
        $this->joinOrder();
        
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

    public function groupByHourOfDay()
    {
        $expression = new Zend_Db_Expr('HOUR('.$this->getTZDate('main_table.created_at').')');

        $this->getSelect()
            ->group($expression)
            ->columns(array('hour_of_day' => $expression))
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

    public function groupByZIP()
    {
        $expression = new Zend_Db_Expr('postcode');

        $this->getSelect()
            ->group($expression)
            ->columns(array('postcode' => $expression))
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
        $this->joinOrder();

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
        $this->joinOrder();

        $expression = new Zend_Db_Expr('main_table.customer_id');

        $this->getSelect()
            ->group($expression)
            ->columns(array('customer_id' => $expression))
            ->columns(array(
                    'customer_email',
                    'customer_name' => 'CONCAT(main_table.customer_firstname, " ", main_table.customer_lastname)')
            )
            ;


        return $this;
    }

    public function addFilterBySku($sku)
    {
        $this->_internalSelect->where('item_table.sku like "%'.$sku.'%"');

        return $this;
    }

    public function addFilterByProductId($id)
    {
        $this->_internalSelect->where('item_table.product_id = ?', intval($id));

        return $this;
    }

    public function addFilterByAttributeOptionId($attribute, $value)
    {
        $product  = Mage::getResourceSingleton('catalog/product');
        
        $joinExprProductName       = array(
            'attribute_table.entity_id = item_table.product_id',
            'attribute_table.entity_type_id = '.$product->getTypeId(),
            'attribute_table.attribute_id = '.$attribute->getId(),
            'attribute_table.store_id = 0'
        );

        $this->_internalSelect
            ->joinLeft(
                array('attribute_table' => $attribute->getBackend()->getTable()),
                implode(' AND ', $joinExprProductName),
                array()
            )
            ->where('attribute_table.value = ?', $value);

        return $this;
    }

    public function joinProduct()
    {
        $this->getSelect()
            ->joinLeft(
                array('catalog_product' => $this->getTable('catalog/product')),
                'catalog_product.entity_id = main_table.product_id',
                array('sku'))
            ->group('catalog_product.entity_id');

        return $this;
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

        return $this;
    }
}