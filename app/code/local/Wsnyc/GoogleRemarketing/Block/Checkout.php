<?php

class Wsnyc_GoogleRemarketing_Block_Checkout extends Wsnyc_GoogleRemarketing_Block_Abstract {
  
  /**
   * Order Ids
   * @var array 
   */
  protected $_orders;
  
  /**
   * Get orders
   */
  public function getOrders() {
    if ($this->_orders == null) {
      $orders = $this->getOrdersId();
      $this->_orders = Mage::getModel('sales/order')->getCollection()->addAttributeToFilter('entity_id', array('in' => $orders));      
    }
    return $this->_orders;
  }
  
  /**
   * Check if this if first order based on customer billing address email
   * 
   * @param Mage_Sales_Model_Order $order
   * @return string
   */
  public function getNewCustomer(Mage_Sales_Model_Order $order) {    
    
    $address = $order->getBillingAddress();
    /* @var $collection Mage_Sales_Model_Resource_Order_Collection */
    $collection = Mage::getModel('sales/order')->getCollection()->addAttributeToFilter('customer_email', array('eq' => $address->getEmail()));
    return $collection->count() == 1 ? '1' : '0';
  }
  
  public function getEcommPageType() {
    return 'purchase';
  }
  
  public function getEcommProdId() {
    $products = array();
    foreach($this->getOrders() as $order) {
      foreach ($order->getItemsCollection() as $item) {
        $products[] = trim($item->getProduct()->getSku());
      }
    }
    return $products;
  }
  public function getEcommPValue() {
    $prices = array();
    foreach($this->getOrders() as $order) {
      foreach ($order->getItemsCollection() as $item) {
        $prices[] = $this->helper('core')->currency($item->getPrice(), false, false);
      }
    }
    return $prices;
  }
  
  public function getEcommQuantity() {
    $qty = array();
    foreach($this->getOrders() as $order) {
      foreach ($order->getItemsCollection() as $item) {
        $qty[] = $item->getQtyOrdered() * 1;
      }
    }
    return $qty;
  }
  
  public function getEcommTotalValue() {
    $total = 0;
    foreach($this->getOrders() as $order) {
      $total += $order->getSubtotal();
    }
    
    return $this->helper('core')->currency($total, false, false);    
  }
  
  
  protected function _getGoogleParams() {
    
    $params = parent::_getGoogleParams();
    $params['ecomm_pvalue'] = $this->getEcommPValue();
    $params['ecomm_quantity'] = $this->getEcommQuantity();
    return $params;
  }
  
}
