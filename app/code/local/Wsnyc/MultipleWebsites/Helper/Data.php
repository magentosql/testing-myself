<?php
class Wsnyc_MultipleWebsites_Helper_Data extends Mage_Core_Helper_Abstract {
    public function getCustomerGroupPriceModifier() {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            return Mage::getModel('customer/group')->load(Mage::getSingleton('customer/session')->getCustomer()->getGroupId())->getPriceMultiplier();
        } else {
            return 1;
        }        
    }
}
	 