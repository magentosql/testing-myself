<?php

class Wsnyc_LeavingPage_Block_Modal extends Mage_Core_Block_Template {

    protected function _construct() {
        $this->setCmsBlock(Mage::getModel('cms/block')->load('pageleave-modal', 'identifier'));
        if(!$this->_isAllowedToRender()) {
            return false;
        }

        $this->setTemplate('wsnyc/leavingpage/modal.phtml');
    }
    
    protected function _isAllowedToRender() {
        if(Mage::getStoreConfig('advanced/modules_disable_output/Wsnyc_LeavingPage') || !Mage::getStoreConfig('promo/leavingpage/active')) {
            return false;
        }
        $storeAllowed = array_intersect(array(Mage::app()->getStore()->getStoreId(), '0'), $this->getCmsBlock()->getStoreId()); 
        if(!$this->getCmsBlock()->getIsActive() || empty($storeAllowed)) {
            return false;
        }

        //generate only for new customers
        if (Mage::getStoreConfig('promo/leavingpage/only_new') && Mage::getSingleton('customer/session')->isLoggedIn()) {
            return false;
        }

        //check if customer already used this promotion
        if (Mage::helper('leavingpage')->couponUsed()) {
            return false;
        }

        return true;
    }
}
