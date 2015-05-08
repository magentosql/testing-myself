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


class Mirasvit_Advr_Block_Adminhtml_Product_Detail
    extends Mirasvit_Advr_Block_Adminhtml_Block_Container
{
    public function _prepareLayout()
    {
        $this->setChartType('column');

        parent::_prepareLayout();

        $this->getToolbar()
            ->setRangesVisibility(true)
            ->setCompareVisibility(false);

        $this->getChart()
            ->setXAxisType('datetime')
            ->setXAxisField('period');

        $this->getGrid()
            ->setDefaultSort('period')
            ->setDefaultDir('asc')
            ->setDefaultLimit(100000)
            ->setPagerVisibility(false);

        $this->setHeaderText(Mage::helper('advr')->__('Sales Report for "%s"',
            Mage::registry('current_product')->getName()));

        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $products = Mage::getResourceModel('advr/order_item_collection')
            ->setFilterData($filterData)
            ->groupByPeriod()
            ;

        $products->addFilterByProductId(Mage::registry('current_product')->getId());
        // echo $products->getSelect();die();
        $collection = Mage::getModel('advr/collection');

        Mage::helper('advr/collection')->preparePeriodCollection(
            $collection,
            $filterData->getFrom(),
            $filterData->getTo(),
            $filterData->getRange()
        );

        $collection->setResourceCollection($products)
            ->setColumnGroupBy('period');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'period' => array(
                'header'                => 'Period',
                'type'                  => 'text',
                'frame_callback'        => array(Mage::helper('advr/callback'), 'period'),
                'totals_label'          => 'Total',
                'filter_totals_label'   => 'Subtotal',
                'grouped'               => true,
                'filter'                => false,
            ),

            'sum_total_qty_ordered' => array(
                'header'         => 'Items Ordered',
                'type'           => 'number',
            ),
            'sum_discount_amount' => array(
                'header'         => 'Discount',
                'type'           => 'currency',
                'frame_callback' => array(Mage::helper('advr/callback'), 'discount'),
                'discount_from'  => 'sum_subtotal',
            ),
            'sum_tax_amount' => array(
                'header'         => 'Tax',
                'type'           => 'currency',
            ),
            'sum_row_invoiced' => array(
                'header'         => 'Invoiced',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'sum_amount_refunded' => array(
                'header'         => 'Refunded',
                'type'           => 'currency',
            ),
            'sum_grand_total' => array(
                'header'         => 'Grand Total',
                'type'           => 'currency',
                'chart'          => true,
            ),

            'avg_total_qty_ordered' => array(
                'header'         => 'Average Items Ordered',
                'type'           => 'number',
                'hidden'         => true,
            ),
            'avg_discount_amount' => array(
                'header'         => 'Average Discount',
                'type'           => 'currency',
                'frame_callback' => array(Mage::helper('advr/callback'), 'discount'),
                'discount_from'  => 'avg_subtotal',
                'hidden'         => true,
            ),
            'avg_tax_amount' => array(
                'header'         => 'Average Tax',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'avg_row_invoiced' => array(
                'header'         => 'Average Invoiced',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'avg_amount_refunded' => array(
                'header'         => 'Average Refunded',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'avg_grand_total' => array(
                'header'         => 'Average Grand Total',
                'type'           => 'currency',
                'chart'          => true,
                'hidden'         => true,
            ),
        );

        return $columns;
    }

    public function getTotals()
    {
        return $this->getCollection()->getResourceCollection()->getTotals();
    }
}