<?php

class Wsnyc_BillToAccountNumber_Helper_Data extends Mage_Core_Helper_Abstract {
    public function checkIfShippingMethodIsAllowed() {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        }
        if(Mage::app()->getRequest()->getModuleName() == 'thelaund_admin' && Mage::getSingleton('adminhtml/session_quote')) {
            $store = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
            $customer = Mage::getSingleton('adminhtml/session_quote')->getQuote()->getCustomer();
            if($customer) {
                $customerGroupId = $customer->getGroupId();
            }
        } else {
            $store = null;
        }
        $availableForGroups = explode(',', Mage::getStoreConfig('carriers/billtoaccountnumber/customergroups', $store));
        if ($customerGroupId && in_array($customerGroupId, $availableForGroups)) {
            return true; 
        }
        return false; 
    }
}
