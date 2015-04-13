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


class Mirasvit_Advr_Model_Resource_Product_Collection extends Mirasvit_Advr_Model_Resource_Collection_Abstract
{
    protected $_entityIdField = 'main_table.entity_id';

    protected function _construct()
    {
        $this->_init('catalog/product');

        $this->setMainTable($this->getTable('catalog/product'));
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);

        return $this;
    }

    public function groupByParentProduct()
    {
        $this->_entityIdField = 'parent_table.entity_id';

        $this->getSelect()
            ->group($this->_entityIdField);

        return $this;
    }

    public function groupByProduct()
    {
        $this->getSelect()
            ->group($this->_entityIdField);

        return $this;
    }

    public function groupBySimpleProduct()
    {
        $this->getSelect()
            ->where('main_table.type_id=?', 'simple')
            ->group($this->_entityIdField);

        return $this;
    }

    public function groupByAttribute($attribute)
    {
        $this->getSelect()
            ->group('attribute_table.value');

        return $this;
    }

    public function joinProduct($fields = array())
    {
        foreach ($fields as $field) {
            $this->getSelect()->columns(array($field => $field), 'main_table');
        }

        return $this;
    }

    public function joinStockItem($fields = array())
    {
        $this->getSelect()->join(
            array('stock_item' => $this->getTable('cataloginventory/stock_item')),
            sprintf('stock_item.product_id = '.$this->_entityIdField),
            array()
        );

        foreach ($fields as $field) {
            $this->getSelect()->columns(array($field => "SUM($field)"), 'stock_item');
        }

        return $this;
    }

    public function joinAttribute($attribute)
    {
        $product  = Mage::getResourceSingleton('catalog/product');
        $attr     = $product->getAttribute($attribute);
        $joinExprProductName       = array(
            'attribute_table.entity_id = IFNULL((SELECT product_id FROM '.$this->getTable('sales/order_item').' WHERE parent_item_id=order_item.item_id), order_item.product_id)',
            'attribute_table.entity_type_id = '.$product->getTypeId(),
            'attribute_table.attribute_id = '.$attr->getAttributeId(),
            'attribute_table.store_id = 0'
        );

        $this->getSelect()->joinLeft(
            array('attribute_table' => $attr->getBackend()->getTable()),
            implode(' AND ', $joinExprProductName),
            array('attribute' => 'attribute_table.value')
        );

        return $this;
    }

    public function joinParentProduct($fields = array())
    {
        $this->getSelect()
            ->joinLeft(
                array('product_relation' => $this->getTable('catalog/product_relation')),
                'product_relation.child_id = main_table.entity_id',
                array())
            ->joinLeft(
                array('parent_table' => $this->getTable('catalog/product')),
                'parent_table.entity_id = product_relation.parent_id',
                array()
            )
            ->where('product_relation.parent_id > 0');

        foreach ($fields as $field) {
            $this->getSelect()->columns(array($field => $field), 'parent_table');
        }

        return $this;
    }

    public function joinOrderItem()
    {
        $onConditions = array();

        $onConditions[] = 'order_item.product_id = '.$this->_entityIdField;
        if ($this->_filterData->getFrom()) {
            $from = $this->_filterData->getFrom();
            $onConditions[] = "order_item.created_at > '$from'";
        }

        if ($this->_filterData->getTo()) {
            $to = $this->_filterData->getTo();
            $onConditions[] = "order_item.created_at < '$to'";
        }

        if (count($this->_filterData->getStoreIds())) {
            $stores = implode(',', $this->_filterData->getStoreIds());
            $onConditions[] = "order_item.store_id IN($stores)";
        }

        $this->getSelect()
            ->joinLeft(
                array('order_item' => $this->getTable('sales/order_item')),
                implode(' AND ', $onConditions),
                array()
            )
            ->where('order_item.parent_item_id IS NULL')
            ;


        $this->addSumColumn('order_item', 'qty_ordered')
            ->addSumColumn('order_item', 'qty_refunded')
            ->addSumColumn('order_item', 'base_tax_amount', 'sum_tax_amount')
            ->addSumColumn('order_item', 'base_discount_amount', 'sum_discount_amount')
            ->addSumColumn('order_item', 'base_amount_refunded', 'sum_amount_refunded')
            ->addSumColumn('order_item', 'base_row_total', 'sum_row_total')
            ;

        $this->getSelect()->columns(array('quantity' => 'COUNT(DISTINCT(order_item.order_id))'));

        return $this;
    }

    public function joinOrder()
    {

        $this->getSelect()
            ->joinLeft(
                array('order_table' => $this->getTable('sales/order')),
                'order_item.order_id = order_table.entity_id',
                array()
            );

        $statuses = Mage::getSingleton('advr/config')->getProcessOrderStatuses();

        $this->getSelect()
            ->where('order_table.status IN(?)', $statuses);

        return $this;
    }

    public function joinAttributeSet()
    {
        $this->getSelect()
            ->joinLeft(
                array('attribute_set' => $this->getTable('eav/attribute_set')),
                'attribute_set.attribute_set_id = main_table.attribute_set_id',
                array());

        return $this;
    }

    public function joinProductName()
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

    public function joinCategoryName()
    {
        $category = Mage::getResourceSingleton('catalog/category');
        $attr     = $category->getAttribute('name');
        $joinExprProductName       = array(
            'category_name.entity_id = category_product.category_id',
            'category_name.entity_type_id = '.$category->getTypeId(),
            'category_name.attribute_id = '.$attr->getAttributeId()
        );

        $select = $this->getConnection()->select();
        $select->from(
                array('category_product' => $this->getTable('catalog/category_product')),
                array()
            )->joinLeft(
                array(
                    'category_name' => $attr->getBackend()->getTable()),
                implode(' AND ', $joinExprProductName),
                array('category_name' => new Zend_Db_Expr('GROUP_CONCAT(category_name.value SEPARATOR ", ")'))
            )
            ->where('category_product.product_id = main_table.entity_id');

        $this->getSelect()->columns(array('category' => $select));

        return $this;
    }

    public function joinCategoryId()
    {
        $category = Mage::getResourceSingleton('catalog/category');

        $select = $this->getConnection()->select();
        $select->from(
                array('category_product' => $this->getTable('catalog/category_product')),
                array('category_id' => new Zend_Db_Expr('CONCAT(",",GROUP_CONCAT(category_product.category_id SEPARATOR ","),",")'))
            )->where('category_product.product_id = main_table.entity_id')
            ->limit(1)
            ;

        $this->getSelect()->columns(array('category' => $select));

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
        $countSelect->columns();
        
        $select = 'SELECT COUNT(*) FROM ('.$countSelect->__toString().') as cnt';
        
        return $select;
    }
}