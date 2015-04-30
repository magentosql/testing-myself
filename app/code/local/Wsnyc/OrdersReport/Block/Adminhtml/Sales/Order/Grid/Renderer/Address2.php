<?php

class Wsnyc_OrdersReport_Block_Adminhtml_Sales_Order_Grid_Renderer_Address2 extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    protected function _getValue(Varien_Object $row)
    {
        $street = explode("\n", $row['street']);
        return $street[1];
    }

}