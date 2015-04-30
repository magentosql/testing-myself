<?php

class Wsnyc_OrdersReport_Block_Adminhtml_Sales_Order_Grid_Renderer_Address2 extends Wsnyc_OrdersReport_Block_Adminhtml_Sales_Order_Grid_Renderer_Address1
{
    protected function _getValue(Varien_Object $row)
    {
        return $this->getStreet(2, $row['street']);
    }

}