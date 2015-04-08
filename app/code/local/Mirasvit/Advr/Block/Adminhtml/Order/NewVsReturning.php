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


class Mirasvit_Advr_Block_Adminhtml_Order_NewVsReturning
    extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
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
            ->setPagerVisibility(false)
            ->setFilterVisibility(false);

        $this->setHeaderText(Mage::helper('advr')->__('New vs Returning Customers'));

        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $orders = Mage::getResourceModel('advr/order_collection');

        if ($filterData->getFilterBySku()) {
            $orders = Mage::getResourceModel('advr/order_item_collection');
            $orders->addFilterBySku($filterData->getFilterBySku());
        }

        $orders
            ->setFilterData($filterData)
            ->groupByPeriod()
            ->joinCustomerType()
            ;

        $collection = Mage::getModel('advr/collection');

        Mage::helper('advr/collection')->preparePeriodCollection(
            $collection,
            $filterData->getFrom(),
            $filterData->getTo(),
            $filterData->getRange()
        );

        $collection->setResourceCollection($orders)
            ->setColumnGroupBy('period');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'period' => array(
                'header'         => 'Period',
                'type'           => 'text',
                'index'          => 'period',
                'frame_callback' => array(Mage::helper('advr/callback'), 'period'),
                'totals_label'   => 'Total',
                'grouped'        => true,
                'chart'          => true,
                'filter'         => false,
                'sortable'       => false,
            ),

            'is_new' => array(
                'header'         => 'Orders by new customers',
                'type'           => 'number',
                'index'          => 'is_new',
                'chart'          => true,
                'sortable'       => false,
            ),

            'sum_grand_total_new' => array(
                'header'         => 'Grand Total by new customers',
                'type'           => 'number',
                'index'          => 'sum_grand_total_new',
                'type'           => 'currency',
                'chart'          => true,
                'sortable'       => false,
            ),

            'is_returning' => array(
                'header'         => 'Orders by returning customers',
                'type'           => 'number',
                'index'          => 'is_returning',
                'chart'          => true,
                'sortable'       => false,
            ),

            'sum_grand_total_returning' => array(
                'header'         => 'Grand Total by returning customers',
                'type'           => 'number',
                'index'          => 'sum_grand_total_returning',
                'type'           => 'currency',
                'chart'          => true,
                'sortable'       => false,
            ),

            'percent_new' => array(
                'header'         => 'Percent of new',
                'type'           => 'percent',
                'index'          => 'is_new',
                'frame_callback' => array($this, 'percent'),
                'filter'         => false,
                'sortable'       => false,
            ),

            'percent_returning' => array(
                'header'         => 'Percent of returning',
                'type'           => 'percent',
                'index'          => 'is_returning',
                'frame_callback' => array($this, 'percent'),
                'filter'         => false,
                'sortable'       => false,
            ),
        );

        return $columns;
    }

    public function percent($value, $row, $column)
    {
        $a = $row->getData('is_new');
        $b = $row->getData('is_returning');

        if ($b > 0) {
            $result = $a / ($a + $b);
        } else {
            $result = 1;
        }

        if ($b == $value) {
            $result = 1 - $result;
        }

        return sprintf("%.1f %%", $result * 100);
    }
}