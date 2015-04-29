<?php

class Wsnyc_GoogleRemarketing_Block_Cart extends Wsnyc_GoogleRemarketing_Block_Abstract {
  
  protected function _getCartProducts() {
    return Mage::getSingleton('checkout/session')->getQuote()->getAllVisibleItems();
  }
  
  public function getEcommPageType() {
    return 'cart';
  }
  
  public function getEcommProdId() {
    $products = array();
    foreach($this->_getCartProducts() as $item) {
      $products[] = trim($item->getSku());
    }    
    return $products;
  }
  
  public function getEcommPValue() {
    $prices= array();
    foreach($this->_getCartProducts() as $item) {
      $prices[] = $item->getRowTotal();
    }
    
    return $prices;
  }
  
  public function getEcommQuantity() {
    $qty= array();
    foreach($this->_getCartProducts() as $item) {
      $qty[] = $item->getQty();
    }
    
    return $qty;
  }
  
  public function getEcommTotalValue() {
    return Mage::getSingleton('checkout/cart')->getQuote()->getSubtotal();
  }
  
  protected function _getGoogleParams() {
    
    $params = parent::_getGoogleParams();
    $params['ecomm_pvalue'] = $this->getEcommPValue();
    $params['ecomm_quantity'] = $this->getEcommQuantity();
    return $params;
  }
  
}
