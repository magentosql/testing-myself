<?php

class Wsnyc_BillToAccountNumber_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction() {
        
    }
    
    public function processAction() {
        $number = Mage::app()->getRequest()->getParam('number');
        if(Mage::getSingleton('checkout/session') && !Mage::app()->getStore()->isAdmin()) {
            $quote = Mage::getSingleton('checkout/session')->getQuote()->setThirdPartyShippingAccount($number)->save();
        } 
    }

}
