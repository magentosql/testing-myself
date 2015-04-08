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


class Mirasvit_Advr_Block_Adminhtml_Order_Orders extends Mirasvit_Advr_Block_Adminhtml_Order_Abstract
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
            ->setPagerVisibility(false)
            ->setRowUrlCallback(array($this, 'rowUrlCallback'));

        $this->setHeaderText(Mage::helper('advr')->__('Sales'));

        return $this;
    }

    protected function _prepareCollection($filterData)
    {
        $orders = Mage::getResourceModel('advr/order_collection');
        
        if ($filterData->getFilterBySku()) {
            $orders = Mage::getResourceModel('advr/order_item_collection');
            $orders->addFilterBySku($filterData->getFilterBySku());
        }

        $orders
            ->setFilterData($filterData)
            ->groupByPeriod()
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
                'header'                => 'Period',
                'type'                  => 'text',
                'frame_callback'        => array(Mage::helper('advr/callback'), 'period'),
                'totals_label'          => 'Total',
                'filter_totals_label'   => 'Subtotal',
                'grouped'               => true,
                'filter'                => false,
            ),
        );

        $columns += $this->getBaseColumns();

        return $columns;
    }

    public function rowUrlCallback($row)
    {
        $period = explode('|', $row->getPeriod());
        $periodFrom = strtotime($period[0]);
        $periodTo = $periodFrom;

        switch ($this->getFilterData()->getRange()) {
            case '1w':
                $periodTo += 7 * 24 * 60 * 60;
                break;

            case '1m':
                $periodTo += 30 * 24 * 60 * 60;
                break;

            case '1q':
                $periodTo += 80 * 24 * 60 * 60;
                break;

            case '1y':
                $periodTo += 365 * 24 * 60 * 60;
        }

        $format = Mage::getSingleton('advr/config')->dateFormat();

        $from = new Zend_Date($periodFrom, null, Mage::app()->getLocale()->getLocaleCode());
        $to   = new Zend_Date($periodTo, null, Mage::app()->getLocale()->getLocaleCode());

        $filter = array(
            'from' => $from->toString($format),
            'to'   => $to->toString($format)
        );

        $filter = base64_encode(http_build_query($filter));

        return $this->getUrl('*/*/plain', array('filter' => $filter));
    }
}