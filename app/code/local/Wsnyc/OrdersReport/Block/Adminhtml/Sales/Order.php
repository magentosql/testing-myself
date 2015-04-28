<?php

class Wsnyc_OrdersReport_Block_Adminhtml_Sales_Order extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_controller = 'sales_order';
        $this->_headerText = Mage::helper('sales')->__('Sample Requests');
        $this->_blockGroup = 'wsnyc_ordersreport';
        $this->_controller = 'adminhtml_sales_order';
        parent::__construct();

        $this->_removeButton('add');
    }
}