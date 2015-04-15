<?php

class Wsnyc_NewsletterModal_Model_Observer {

    public function setSubscriptionStatus(Varien_Event_Observer $observer) {
        $status = $observer->getEvent()->getControllerAction()->getRequest()->getPost('is_subscribed', false);
        Mage::getSingleton('checkout/session')->setSubscriptionStatus($status);
    }

    public function sentSubscription(Varien_Event_Observer $observer) {
        $event = $observer->getEvent();
        $quote =$event->getQuote();
        if (Mage::getSingleton('checkout/session')->getSubscriptionStatus()) {
            $this->_subscribeInMailchimp($quote->getBillingAddress()->getEmail());
        }
    }

    protected function _subscribeInMailchimp($email) {
        $client = new Varien_Http_Client();
        $client->setUri('http://thelaundress.us6.list-manage.com/subscribe/post?u=d3d48e75efd637e646b0beb3c&id=dbfb7e7934');
        $client->setMethod(Zend_Http_Client::POST);
        $client->setConfig(array(
            'maxredirects' => 0,
            'timeout' => 30
        ));
        $client->setParameterPost('EMAIL', $email);
        $client->setParameterPost('subscribe', '');
        $client->setParameterPost('b_d3d48e75efd637e646b0beb3c_dbfb7e7934', '');
        $client->setHeaders(array(
            'Content-type' => 'application/x-www-form-urlencoded',
            'Connection' => 'close'
        ));
        $client->request();
    }
}