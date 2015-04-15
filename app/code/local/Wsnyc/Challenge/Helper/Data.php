<?php

class Wsnyc_Challenge_Helper_Data extends Mage_Core_Helper_Abstract {

    /**
     * Create order for challenge sample request
     *
     * @param array                             $request
     * @param Mage_Catalog_Model_Product        $product
     * @return Mage_Sales_Model_Service_Quote
     */
    public function createSampleOrder($request, Mage_Catalog_Model_Product $product) {
        /* @var $quote Mage_Sales_Model_Quote */
        $quote = Mage::getModel('sales/quote')
                        ->setStoreId(Mage::app()->getStore()->getId())
                        ->setIsSuperMode(true);

        $this->_addCustomer($quote, $request['email']);
        $this->_addProduct($quote, $product, $request['qty']);
        $this->_addAddresses($quote, $request);
        $this->_addPayment($quote);
        $quote->collectTotals()->save();

        $service = Mage::getModel('sales/service_quote', $quote);
        $service->submitAll();

        return $service;
    }

    /**
     * Add customer to the order
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param string                 $email
     * @throws Mage_Core_Exception
     */
    protected function _addCustomer(Mage_Sales_Model_Quote $quote, $email) {
        $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($email);
        if($customer->getId()) {
            $quote->assignCustomer($customer);
        }
        else {
            $quote->setCustomerEmail($email);
        }
    }

    /**
     * Add selected product to quote
     *
     * @param Mage_Sales_Model_Quote     $quote
     * @param Mage_Catalog_Model_Product $_product
     * @param int                        $qty
     */
    protected function _addProduct(Mage_Sales_Model_Quote $quote, Mage_Catalog_Model_Product $_product, $qty) {

        $item = new Varien_Object(array('qty' => $qty,'product' => $_product->getId()));
        $quotItem = $quote->addProduct($_product, $item);
        $quotItem->setCustomPrice(0);
        $quotItem->setOriginalCustomPrice(0);
        $quotItem->getProduct()->setIsSuperMode(true);
    }

    /**
     * Add addresses to the quote
     *
     * @param Mage_Sales_Model_Quote $quote
     * @param array                  $data
     */
    protected function _addAddresses(Mage_Sales_Model_Quote $quote, $data) {
        $address = array(
            'firstname' => $data['firstname'],
            'lastname' => $data['lastname'],
            'email' => $data['email'],
            'street' => $data['street'],
            'city' => $data['city'],
            'country_id' => $data['country'],
            'region_id' => $data['region_id'],
            'postcode' => $data['postcode'],
            'telephone' => '1111111111'
        );
        $billingAddress = $quote->getBillingAddress()->addData($address);
        $shippingAddress = $quote->getShippingAddress()->addData($address);
        $this->_setShippingMethod($shippingAddress);
    }

    /**
     * Set shipping method
     *
     * @param Mage_Sales_Model_Quote_Address $shippingAddress
     */
    protected function _setShippingMethod(Mage_Sales_Model_Quote_Address $shippingAddress) {
        $shippingAddress
            ->setCollectShippingRates(false)
            ->setShippingAmount(0)
            ->setBaseShippingAmount(0)
            ->setShippingMethod('flatrate_flatrate')
            ->setShippingDescription('');

        /* @var $rate Mage_Sales_Model_Quote_Address_Rate */
        $rate = Mage::getModel('sales/quote_address_rate');
        $rate->setCode( 'flatrate_flatrate' )
                ->setCarrier('flatrate')
                ->setMethod('flatrate')
                ->setMethodTitle('Fixed')
                ->setPrice(0)
                ->setCarrierTitle('Flat Rate' );

        $shippingAddress->addShippingRate($rate);
        $shippingAddress->setPaymentMethod('free');
    }

    /**
     * Add free payment method
     *
     * @param Mage_Sales_Model_Quote $quote
     */
    protected function _addPayment(Mage_Sales_Model_Quote $quote) {
        $quote->getPayment()->importData(array('method' => 'free'));
    }
}