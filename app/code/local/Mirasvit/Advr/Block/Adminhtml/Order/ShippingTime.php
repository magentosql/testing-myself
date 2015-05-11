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


class Mirasvit_Advr_Block_Adminhtml_Order_ShippingTime
    extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        $this->setChartType('column');

        parent::_prepareLayout();

        $this->getToolbar()
            ->setRangesVisibility(true)
            ->setCompareVisibility(false);

        $this->getGrid()
            ->setDefaultSort('period')
            ->setDefaultDir('asc')
            ->setDefaultLimit(100000)
            ->setPagerVisibility(false)
            ->setFilterVisibility(false);

        $this->setHeaderText(Mage::helper('advr')->__('Average Shipping Time'));

        return $this;
    }

    protected function _initChart()
    {
        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $orders = Mage::getResourceModel('advr/order_collection');

        $orders
            ->setFilterData($filterData)
            ->groupByPeriod()
            ->joinShippingTime()
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
                'filter'         => false,
                'sortable'       => false,
            ),

            'avg_shipping_time' => array(
                'header'         => 'Average Shipping Time',
                'type'           => 'number',
                'index'          => 'avg_shipping_time',
                'totals_label'   => '',
                'sortable'       => false,
                'frame_callback' => array(Mage::helper('advr/callback'), 'time'),
            ),

            'sum_time_0_1' => array(
                'header'         => 'Number of orders (< 1 hour)',
                'type'           => 'number',
                'index'          => 'sum_time_0_1',
                'sortable'       => false,
                'frame_callback' => array(Mage::helper('advr/callback'), 'percentOf'),
                'percent_of'     => 'quantity'
            ),
            
            'sum_time_1_24' => array(
                'header'         => '1 - 24 hours',
                'type'           => 'number',
                'index'          => 'sum_time_1_24',
                'sortable'       => false,
                'frame_callback' => array(Mage::helper('advr/callback'), 'percentOf'),
                'percent_of'     => 'quantity'
            ),
            
            'sum_time_24_48' => array(
                'header'         => '24 - 48 hours',
                'type'           => 'number',
                'index'          => 'sum_time_24_48',
                'sortable'       => false,
                'frame_callback' => array(Mage::helper('advr/callback'), 'percentOf'),
                'percent_of'     => 'quantity'
            ),
            
            'sum_time_48_' => array(
                'header'         => '48 - 72 hours',
                'type'           => 'number',
                'index'          => 'sum_time_48_72',
                'sortable'       => false,
                'frame_callback' => array(Mage::helper('advr/callback'), 'percentOf'),
                'percent_of'     => 'quantity'
            ),
            
            'sum_time_72_' => array(
                'header'         => '> 72 hours',
                'type'           => 'number',
                'index'          => 'sum_time_72_',
                'sortable'       => false,
                'frame_callback' => array(Mage::helper('advr/callback'), 'percentOf'),
                'percent_of'     => 'quantity'
            ),

            'quantity' => array(
                'header'         => 'Number of Orders',
                'type'           => 'number',
                'index'          => 'quantity',
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