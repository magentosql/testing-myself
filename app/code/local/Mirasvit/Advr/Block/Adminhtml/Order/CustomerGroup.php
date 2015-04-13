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


class Mirasvit_Advr_Block_Adminhtml_Order_CustomerGroup
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
            ->setNameField('customer_group_code')
            ->setValueField('quantity');

        $this->getGrid()
            ->setDefaultSort('quantity')
            ->setDefaultDir('desc')
            ->setDefaultLimit(1000)
            ->setPagerVisibility(false);

        $this->setHeaderText(Mage::helper('advr')->__('Sales By Customer Group'));

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
            ->groupByCustomerGroup()
            ;

        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($orders)
            ->setColumnGroupBy('customer_group_code');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'customer_group_code' => array(
                'header'               => 'Customer Group',
                'type'                 => 'text',
                'index'                => 'customer_group_code',
                'totals_label'         => 'Total',
                'filter_totals_label'  => 'Subtotal',
                'chart'                => true,
                'filter'               => false,
            ),

            'percent' => array(
                'header'          => 'Number Of Orders, %',
                'type'            => 'percent',
                'index'           => 'quantity',
                'frame_callback'  => array(Mage::helper('advr/callback'), 'percent'),
                'filter'          => false,
                'export_callback' => array(Mage::helper('advr/callback'), '_percent'),
            ),
        );

        $columns += $this->getBaseColumns();

        return $columns;
    }
}