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
        $log = false;
        if ($method->getCode() == 'purchaseorder') {
            $availableForGroups = explode(',', Mage::getStoreConfig('payment/purchaseorder/customergroups'));
            if(!in_array($customerGroupId, $availableForGroups)) {
                $log = true;
                $result->isAvailable = false;
            }
        } elseif ($method->getCode() == 'checkmo') { 
            $availableForGroups = explode(',', Mage::getStoreConfig('payment/checkmo/customergroups'));
            if(!in_array($customerGroupId, $availableForGroups) && $customerGroupId != 0) {
                $log = true;
                $result->isAvailable = false;
            }
        } elseif ($method->getCode() == 'authorizenet') { 
            $availableForGroups = explode(',', Mage::getStoreConfig('payment/authorizenet/customergroups'));
            if(!in_array($customerGroupId, $availableForGroups) && $customerGroupId != 0) {
                $log = true;
                $result->isAvailable = false;
            }
        }

        if ($log) {
            Mage::log('inactive payment method', null, 'paymentmethods.log');
            Mage::log($customerGroupId, null, 'paymentmethods.log');
            Mage::log(Mage::getSingleton('customer/session')->getCustomer()->debug(), null, 'paymentmethods.log');
            Mage::log($observer->getQuote()->debug(), null, 'paymentmethods.log');
        }
    }

}
