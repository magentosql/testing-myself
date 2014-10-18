<?php

class Wsnyc_PurchaseOrderGroupLimit_Model_Observer {

    public function onPaymentMethodIsActive(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $method = $event->getMethodInstance();
        $result = $event->getResult();
        if ($method->getCode() == 'purchaseorder') {
            $customerGroupId = $observer->getQuote()->getCustomerGroupId();
            $availableForGroups = explode(',', Mage::getStoreConfig('payment/purchaseorder/customergroups'));
            if(!in_array($customerGroupId, $availableForGroups)) {
                $result->isAvailable = false;
            }
        }
    }

}
