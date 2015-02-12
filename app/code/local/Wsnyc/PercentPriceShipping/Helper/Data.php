<?php

class Wsnyc_PercentPriceShipping_Helper_Data extends Mage_Core_Helper_Abstract {
    public function checkIfShippingMethodIsAllowed() {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $customerGroupId = Mage::getSingleton('customer/session')->getCustomer()->getGroupId();
        }
        $availableForGroups = explode(',', Mage::getStoreConfig('carriers/percentageprice/customergroups'));
        if ($customerGroupId && in_array($customerGroupId, $availableForGroups)) {
            return true; 
        }
        return false; 
    }
}
