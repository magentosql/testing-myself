<?php

class Wsnyc_NoShippingNeeded_Model_Carrier_Noshippingneeded extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'noshippingneeded';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        $result = Mage::getModel('shipping/rate_result');
        if(Mage::helper('noshippingneeded')->checkIfShippingMethodStepShouldBeIgnored()) {
            $method = Mage::getModel('shipping/rate_result_method');
            $method->setCarrier($this->_code);
            $method->setCarrierTitle($this->getConfigData('title'));
            $method->setMethod($this->_code);
            $method->setMethodTitle($this->getConfigData('name'));
            $method->setPrice('0.00');
            $method->setCost('0.00');
            $result->append($method);
        }
        return $result;
    }

    public function getAllowedMethods() {
        if(false) {
            return array($this->_code => $this->getConfigData('name'));
        } else {
            return array();
        }        
    }

}
