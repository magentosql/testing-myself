<?php
/**
 * Status source class
 * 
 */
class Wsnyc_SeoSubfooter_Model_Source_Status extends Mage_Core_Model_Abstract {
  
  const BLURB_STATUS_ENABLED = 1;
  const BLURB_STATUS_DISABLED = 0;
  
  public function toOptionHash() {
    return array(
        self::BLURB_STATUS_ENABLED => Mage::helper('seosubfooter')->__('Enabled'),
        self::BLURB_STATUS_DISABLED => Mage::helper('seosubfooter')->__('Disabled')
    );
  }
}