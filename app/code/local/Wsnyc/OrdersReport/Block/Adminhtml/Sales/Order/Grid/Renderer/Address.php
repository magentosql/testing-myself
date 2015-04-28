<?php

class Wsnyc_OrdersReport_Block_Adminhtml_Sales_Order_Grid_Renderer_Address extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    protected function _getValue(Varien_Object $row)
    {
        $sep = "<br>";
        return $row['street'] . $sep . $row['city'] . $sep . $row['region'] . $sep . $row['postcode'] . $sep . $row['country'];
    }

}