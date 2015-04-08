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


class Mirasvit_Advr_Block_Adminhtml_Product_Attribute 
    extends Mirasvit_Advr_Block_Adminhtml_Product_Abstract
{
    public function _prepareLayout()
    {
        $this->setChartType('column');

        parent::_prepareLayout();

        $this->getToolbar()
            ->setRangesVisibility(false)
            ->setCompareVisibility(false);

        $this->getChart()
            ->setXAxisType('category')
            ->setXAxisField('attribute');

        $this->getGrid()
            ->setDefaultSort('sum_row_total')
            ->setDefaultDir('desc')
            ->setDefaultLimit(1000)
            ->setPagerVisibility(false);

        $this->setHeaderText(Mage::helper('advr')->__('Sales by Attribute'));

        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $products = Mage::getResourceModel('advr/product_collection')
            ->setFilterData($filterData);

        $attribute = $filterData->getGroupByAttribute();

        if (!$attribute) {
            $attribute = 'status';
        }

        $categories = Mage::getSingleton('advr/system_config_source_category')->toOptionArray();

        $products->joinProduct()
            ->joinOrderItem()
            ->joinOrder()
            ->joinAttribute($attribute)
            ->joinProductName();

        $categories ? $products->joinCategoryId() : $products->joinCategoryName();

        $products->groupByAttribute($attribute);

        if ($filterData->getFilterByCategory()) {
            $products->addFieldToFilter('category', array('like' => '%'.$filterData->getFilterByCategory().'%'));
        }
        // echo $products->getSelect();die();
        $collection = Mage::getModel('advr/collection');

        Mage::helper('advr/collection')->prepareAttributeCollection(
            $collection,
            $attribute,
            'attribute'
        );

        $collection->setResourceCollection($products)
            ->setColumnGroupBy('attribute');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'attribute' => array(
                'header'          => $this->_getAttribute() ? $this->_getAttribute()->getFrontendLabel() : '',
                'type'            => 'text',
                'totals_label'    => 'Total',
                'filter'          => false,
                'frame_callback'  => array($this, 'frameCallbackAttribute'),
                'chart'           => true,
                'sortable'        => true,
                'grouped'         => true,
                'export_callback' => array($this, 'frameCallbackAttribute'),
            ),

            'percent' => array(
                'header'          => 'Total, %',
                'type'            => 'percent',
                'index'           => 'sum_row_total',
                'frame_callback'  => array(Mage::helper('advr/callback'), 'percent'),
                'filter'          => false,
                'export_callback' => array(Mage::helper('advr/callback'), '_percent'),
            ),
        );

        $columns += $this->getBaseColumns();

        return $columns;
    }

    public function _initToolbar()
    {
        parent::_initToolbar();

        $form = $this->_toolbar->getForm();

        $values = array(
            array('value' => '', 'label' => '-')
        );
        
        $attributeCollection = Mage::getResourceModel('catalog/product_attribute_collection')
            ->addFieldToFilter('frontend_input', array('select'));

        foreach($attributeCollection as $attribute) {
            if ($attribute->getFrontendLabel() && $attribute->getAttributeCode()) {
                $values[$attribute->getAttributeCode()] = array(
                    'value' => $attribute->getAttributeCode(),
                    'label' => $attribute->getFrontendLabel(),
                );
            }
        }

        $form->addField('group_by_attribute', 'select', array(
            'name'    => 'group_by_attribute',
            'label'   => Mage::helper('advr')->__('Group By Attribute'),
            'values'  => $values,
        ));

        $categories = Mage::getSingleton('advr/system_config_source_category')->toOptionArray();
        if ($categories) {
            $form->addField('filter_by_category', 'select', array(
                'name'    => 'filter_by_category',
                'label'   => Mage::helper('advr')->__('Filter By Category'),
                'values'  => Mage::getSingleton('advr/system_config_source_category')->toOptionArray(true),
            ));
        } else {  
            $form->addField('filter_by_category', 'text', array(
                'name'    => 'filter_by_category',
                'label'   => Mage::helper('advr')->__('Filter By Category'),
            ));
        }

        return $this;
    }

    public function frameCallbackAttribute($value, $row, $column)
    {
        $attribute = $this->_getAttribute();
        if ($attribute && $attribute->usesSource()) {
            $options = $attribute->getSource()->getAllOptions(false);
            foreach ($options as $opt) {
                if ($opt['value'] == $value) {
                    return $opt['label'];
                }
            }

            return Mage::helper('advr')->__('not set');
        }
        
        return Mage::helper('core/string')->truncate($value, 50);
    }

    protected function _getAttribute()
    {
        $attrCode = $this->getFilterData()->getGroupByAttribute();
        if (!$attrCode) {
            $attrCode = 'status';
        }

        if ($attrCode) {
            $attribute = Mage::getSingleton('eav/config')->getAttribute('catalog_product', $attrCode);

            return $attribute;
        }

        return null;
    }
}