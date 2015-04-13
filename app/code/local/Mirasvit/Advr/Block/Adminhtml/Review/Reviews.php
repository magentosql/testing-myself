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


class Mirasvit_Advr_Block_Adminhtml_Review_Reviews extends Mirasvit_Advr_Block_Adminhtml_Block_Container
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
            ->setDefaultDir('desc');

        $this->setHeaderText(Mage::helper('advr')->__('Reviews'));

        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $reviews = Mage::getResourceModel('advr/review_collection');
        
        $reviews
            ->setFilterData($filterData)
            ->groupByPeriod()
            ;
        // echo $reviews->getSelect();die();
        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($reviews)
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

            'quantity' => array(
                'header'         => 'Number Of Reviews',
                'type'           => 'number',
                'chart' => true,
            ),
        );

        return $columns;
    }
}