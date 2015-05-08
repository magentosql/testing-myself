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


class Mirasvit_Advr_Block_Adminhtml_Order_Category
    extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        $this->setChartType('pie');

        parent::_prepareLayout();

        $this->getToolbar()
            ->setRangesVisibility(false)
            ->setCompareVisibility(false);

        $this->getChart()
            ->setNameField('name')
            ->setValueField('sum_row_total');

        $this->getGrid()
            ->setPagerVisibility(true);

        $this->setHeaderText(Mage::helper('advr')->__('Sales By Category'));

        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $categories = Mage::getResourceModel('advr/category_collection');
        $categories->setFilterData($this->getFilterData())
            ->joinCategory(array('path', 'position', 'level', 'entity_id'))
            ->joinOrderItem()
            ->joinAttribute('name')
            ->groupByCategory();

        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($categories)
            ->setColumnGroupBy('entity_id');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'level' => array(
                'header'           => 'Level',
                'type'             => 'number',
                'sortable'         => false,
            ),

            'name' => array(
                'header'              => 'Category',
                'type'                => 'text',
                'frame_callback'      => array(Mage::helper('advr/callback'), 'category'),
                'chart'               => true,
                'sortable'            => false,
            ),

            'sum_qty_ordered' => array(
                'header'         => 'QTY Ordered',
                'type'           => 'number',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false,
                'sortable'       => false,
            ),
            'sum_qty_refunded' => array(
                'header'         => 'QTY Refunded',
                'type'           => 'number',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false,
                'sortable'       => false,
            ),
            'sum_tax_amount' => array(
                'header'         => 'Tax',
                'type'           => 'currency',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false,
                'sortable'       => false,
            ),
            'sum_discount_amount' => array(
                'header'         => 'Discount',
                'type'           => 'currency',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false,
                'frame_callback' => array(Mage::helper('advr/callback'), 'discount'),
                'discount_from'  => 'sum_row_total',
                'sortable'       => false,
            ),
            'sum_amount_refunded' => array(
                'header'         => 'Refunded',
                'type'           => 'currency',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false,
                'sortable'       => false,
            ),
            'sum_row_total' => array(
                'header'         => 'Total',
                'type'           => 'currency',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => true,
                'sortable'       => false,
            ),
        );

        return $columns;
    }

    public function getTotals()
    {
        return false;
    }
}