<?php

class Wsnyc_GoogleRemarketing_Model_Observer {
  
  public function saveLastOrderId($observer) {
    $order = $observer->getEvent()->getOrderIds();    
    Mage::app()->getLayout()->getBlock('remarketing.checkout')->setOrdersId($order);
    
  }
}