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


class Mirasvit_Advr_Block_Adminhtml_Order_Product extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
{
    public function _prepareLayout()
    {
        $this->setChartType('column');

        parent::_prepareLayout();

        $this->getToolbar()
            ->setRangesVisibility(true)
            ->setCompareVisibility(true);

        $this->getChart()
            ->setXAxisType('datetime')
            ->setXAxisField('period');

        $this->getGrid()
            ->setDefaultSort('period')
            ->setDefaultDir('asc')
            ->setDefaultLimit(100000)
            ->setPagerVisibility(false);

        $this->setHeaderText(Mage::helper('advr')->__('Product Sales'));

        return $this;
    }

    protected function _prepareCollection($filterData)
    {
        $orderItems = Mage::getResourceModel('advr/order_item_collection');
        $orderItems->addDefaultColumns()
            ->setFilterData($filterData)
            ->groupBy('period')
            ->joinProduct();

        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($orderItems)
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

            'sku' => array(
                'header'                => 'SKU',
                'type'                  => 'text',
                'totals_label'          => 'Total',
                'filter_totals_label'   => 'Subtotal',
                // 'grouped'               => true,
                'filter'                => false,
            ),
        );

        $columns += $this->getBaseColumns();

        return $columns;
    }
}