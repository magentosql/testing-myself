<?php

class Wsnyc_OrdersReport_Block_Adminhtml_Sales_Order_Grid_Renderer_Address1 extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Number
{
    protected function _getValue(Varien_Object $row)
    {
        return $this->getStreet(1, $row['street']);
    }

    /**
     * get address street
     *
     * @param   int $line address line index
     * @return  string
     */
    public function getStreet($line=0, $street)
    {
        if (-1 === $line) {
            return $street;
        } else {
            $arr = is_array($street) ? $street : explode("\n", $street);
            if (0 === $line || $line === null) {
                return $arr;
            } elseif (isset($arr[$line-1])) {
                return $arr[$line-1];
            } else {
                return '';
            }
        }
    }
}