<?php

/**
 *
 * Class Wsnyc_LeavingPage_Block_Modal
 *
 * @method setCmsBlock()
 * @method Mage_Cms_Block_Block getCmsBlock();
 */
class Wsnyc_LeavingPage_Block_Modal extends Mage_Core_Block_Template {

    protected function _construct() {
        $this->setCmsBlock(Mage::getModel('cms/block')->load('pageleave-modal', 'identifier'));

        if($this->_canRender()) {
            $this->setTemplate('wsnyc/leavingpage/modal.phtml');
        }
    }
    
    protected function _canRender() {
        // check is module enabled
        if (Mage::getStoreConfig('advanced/modules_disable_output/Wsnyc_LeavingPage') || !Mage::getStoreConfig('promo/leavingpage/active')) {
            return false;
        }
        // enabled in store
        $storeAllowed = array_intersect(array(Mage::app()->getStore()->getStoreId(), '0'), $this->getCmsBlock()->getStoreId()); 
        if(!$this->getCmsBlock()->getIsActive() || empty($storeAllowed)) {
            return false;
        }

        // generate only for new customers
        if (Mage::getStoreConfig('promo/leavingpage/only_new') && Mage::getSingleton('customer/session')->isLoggedIn()) {
            return false;
        }

        // check is allowed page
        if (!$this->_isAllowedPage()) {
            return false;
        }

            //check if customer already used this promotion{}
        if (Mage::helper('leavingpage')->couponUsed()) {
            return false;
        }

        return true;
    }

    protected function _isAllowedPage()
    {
        $currentRequest = $this->getRequest()->getModuleName() . '-' . $this->getRequest()->getControllerName();
        $allowedPages = Mage::getSingleton('leavingpage/config')->getAllowedPages();

        // add exception for homepage
        $currentRequestWithAction = $currentRequest . '-' . $this->getRequest()->getActionName();
        if ('cms-index-index' == $currentRequestWithAction) {
            $currentRequest = $currentRequestWithAction;
        }

        if (in_array($currentRequest, $allowedPages)) {
            return true;
        } else {
            return false;
        }
    }
}
