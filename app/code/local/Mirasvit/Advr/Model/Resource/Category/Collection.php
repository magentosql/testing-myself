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
 * @build     345
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advr_Model_Resource_Category_Collection extends Mirasvit_Advr_Model_Resource_Collection_Abstract
{
    protected $_entityIdField = 'main_table.entity_id';

    protected function _construct()
    {
        $this->_init('catalog/category');

        $this->setMainTable($this->getTable('catalog/category'));
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);

        return $this;
    }

    public function groupByCategory()
    {
        $this->_entityIdField = 'main_table.entity_id';

        $this->getSelect()
            ->group($this->_entityIdField);

        return $this;
    }

    public function joinCategory($fields = array())
    {
        foreach ($fields as $field) {
            $this->getSelect()->columns(array($field => $field), 'main_table');
        }

        $this->getSelect()->order('main_table.path');

        return $this;
    }

    public function joinAttribute($attribute)
    {
        $category  = Mage::getResourceSingleton('catalog/category');
        $attr     = $category->getAttribute($attribute);
        $joinExpr       = array(
            'attribute_table.entity_id = main_table.entity_id',
            'attribute_table.entity_type_id = '.$category->getTypeId(),
            'attribute_table.attribute_id = '.$attr->getAttributeId(),
            'attribute_table.store_id = 0'
        );

        $this->getSelect()->joinLeft(
            array('attribute_table' => $attr->getBackend()->getTable()),
            implode(' AND ', $joinExpr),
            array($attribute => 'attribute_table.value')
        );

        return $this;
    }

    public function joinProducts($fields = array())
    {
        $this->getSelect()
            ->joinLeft(
                array('product_relation_table' => $this->getTable('catalog/category_product')),
                'product_relation_table.category_id = main_table.entity_id',
                array())
            ->joinLeft(
                array('product_table' => $this->getTable('catalog/product')),
                'product_table.entity_id = product_relation_table.product_id',
                array()
            );

        // foreach ($fields as $field) {
        //     $this->getSelect()->columns(array($field => $field), 'parent_table');
        // }

        return $this;
    }

    public function joinOrderItem()
    {
        $this->joinProducts();

        $onConditions = array();

        $onConditions[] = 'order_item.product_id = product_table.entity_id';
        
        if (count($this->_filterData->getStoreIds())) {
            $stores = implode(',', $this->_filterData->getStoreIds());
            $onConditions[] = "order_item.store_id IN($stores)";
        }

        $this->getSelect()
            ->joinLeft(
                array('order_item' => $this->getTable('sales/order_item')),
                implode(' AND ', $onConditions),
                array()
            );

        if ($this->_filterData->getFrom()) {
            $from = $this->_filterData->getFrom();
            $this->getSelect()->where("order_item.created_at > '$from'");
        }

        if ($this->_filterData->getTo()) {
            $to = $this->_filterData->getTo();
            $this->getSelect()->where("order_item.created_at < '$to'");
        }

        $this->addSumColumn('order_item', 'qty_ordered')
            ->addSumColumn('order_item', 'qty_refunded')
            ->addSumColumn('order_item', 'base_tax_amount', 'sum_tax_amount')
            ->addSumColumn('order_item', 'base_discount_amount', 'sum_discount_amount')
            ->addSumColumn('order_item', 'base_amount_refunded', 'sum_amount_refunded')
            ->addSumColumn('order_item', 'base_row_total', 'sum_row_total');

        return $this;
    }

    public function joinCategoryName()
    {
        $product  = Mage::getResourceSingleton('catalog/product');
        $attr     = $product->getAttribute('name');

        $joinExprProductName       = array(
            'product_name.entity_id = '.$this->_entityIdField,
            'product_name.entity_type_id = '.$product->getTypeId(),
            'product_name.attribute_id = '.$attr->getAttributeId(),
            'product_name.store_id = 0'
        );

        $this->getSelect()->joinLeft(
            array('product_name' => $attr->getBackend()->getTable()),
            implode(' AND ', $joinExprProductName),
            array('product_name' => 'product_name.value')
        );

        return $this;
    }

    public function setFilterData($data)
    {
        $this->_filterData = $data;

        return $this;
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);
        $countSelect->reset(Zend_Db_Select::GROUP);
        $countSelect->reset(Zend_Db_Select::HAVING);
        $countSelect->columns("COUNT(DISTINCT(".$this->_entityIdField."))");

        return $countSelect;
    }
}