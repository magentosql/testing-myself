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


class Mirasvit_Advr_Block_Adminhtml_Product_Abstract extends Mirasvit_Advr_Block_Adminhtml_Block_Container
{
    public function getTotals()
    {
        return $this->getCollection()->getResourceCollection()->getTotals();
    }

    public function _initGrid()
    {
        parent::_initGrid();

        $this->_grid->addExportType('csv', Mage::helper('advr')->__('CSV'));
        $this->_grid->addExportType('xml', Mage::helper('advr')->__('Excel XML'));

        return $this;
    }

    public function getBaseColumns()
    {
        return array(
            'quantity' => array(
                'header'         => 'Number Of Orders',
                'type'           => 'number',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false
            ),
            'sum_qty_ordered' => array(
                'header'         => 'QTY Ordered',
                'type'           => 'number',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false
            ),
            'sum_qty_refunded' => array(
                'header'         => 'QTY Refunded',
                'type'           => 'number',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false
            ),
            'sum_tax_amount' => array(
                'header'         => 'Tax',
                'type'           => 'currency',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false
            ),
            'sum_discount_amount' => array(
                'header'         => 'Discount',
                'type'           => 'currency',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false,
                'frame_callback' => array(Mage::helper('advr/callback'), 'discount'),
                'discount_from'  => 'sum_row_total',
            ),
            'sum_amount_refunded' => array(
                'header'         => 'Refunded',
                'type'           => 'currency',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => false
            ),
            'sum_row_total' => array(
                'header'         => 'Total',
                'type'           => 'currency',
                'sortable'       => true,
                'filter'         => false,
                'chart'          => true
            ),
        );
    }
}