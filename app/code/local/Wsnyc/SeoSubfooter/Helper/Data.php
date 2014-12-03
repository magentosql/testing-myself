<?php

class Wsnyc_SeoSubfooter_Helper_Data extends Mage_Core_Helper_Abstract {
    
    public function getCmsPageBodyClass() {
        $page = Mage::getSingleton('cms/page');
        return $page->getSeosubfooterLink() ? 'seosubfooter-page' : null;
    }
}