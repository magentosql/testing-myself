<?php

class Wsnyc_CheckoutAccount_Model_Observer
{

    public function registerCustomer(Varien_Event_Observer $observer)
    {
        /** @var Mage_Checkout_Model_Type_Onepage $checkout */
        $checkout = Mage::getSingleton('checkout/type_onepage');
        if (!Mage::helper('customer')->isLoggedIn() && $checkout->getQuote()->getData('checkout_method') == 'register') {
            $request = $observer->getEvent()->getControllerAction()->getRequest();
            $data = $request->getPost('billing', array());
            $customer = Mage::getModel("customer/customer");
            $email = $data['email'];
            $websiteId = Mage::app()->getWebsite()->getId();
            $store = Mage::app()->getStore();
            $pwd = $data['customer_password'];
            $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($email);
            if (!$customer->getId()) {
                //Code begins here for new customer registration
                $customer->setWebsiteId($websiteId);
                $customer->setStore($store);
                $customer->setFirstname($data['firstname']);
                $customer->setLastname($data['lastname']);
                $customer->setEmail($email);
                $customer->setPassword($pwd);
                $customer->sendNewAccountEmail('confirmed');
                $customer->save();
                Mage::getSingleton('customer/session')->loginById($customer->getId());
                $checkout->getQuote()->assignCustomer($customer);
            }
            else {
                //email already used
                $result = array('error' => -1, 'message' => Mage::helper('checkout')->__('There is an account registered for this email.'));
                $response = $observer->getEvent()->getControllerAction()->getResponse();
                $response->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }
}