<?php

class Wsnyc_NoShippingNeeded_Helper_Data extends Mage_Core_Helper_Abstract {
    public function checkIfShippingMethodStepShouldBeIgnored() {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        }
        $availableForGroups = explode(',', Mage::getStoreConfig('carriers/noshippingneeded/customergroups'));
        if ($customerGroupId && in_array($customerGroupId, $availableForGroups)) {
            return true; 
        }
        return false; 
    }
}
