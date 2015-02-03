<?php

class Wsnyc_LeavingPage_Model_Observer {

    /**
     * Prevents the Shopping Cart Rule used in the modal box from being deleted
     * @param Varien_Event_Observer $observer
     */
    public function preventDelete($observer) {
        $controller = $observer->getControllerAction();
        if($controller->getRequest()->getParam('id') == Mage::getStoreConfig('wsnyc/leavingpage/rule_id')) {
            $controller->getRequest()->setDispatched(true);
            $controller->setFlag('', Mage_Core_Controller_Front_Action::FLAG_NO_DISPATCH, true);
            Mage::getSingleton('core/session')->addError('This is a system rule and it cannot be deleted. If it is not needed anymore, set the Status option to Inactive.');
            $url = Mage::helper('core/http')->getHttpReferer() ? Mage::helper('core/http')->getHttpReferer()  : Mage::getUrl();
            Mage::app()->getFrontController()->getResponse()->setRedirect($url);
            Mage::app()->getResponse()->sendResponse();
        }        
    }

}
