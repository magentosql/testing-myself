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


class Mirasvit_Advr_Block_Adminhtml_Order_Abstract extends Mirasvit_Advr_Block_Adminhtml_Block_Container
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
            'sum_shipping_amount' => array(
                'header'         => 'Shipping',
                'type'           => 'currency',
            ),
            'sum_tax_amount' => array(
                'header'         => 'Tax',
                'type'           => 'currency',
            ),
            'sum_shipping_tax_amount' => array(
                'header'         => 'Shipping Tax',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'sum_total_invoiced' => array(
                'header'         => 'Invoiced',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'sum_total_refunded' => array(
                'header'         => 'Refunded',
                'type'           => 'currency',
            ),
            'sum_grand_total' => array(
                'header'         => 'Grand Total',
                'type'           => 'currency',
                'chart'          => true,
            ),
            'sum_total_invoiced_cost' => array(
                'header'         => 'Invoiced Cost',
                'type'           => 'currency',
                'chart'          => true,
                'hidden'         => true,
            ),
            'sum_gross_profit' => array(
                'header'         => 'Gross Profit',
                'type'           => 'currency',
                'chart'          => true,
                'hidden'         => true,
            ),

            'avg_total_qty_ordered' => array(
                'header'         => 'Average Items Ordered',
                'type'           => 'number',
                'hidden'         => true,
            ),
            'avg_discount_amount' => array(
                'header'         => 'Average Discount',
                'type'           => 'currency',
                'hidden'         => true,
                'frame_callback' => array(Mage::helper('advr/callback'), 'discount'),
                'discount_from'  => 'avg_subtotal',
            ),
            'avg_shipping_amount' => array(
                'header'         => 'Average Shipping',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'avg_tax_amount' => array(
                'header'         => 'Average Tax',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'avg_shipping_tax_amount' => array(
                'header'         => 'Average Shipping Tax',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'avg_total_invoiced' => array(
                'header'         => 'Average Invoiced',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'avg_total_refunded' => array(
                'header'         => 'Average Refunded',
                'type'           => 'currency',
                'hidden'         => true,
            ),
            'avg_grand_total' => array(
                'header'         => 'Average Grand Total',
                'type'           => 'currency',
                'hidden'         => true,
            ),
        );
    }

    public function _initToolbar()
    {
        parent::_initToolbar();

        $form = $this->_toolbar->getForm();

        $form->addField('filter_by_sku', 'text', array(
            'name'    => 'filter_by_sku',
            'label'   => Mage::helper('advr')->__('Filter By SKU'),
            'value'   => $this->getFilterData()->getFilterBySku(),
        ));

        return $this;
    }
}