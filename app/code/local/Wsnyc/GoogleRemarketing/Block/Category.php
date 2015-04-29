<?php

class Wsnyc_GoogleRemarketing_Block_Category extends Wsnyc_GoogleRemarketing_Block_Abstract {
 
  public function getEcommPageType() {
    return 'category';
  }
  
  public function getEcommCategory() {
    return Mage::registry('current_category')->getName();
  }
  
  protected function _getGoogleParams() {
    
    $params = parent::_getGoogleParams();
    $params['ecomm_category'] = $this->getEcommCategory();
    return $params;
  }
}
