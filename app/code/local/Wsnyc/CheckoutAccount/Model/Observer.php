<?php

class Wsnyc_CheckoutAccount_Model_Observer
{

    /**
     * Register customer at billing step
     *
     * @param Varien_Event_Observer $observer
     */
    public function registerCustomer(Varien_Event_Observer $observer)
    {
        /** @var Mage_Checkout_Model_Type_Onepage $checkout */
        $checkout = Mage::getSingleton('checkout/type_onepage');
        if (!Mage::helper('customer')->isLoggedIn() && $checkout->getQuote()->getData('checkout_method') == 'register') {
            $request = $observer->getEvent()->getControllerAction()->getRequest();
            $data = $request->getPost('billing', array());
            $customer = Mage::getModel("customer/customer");
            $customer->setWebsiteId(Mage::app()->getStore()->getWebsiteId())->loadByEmail($data['email']);
            if (!$customer->getId()) {
                //Code begins here for new customer registration
                $this->_saveCustomer($customer, $data);
                $this->_saveCustomerAddress($customer, $data);
                Mage::getSingleton('customer/session')->loginById($customer->getId());
                $checkout->getQuote()->assignCustomer($customer);
                $response = $observer->getEvent()->getControllerAction()->getResponse();
                $result = json_decode($response->getBody(), true);
                $result['top_links'] = $this->_renderHeader();
                $response->setBody(Mage::helper('core')->jsonEncode($result));
            }
            else {
                //email already used
                $result = array('error' => -1, 'message' => Mage::helper('checkout')->__('There is an account registered for this email.'));
                $response = $observer->getEvent()->getControllerAction()->getResponse();
                $response->setBody(Mage::helper('core')->jsonEncode($result));
            }
        }
    }

    /**
     * Register new customer account
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param                              $data
     * @throws Exception
     * @throws Mage_Core_Exception
     */
    protected function _saveCustomer(Mage_Customer_Model_Customer $customer, $data) {
        $customer->setWebsiteId(Mage::app()->getWebsite()->getId());
        $customer->setStore(Mage::app()->getStore());
        $customer->setFirstname($data['firstname']);
        $customer->setLastname($data['lastname']);
        $customer->setEmail($data['email']);
        $customer->setPassword($data['customer_password']);
        $customer->save();
        $customer->sendNewAccountEmail('confirmed');
    }

    /**
     * Save billing addresss as default address
     *
     * @param Mage_Customer_Model_Customer $customer
     * @param                              $data
     * @throws Exception
     */
    protected function _saveCustomerAddress(Mage_Customer_Model_Customer $customer, $data) {
        $address = Mage::getModel("customer/address");
        $address->setCustomerId($customer->getId())
            ->setFirstname($customer->getFirstname())
            ->setMiddleName($customer->getMiddlename())
            ->setLastname($customer->getLastname())
            ->setCountryId($data['country_id'])
            ->setRegionId($data['region_id'])
            ->setRegion($data['region'])
            ->setPostcode($data['postcode'])
            ->setCity($data['city'])
            ->setTelephone($data['telephone'])
            ->setFax($data['fax'])
            ->setCompany($data['company'])
            ->setStreet(implode(' ',$data['street']))
            ->setIsDefaultBilling('1')
            ->setIsDefaultShipping('1')
            ->setSaveInAddressBook('1');
        $address->save();
    }

    /**
     * Render toplinks
     *
     * @return string
     * @throws Mage_Core_Exception
     */
    protected function _renderHeader() {
        $layout = Mage::getModel('core/layout');
        $update = $layout->getUpdate();
        $update->load(array('default', 'customer_logged_in'));
        $layout->generateXml();
        $layout->generateBlocks();
        $output = $layout->getBlock('top.links')->toHtml();
        return $output;
    }
}