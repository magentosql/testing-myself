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


class Mirasvit_Advr_Block_Adminhtml_Order_Plain extends Mirasvit_Advr_Block_Adminhtml_Block_Container
{
    public function _prepareLayout()
    {
        $this->setChartType('column');

        parent::_prepareLayout();

        $this->getToolbar()
            ->setRangesVisibility(false)
            ->setCompareVisibility(false);

        $this->getGrid()
            ->setDefaultSort('sum_grand_total')
            ->setDefaultDir('desc')
            ->setPagerVisibility(true)
            ->setRowUrlCallback(array($this, 'rowUrlCallback'));

        $this->setHeaderText(Mage::helper('advr')->__('Orders'));

        return $this;
    }

    public function _initGrid()
    {
        parent::_initGrid();

        $this->_grid->addExportType('csv', Mage::helper('advr')->__('CSV'));
        $this->_grid->addExportType('xml', Mage::helper('advr')->__('Excel XML'));

        return $this;
    }

    protected function _initChart()
    {
        return $this;
    }

    public function _prepareCollection($filterData)
    {
        $orders = Mage::getModel('sales/order')->getCollection();

        $orders->addFieldToFilter('created_at', array('gteq' => $filterData->getFromLocal()))
            ->addFieldToFilter('created_at', array('lteq' => $filterData->getToLocal()))
            ;

        if (count($filterData->getStoreIds())) {
            $orders->getSelect()
                ->where('main_table.store_id IN('.implode(',', $filterData->getStoreIds()).')');
        }

        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($orders)
            ->setColumnGroupBy('entity_id');

        return $collection;
    }

    public function afterGridCollectionLoad()
    {
        $totals = $this->getGrid()->getCollection()->getTotals();

        if ($totals && $totals != $this->getGrid()->getTotals()) {
            $this->getGrid()->setTotals($totals);
            $this->getGrid()->setCountTotals(1);
        }

        return $this;
    }

    public function getColumns()
    {
        $columns = array(
            'increment_id' => array(
                'header'       => Mage::helper('advr')->__('Order #'),
                'type'         => 'text',
                'index'        => 'increment_id',
                'totals_label' => Mage::helper('advr')->__('Totals')
            ),

            'invoice_id' => array(
                'header'       => Mage::helper('advr')->__('Invoice #'),
                'type'         => 'text',
                'index'        => 'invoice_id',
                'sortable'     => false,
                'filter'       => false,
                'totals_label' => '',
                'frame_callback' => array($this, 'invoice'),
                'hidden'         => true,
            ),

            'customer_firstname' => array(
                'header'       => Mage::helper('advr')->__('Customer Firstname'),
                'type'         => 'text',
                'index'        => 'customer_firstname',
                'totals_label' => '',
            ),

            'customer_lastname' => array(
                'header'       => Mage::helper('advr')->__('Customer Lastname'),
                'type'         => 'text',
                'index'        => 'customer_lastname',
                'totals_label' => '',
            ),

            'customer_taxvat' => array(
                'header'       => Mage::helper('advr')->__('Tax/VAT number'),
                'type'         => 'text',
                'index'        => 'customer_taxvat',
                'totals_label' => '',
                'hidden'       => true,
            ),

            'created_at' => array(
                'header'            => Mage::helper('advr')->__('Purchased On'),
                'index'             => 'created_at',
                'type'              => 'datetime',
                'column_css_class'  => 'nobr',
                'totals_label'      => '',
            ),

            'state' => array(
                'header'       => Mage::helper('advr')->__('State'),
                'index'        => 'state',
                'type'         => 'options',
                'options'      => Mage::getSingleton('sales/order_config')->getStates(),
                'totals_label' => '',
                'hidden'       => true,
            ),

            'status' => array(
                'header'       => Mage::helper('advr')->__('Status'),
                'index'        => 'status',
                'type'         => 'options',
                'options'      => Mage::getSingleton('sales/order_config')->getStatuses(),
                'totals_label' => '',
            ),

            'products' => array(
                'header'         => Mage::helper('advr')->__('Item(s)'),
                'type'           => 'text',
                'sortable'       => false,
                'filter'         => false,
                'totals_label'   => '',
                'frame_callback' => array($this, 'products'),
                'hidden'         => true,
            ),

            'tracking_number' => array(
                'header'         => Mage::helper('advr')->__('Tracking Number'),
                'type'           => 'text',
                'sortable'       => false,
                'filter'         => false,
                'totals_label'   => '',
                'frame_callback' => array($this, 'trackingNumber'),
                'hidden'         => true,
            ),

            'total_qty_ordered' => array(
                'header'    => Mage::helper('advr')->__('Items Ordered'),
                'type'      => 'number',
                'index'     => 'total_qty_ordered',
            ),

            'tax_amount' => array(
                'header'        => Mage::helper('advr')->__('Tax'),
                'type'          => 'currency',
                'index'         => 'tax_amount',
            ),

            'shipping_amount' => array(
                'header'        => Mage::helper('advr')->__('Shipping'),
                'type'          => 'currency',
                'index'         => 'shipping_amount',
            ),

            'discount_amount' => array(
                'header'        => Mage::helper('advr')->__('Discount'),
                'type'          => 'currency',
                'index'         => 'discount_amount',
            ),

            'total_refunded' => array(
                'header'        => Mage::helper('advr')->__('Refunded'),
                'type'          => 'currency',
                'index'         => 'total_refunded',
            ),

            'total_paid' => array(
                'header'        => Mage::helper('advr')->__('Paid'),
                'type'          => 'currency',
                'index'         => 'total_paid',
                'hidden'        => true,
            ),

            'grand_total' => array(
                'header'        => Mage::helper('advr')->__('Grand Total'),
                'type'          => 'currency',
                'index'         => 'grand_total',
            ),
        );

        return $columns;
    }

    public function rowUrlCallback($row)
    {
        return $this->getUrl('adminhtml/sales_order/view', array('order_id' => $row->getEntityId()));
    }

    public function invoice($value, $row, $column)
    {
        $data = array();
        $collection = $row->getInvoiceCollection();
        foreach ($collection as $invoice) {
            $data[] = $invoice->getIncrementId();
        }
        return implode(' ', $data);
    }

    public function products($value, $row, $column)
    {
        $collection = $row->getAllVisibleItems();
        foreach ($collection as $item) {
            $data[] = '<a href="'.$this->getUrl('adminhtml/catalog_product/edit', array('id' => $item->getProductId())).'">'
                .$item->getProduct()->getName()
                .'</a>';
        }

        return implode('<br>', $data);
    }

    public function trackingNumber($value, $row, $column)
    {
        $trackNumbers = array();

        $shipmentCollection = Mage::getResourceModel('sales/order_shipment_collection')
            ->setOrderFilter($row);

        foreach ($shipmentCollection as $shipment){
            foreach($shipment->getAllTracks() as $trackNumber) {
                $trackNumbers[] = $trackNumber->getNumber();
            }
        }
        
        return implode('<br>', $trackNumbers);
    }
}