<?php

class Wsnyc_GoogleRemarketing_Model_Observer {

    public function saveLastOrderId($observer) {
        $order = $observer->getEvent()->getOrderIds();
        if(Mage::app()->getLayout()->getBlock('remarketing.checkout')) {
            Mage::app()->getLayout()->getBlock('remarketing.checkout')->setOrdersId($order);
        }
    }
}