<?php

class Wsnyc_Challenge_RequestController extends Mage_Core_Controller_Front_Action {

    /**
     * Process sample request form
     */
    public function indexAction() {
        if (!$this->_validateRequest()) {
            $this->getResponse()->sendResponse();
            exit;
        }

        $request = $this->getRequest()->getPost();
        $request['qty'] = 1;
        $product = Mage::getModel('catalog/product')->loadByAttribute('sku', $request['sample']);
        if (!$product || !$product->getId()) {
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('Selected sample cannot be found'));
            $this->_redirect('30-day-clean-home-challenge-sample-request');
        }

        try {
            $service = Mage::helper('wsnyc_challenge')->createSampleOrder($request, $product);
        }
        catch (Exception $e) {
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('An error occurred while saving the request. Please try again later'));
            Mage::logException($e);
            $this->_redirect('30-day-clean-home-challenge-sample-request');
        }

        //subscribe to newsletter
        Mage::getSingleton('checkout/session')->setSubscriptionStatus($request['newsletter']);
        Mage::dispatchEvent('submit_sample_request', array('quote' => $service->getQuote()));

        Mage::getSingleton('checkout/session')->setLastRealOrderId($service->getOrder()->getIncrementId());
        $this->_redirect('30-day-clean-home-challenge-sample-request-success');
    }

    /**
     * Check for required fields
     */
    protected function _validateRequest() {
        if (!$this->getRequest()->isPost()) {
            $this->_redirect('30-day-clean-home-challenge-sample-request');
            return false;
        }
        if ($this->getRequest()->getPost('newsletter', 0) == 0) {
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('Subscription to newsletter is required for receive free sample'));
            $this->_redirect('30-day-clean-home-challenge-sample-request');
            return false;
        }
        if ($this->getRequest()->getPost('sample', null) == null) {
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('Please select sample item'));
            $this->_redirect('30-day-clean-home-challenge-sample-request');
            return false;
        }

        if ($this->_findAnotherSampleOrder()) {
            Mage::getSingleton('core/session')->addError(Mage::helper('core')->__('An order for this customer has already been placed'));
            $this->_redirect('30-day-clean-home-challenge-sample-request');
            return false;
        }

        return true;
    }

    /**
     * Try to detect if given customer has already placed a sample request order
     *
     * @return bool
     */
    protected function _findAnotherSampleOrder() {
        $request = $this->getRequest()->getPost();
        $_sampleProducts = array('Bleach-PQ', 'Dish-PQ', 'Sport-PQ', 'Denim-PQ', 'ClassicConditioner-PQ');

        /** @var Mage_Sales_Model_Resource_Order_Collection $collection */
        $collection = Mage::getModel('sales/order')->getCollection();

        $collection->join(array('items' => 'sales/order_item'), 'items.order_id=main_table.entity_id')
                    ->join(array('address' => 'sales/order_address'), 'address.parent_id=main_table.entity_id')
                    ->addFieldToFilter('main_table.base_grand_total', array('eq' => 0.0000))
                    ->addFieldToFilter('items.sku', array('ub' => $_sampleProducts))
                    ->addFieldToFilter(
                        array('main_table.customer_email','CONCAT(address.postcode, address.city,address.street)'),
                        array(
                            array('eq' => $request['email']),
                            array('eq' => $request['postcode'].$request['city'].$request['street'][0]),
                            )
                    );


        return ($collection->count() > 0);
    }
}