<?php

class Wsnyc_GoogleRemarketing_Block_Product extends Wsnyc_GoogleRemarketing_Block_Abstract {
  
  protected $_product;
 
  protected function _getProduct() {
    
    if ($this->_product == null) {
      $product = Mage::registry('current_product');
      if (!$product) {
         $product = Mage::getModel('catalog/product');
      }
      $this->_product = $product;
    }
    return $this->_product;
  }
  
  public function getEcommPageType() {
    return 'product';
  }
  
  public function getEcommProdId() {
    return trim($this->_getProduct()->getSku());
  }
  
  public function getEcommTotalValue() {
    return Mage::helper('core')->currency($this->_getProduct()->getFinalPrice(), false, false);
  }
  
  
}