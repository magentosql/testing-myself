<?php

class Wsnyc_BillToAccountNumber_Model_Observer {
    public function setThirdPartyShippingNumber($observer) {
        $params = $observer->getControllerAction()->getRequest()->getPost('order');
        $thirdPartyShippingNumber = $params['third_party_shipping_number'];
        if($thirdPartyShippingNumber) {
            Mage::getSingleton('adminhtml/session_quote')->getQuote()->setThirdPartyShippingAccount($thirdPartyShippingNumber)->save();
        }
    }
}