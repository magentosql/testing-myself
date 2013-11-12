<?php

class Wsnyc_Groundonly_Model_Observer
    extends Varien_Event_Observer
{

    protected $_groundedVendors = array();
    protected $_availableShippingMethods = null;

    public function updateShippingMethods($observer)
    {
        $event = $observer->getEvent();
        if($this->_getAvailableShippingMethods()
            && $this->_isVendorGrounded($event)
        ) {
            $this->_skipMethodIfNotAllowed($event);
        }
    }

    protected function _getAvailableShippingMethods()
    {
        if(null === $this->_availableShippingMethods) {
            $shippingMethods = Mage::getConfig()->getXpath('global/wsnyc_groundonly/allowed_shipping_methods');
            if(!$shippingMethods) {
                $this->_availableShippingMethods = array();
            } else {
                $this->_availableShippingMethods = current($shippingMethods)->asArray();
            }
        }
        return $this->_availableShippingMethods;
    }

    protected function _isVendorGrounded($event) {
        $vendor = $event->getVendor()->getId();
        if(!isset($this->_groundedVendors[$vendor])) {
            $this->_groundedVendors[$vendor] = false;
            if($this->_hasRequestGroundOnlyItems($event->getRequest()->getAllItems())) {
                $this->_groundedVendors[$vendor] = true;
            }
        }
        return $this->_groundedVendors[$vendor];
    }

    protected function _hasRequestGroundOnlyItems($items)
    {
        $groundOnly = false;
        $resource = Mage::getResourceModel('catalog/product');
        foreach($items as $item) {
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

    protected function _skipMethodIfNotAllowed($event)
    {
        $rate = $event->getRate();
        if(!in_array($rate->getCarrier() . '_' . $rate->getMethod(), $this->_getAvailableShippingMethods())) {
            $rate->setUdsIsSkip(true);
        }
    }
}