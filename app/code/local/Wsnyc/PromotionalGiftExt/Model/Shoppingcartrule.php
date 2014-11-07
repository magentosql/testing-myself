<?php

class Wsnyc_PromotionalGiftExt_Model_Shoppingcartrule extends Magestore_Promotionalgift_Model_Shoppingcartrule {

    const DEFAULT_VENDOR_XML_PATH = 'promotionalgift/general/vendor';
    const ALLOW_VIRTUAL_XML_PATH = 'promotionalgift/general/allow_virtual';

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
        $checkVendor = false;
        $ids = Mage::getStoreConfig(self::DEFAULT_VENDOR_XML_PATH);        
        if (Mage::helper('core')->isModuleEnabled('Unirgy_Dropship') && $ids == null) {
            $hasDefaultVendor = false;
            $vendorIds = explode(',', $ids);            
            foreach (Mage::getModel('checkout/cart')->getItems() as $item) {                
                if ($checkVendor && in_array($item->getUdropshipVendor(), $vendorIds)) {        
                    $hasDefaultVendor = true;
                    break;
                }
            }
        }
        //do not show gift if there are only virtual items as this would cause shipping charge
        $virtualOnly = Mage::getStoreConfig(self::ALLOW_VIRTUAL_XML_PATH) ? false : Mage::getSingleton('checkout/type_onepage')->getQuote()->isVirtual();
        
        return $hasDefaultVendor && !$virtualOnly;
    }

}
