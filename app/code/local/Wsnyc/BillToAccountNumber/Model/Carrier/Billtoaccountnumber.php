<?php

class Wsnyc_BillToAccountNumber_Model_Carrier_Billtoaccountnumber extends Mage_Shipping_Model_Carrier_Abstract implements Mage_Shipping_Model_Carrier_Interface {

    protected $_code = 'billtoaccountnumber';

    public function collectRates(Mage_Shipping_Model_Rate_Request $request) {
        $result = Mage::getModel('shipping/rate_result');
        if(Mage::helper('billtoaccountnumber')->checkIfShippingMethodIsAllowed()) {
            $method = Mage::getModel('shipping/rate_result_method')
                ->setCarrier($this->_code)
                ->setCarrierTitle($this->getConfigData('title'))
                ->setMethod($this->_code)
                ->setMethodTitle($this->getConfigData('name'))
                ->setPrice('0.00')
                ->setCost('0.00');
            $result->append($method);
        }
        return $result;
    }

    public function getAllowedMethods() {
        if(Mage::helper('billtoaccountnumber')->checkIfShippingMethodIsAllowed()) {
            return array($this->_code => $this->getConfigData('name'));
        }
    }

}
