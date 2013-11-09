<?php

class Wsnyc_Groundonly_Model_Groundonly
{
    protected $_address;

    public function updateShippingMethods(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        if($this->_cartHasGroundOnlyItems()) {
            $this->_hideNonGroundOnlyShippingMethods();
        }
    }

    protected function _cartHasGroundOnlyItems()
    {
        $groundOnly = false;
        $resource = Mage::getResourceModel('catalog/product');
        foreach($this->_address->getAllItems() as $item) {
            try {
                $value = $resource->getAttributeRawValue($item->getProduct()->getId(), 'ground_only', $item->getStoreId());
                if((int)$value) {
                    $groundOnly = true;
                    break;
                }
            } catch( Exception $e ) {}
        }
        return $groundOnly;
    }

    protected function _hideNonGroundOnlyShippingMethods()
    {
        $avialableShippingMethods = $this->_getAvailableShippingMethods();

        foreach($this->_address->getAllShippingRates() as $rate) {
            if(!in_array($rate->getCode(), $avialableShippingMethods)) {
                $rate->isDeleted(true);
            }
        }
    }

    protected function _getAvailableShippingMethods()
    {
        $shippingMethods = Mage::getConfig()->getXpath('global/wsnyc_groundonly/allowed_shipping_methods');
        if(!$shippingMethods) {
            $shippingMethods = array();
        } else {
            $shippingMethods = current($shippingMethods)->asArray();
        }
        return $shippingMethods;
    }
}