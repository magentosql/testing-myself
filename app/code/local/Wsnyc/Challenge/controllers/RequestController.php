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

        return true;
    }
}