<?php

class Wsnyc_Oversizeshipping_Model_Rewrite_Sales_Quote_Address
    extends Unirgy_DropshipSplit_Model_Quote_Address
{
    /**
     * replace method prices to oversize price * items * item qty
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return bool
     */
    public function requestShippingRates(Mage_Sales_Model_Quote_Item_Abstract $item = null)
    {
        $found = parent::requestShippingRates($item);

        Mage::getModel('oversizeshipping/oversizeshipping')->updateShippingMethods($this);

        return $found;
    }

}