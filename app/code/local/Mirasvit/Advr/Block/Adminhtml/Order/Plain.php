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
                ->where('main_table.store_id IN('.implode(',', $filterData->getStoreIds()).')')
                ;
        }

        $collection = Mage::getModel('advr/collection');

        $collection->setResourceCollection($orders)
            ->setColumnGroupBy('entity_id');

        return $collection;
    }

    public function getColumns()
    {

        $groups = Mage::getResourceModel('customer/group_collection')
            ->addFieldToFilter('customer_group_id', array('gt'=> 0))
            ->load()
            ->toOptionHash();

        $columns = array(
            'increment_id' => array(
                'header'       => Mage::helper('advr')->__('Order #'),
                'totals_label' => Mage::helper('advr')->__('Totals')
            ),

            'invoice_id' => array(
                'header'          => Mage::helper('advr')->__('Invoice #'),
                'sortable'        => false,
                'filter'          => false,
                'frame_callback'  => array($this, 'invoice'),
                'export_callback' => array($this, 'invoice'),
                'hidden'          => true,
            ),

            'customer_firstname' => array(
                'header'            => Mage::helper('advr')->__('Firstname'),
                'column_css_class'  => 'nobr',
            ),

            'customer_lastname' => array(
                'header'            => Mage::helper('advr')->__('Lastname'),
                'column_css_class'  => 'nobr',
            ),

            'customer_email' => array(
                'header'            => Mage::helper('advr')->__('Email'),
                'column_css_class'  => 'nobr',
            ),

            'customer_group_id' => array(
                'header'            => Mage::helper('advr')->__('Customer Group'),
                'type'              => 'options',
                'options'           =>  $groups,
                'column_css_class'  => 'nobr',
            ),
            
            'customer_taxvat' => array(
                'header'       => Mage::helper('advr')->__('Tax/VAT number'),
                'hidden'       => true,
            ),

            'created_at' => array(
                'header'            => Mage::helper('advr')->__('Purchased On'),
                'type'              => 'datetime',
                'column_css_class'  => 'nobr',
            ),

            'state' => array(
                'header'       => Mage::helper('advr')->__('State'),
                'type'         => 'options',
                'options'      => Mage::getSingleton('sales/order_config')->getStates(),
                'hidden'       => true,
            ),

            'status' => array(
                'header'       => Mage::helper('advr')->__('Status'),
                'type'         => 'options',
                'options'      => Mage::getSingleton('sales/order_config')->getStatuses(),
            ),

            'products' => array(
                'header'          => Mage::helper('advr')->__('Item(s)'),
                'sortable'        => false,
                'filter'          => false,
                'frame_callback'  => array($this, 'products'),
                'export_callback' => array($this, 'products'),
                'hidden'          => true,
            ),

            'tracking_number' => array(
                'header'          => Mage::helper('advr')->__('Tracking Number'),
                'sortable'        => false,
                'filter'          => false,
                'frame_callback'  => array($this, 'trackingNumber'),
                'export_callback' => array($this, 'trackingNumber'),
                'hidden'          => true,
            ),

            'total_qty_ordered' => array(
                'header'        => Mage::helper('advr')->__('Quantity Ordered'),
                'type'          => 'number',
            ),

            'tax_amount' => array(
                'header'        => Mage::helper('advr')->__('Tax'),
                'type'          => 'currency',
                'hidden'        => true,
            ),

            'shipping_amount' => array(
                'header'        => Mage::helper('advr')->__('Shipping'),
                'type'          => 'currency',
                'hidden'        => true,
            ),

            'discount_amount' => array(
                'header'        => Mage::helper('advr')->__('Discount'),
                'type'          => 'currency',
            ),

            'total_refunded' => array(
                'header'        => Mage::helper('advr')->__('Refunded'),
                'type'          => 'currency',
            ),

            'total_paid' => array(
                'header'        => Mage::helper('advr')->__('Paid'),
                'type'          => 'currency',
                'hidden'        => true,
            ),

            'base_total_invoiced' => array(
                'header'        => Mage::helper('advr')->__('Total Invoiced'),
                'type'          => 'currency',
                'hidden'        => true,
            ),

            'grand_total' => array(
                'header'        => Mage::helper('advr')->__('Grand Total'),
                'type'          => 'currency',
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
            $data[] = '<a class="nobr" href="'.$this->getUrl('adminhtml/catalog_product/edit', array('id' => $item->getProductId())).'">'
                    .$item->getSku()
                    .' / '
                    .Mage::helper('core/string')->truncate($item->getName(), 50)
                    .' / '.intval($item->getQtyOrdered()).' × '.Mage::helper('core')->currency($item->getBasePrice())
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