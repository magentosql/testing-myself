<?php

class Wsnyc_PaymentGroupLimit_Model_Observer {

    public function onPaymentMethodIsActive(Varien_Event_Observer $observer) {
        if($observer->getQuote() == null) {
            return;
        }
        $event = $observer->getEvent();
        $method = $event->getMethodInstance();
        $result = $event->getResult();
        $customerGroupId = $observer->getQuote()->getCustomerGroupId();
        if ($method->getCode() == 'purchaseorder') {
            $availableForGroups = explode(',', Mage::getStoreConfig('payment/purchaseorder/customergroups'));
            if(!in_array($customerGroupId, $availableForGroups)) {
                $result->isAvailable = false;
            }
        } elseif ($method->getCode() == 'checkmo') { 
            $availableForGroups = explode(',', Mage::getStoreConfig('payment/checkmo/customergroups'));
            if(!in_array($customerGroupId, $availableForGroups) && $customerGroupId != 0) {
                $result->isAvailable = false;
            }
        } elseif ($method->getCode() == 'authorizenet') { 
            $availableForGroups = explode(',', Mage::getStoreConfig('payment/authorizenet/customergroups'));
            if(!in_array($customerGroupId, $availableForGroups) && $customerGroupId != 0) {
                $result->isAvailable = false;
            }
        }
    }

}
