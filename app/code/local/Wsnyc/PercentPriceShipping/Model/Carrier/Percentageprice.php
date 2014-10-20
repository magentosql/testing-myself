<?php

class Wsnyc_PercentPriceShipping_Model_Carrier_Percentageprice extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'percentageprice';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        $result = Mage::getModel('shipping/rate_result');
        if(Mage::helper('percentpriceshipping')->checkIfShippingMethodIsAllowed()) {
            $method = Mage::getModel('shipping/rate_result_method')->setCarrier($this->_code)
                        ->setCarrierTitle($this->getConfigData('title'))
                        ->setMethod($this->_code)
                        ->setMethodTitle($this->getConfigData('name'))
                        ->setPrice(Mage::getModel('checkout/session')->getQuote()->getSubtotal() * Mage::getStoreConfig('carriers/percentageprice/price') / 100)
                        ->setCost('0.00');
            $result->append($method);
        }
        return $result;
    }

    public function getAllowedMethods() {
        if(Mage::helper('percentpriceshipping')->checkIfShippingMethodIsAllowed()) {
            return array($this->_code => $this->getConfigData('name'));
        }
    }

}
