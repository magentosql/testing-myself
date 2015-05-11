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


class Mirasvit_Advr_Model_Resource_Customer_Collection extends Mirasvit_Advr_Model_Resource_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('customer/customer');

        $this->setMainTable($this->getTable('customer/entity'));
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()
            ->reset(Zend_Db_Select::COLUMNS)
            ->columns(array(
                'email',
                'created_at'
            ))
            ->group('main_table.entity_id')
            ;

        return $this;
    }

    public function joinCustomerGroup()
    {
        $this->getSelect()
            ->joinLeft(
                array('customer_group_table' => $this->getTable('customer/customer_group')),
                'main_table.group_id = customer_group_table.customer_group_id',
                array('customer_group_code')
            )
            ;

        return $this;
    }

    public function joinOrdersInformation()
    {
        $this->getSelect()
            ->joinLeft(
                array('order_table' => $this->getTable('sales/order')),
                'main_table.entity_id = order_table.customer_id',
                array()
            )
            ->columns(array(
                'last_order_at' => 'MAX(order_table.created_at)',
            ))
            ;

        $this->addSumColumn('order_table', 'base_grand_total', 'sum_grand_total')
            ->addAvgColumn('order_table', 'base_grand_total', 'avg_grand_total')
        ;

        return $this;
    }

    public function joinPurchasedProducts()
    {
        $this->getSelect()
            ->joinLeft(
                array('order_item_table' => $this->getTable('sales/order_item')),
                'order_item_table.order_id = order_table.entity_id',
                array('products' => new Zend_Db_Expr('
                    CONCAT_WS("@",
                        GROUP_CONCAT(order_item_table.product_id SEPARATOR "^"),
                        GROUP_CONCAT(order_item_table.name SEPARATOR "^"),
                        GROUP_CONCAT(order_item_table.sku SEPARATOR "^"),
                        GROUP_CONCAT(order_item_table.qty_ordered SEPARATOR "^")
                    )')
                )
            )
            ;

        return $this;
    }

    public function joinBillingAddress()
    {
         $this->getSelect()
            ->joinLeft(
                array('address_table' => $this->getTable('sales/order_address')),
                'address_table.entity_id = order_table.billing_address_id',
                array(
                    'country_id' => 'address_table.country_id',
                    'postcode'   => 'address_table.postcode',
                    'postcode'   => 'address_table.postcode',
                    'company'    => 'address_table.company',
                    'name'       => 'CONCAT(address_table.firstname, " ", address_table.lastname)',
                )
            )
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
}