<?php

class Wsnyc_Groundonly_Model_Rewrite_Sales_Quote_Address
    extends Wsnyc_Oversizeshipping_Model_Rewrite_Sales_Quote_Address
{
    /**
     * if cart has ground only items, display only ground shipping methods
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return bool
     */
    public function requestShippingRates(Mage_Sales_Model_Quote_Item_Abstract $item = null)
    {
        $found = parent::requestShippingRates($item);

        Mage::getModel('groundonly/groundonly')->updateShippingMethods($this);

        return $found;
    }

}