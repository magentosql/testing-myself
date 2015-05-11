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


class Mirasvit_Advr_Block_Adminhtml_Product_Lowstock
    extends Mirasvit_Advr_Block_Adminhtml_Product_Abstract
{
    public function _prepareLayout()
    {
        $this->setChartType('column');

        parent::_prepareLayout();

        $this->getChart()
            ->setXAxisType('category')
            ->setXAxisField('product_name');

        $this->getGrid()
            ->setDefaultSort('qty')
            ->setDefaultDir('asc')
            ->setRowUrlCallback(array($this, 'rowUrlCallback'));

        $this->getToolbar()
            ->setRangesVisibility(false)
            ->setIntervalsVisibility(false);

        $this->setHeaderText(Mage::helper('advr')->__('Low stock'));

        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $products = Mage::getResourceModel('advr/product_collection');

        $products->setFilterData($filterData);

        if ($this->getFilterData()->getGroupByParent()) {
            $products
                ->joinParentProduct(array('entity_id', 'sku'))
                ->joinStockItem(array('qty'))
                ->joinProductName()
                ->groupByParentProduct();
        } else {
            $products
                ->joinProduct(array('entity_id', 'sku'))
                ->joinStockItem(array('qty'))
                ->joinProductName()
                ->groupByProduct();
        }

        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($products)
            ->setColumnGroupBy('entity_id');
            
        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'sku' => array(
                'header'              => 'SKU',
                'type'                => 'text',
                'totals_label'        => 'Total',
                'filter_totals_label' => 'Subtotal',
                'filter_index'        => 'main_table.sku',
                'sortable'            => true,
            ),

            'product_name' => array(
                'header'              => 'Product',
                'type'                => 'text',
                'filter_index'        => 'product_name.value',
                'totals_label'        => '',
                'sortable'            => true,
            ),
            'qty' => array(
                'header'              => 'Quantity',
                'type'                => 'number',
                'sortable'            => true,
                'chart'               => true,
            ),
            
        );

        return $columns;
    }

    public function _initToolbar()
    {
        parent::_initToolbar();

        $form = $this->_toolbar->getForm();

        $form->addField('group_by_parent', 'checkbox', array(
            'name'    => 'group_by_parent',
            'label'   => Mage::helper('advr')->__('Group By Parent Product'),
            'value'   => 1,
            'checked' => $this->getFilterData()->getGroupByParent(),
        ));

        return $this;
    }

    public function rowUrlCallback($row)
    {
        return $this->getUrl('adminhtml/catalog_product/edit', array('id' => $row->getData('entity_id')));
    }
}