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


class Mirasvit_Advr_Block_Adminhtml_Order_Coupon extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
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
            ->setXAxisField('coupon_code');

        $this->getGrid()
            ->setDefaultSort('sum_grand_total')
            ->setDefaultDir('desc')
            ->setDefaultLimit(1000)
            ->setPagerVisibility(false);

        $this->setHeaderText(Mage::helper('advr')->__('Sales By Coupon'));

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
            ->groupByCouponCode()
            ;


        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($orders)
            ->setColumnGroupBy('coupon_code');

        return $collection;
    }

    public function getColumns()
    {
        $columns = array(
            'coupon_code' => array(
                'header'         => 'Coupon Code',
                'type'           => 'text',
                'totals_label'   => 'Total',
                'chart'          => true,
            ),
        );

        $columns += $this->getBaseColumns();

        return $columns;
    }
}