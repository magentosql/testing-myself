<?php

class Wsnyc_PromotionalGiftExt_Model_Shoppingcartrule extends Magestore_Promotionalgift_Model_Shoppingcartrule {
    
    const DEFAULT_VENDOR_XML_PATH = 'promotionalgift/general/vendor';
    
    /**
     * Fetch rules only if there is default vendor items in cart
     * 
     * @param array $ruleIds
     * @return null|Magestore_Promotionalgift_Model_Mysql4_Shoppingcartrule_Collection
     */
    public function getAvailableRule($ruleIds = array()) {
        if ($this->_checkCartItems()) {
            return parent::getAvailableRule($ruleIds);
        }
        
        return null;
    }
    
    /**
     * Check if cart has items from default vendor
     * 
     * @return boolean
     */
    protected function _checkCartItems() {
        $hasDefaultVendor = true;
        $vendorId = Mage::getStoreConfig(self::DEFAULT_VENDOR_XML_PATH);
        if (Mage::helper('core')->isModuleEnabled('Unirgy_Dropship') && $vendorId != 0) {
            $hasDefaultVendor = false;
            foreach (Mage::getModel('checkout/cart')->getItems() as $item) {
                if ($item->getUdropshipVendor() == $vendorId) {
                    $hasDefaultVendor = true;
                    break;
                }
            }
        }
        
        return $hasDefaultVendor;        
    }
}