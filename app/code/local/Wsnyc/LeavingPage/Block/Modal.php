<?php

class Wsnyc_LeavingPage_Block_Modal extends Mage_Core_Block_Template {

    protected function _construct() {
        $this->setCmsBlock(Mage::getModel('cms/block')->load('pageleave-modal', 'identifier'));
        if(!$this->_isAllowedToRender()) {
            return false;
        }
//        Mage::getSingleton('core/cookie')->set('pageleave', '1' ,time()+3600000,'/');
        $this->setTemplate('wsnyc/leavingpage/modal.phtml');
    }
    
    protected function _isAllowedToRender() {
        if(Mage::getStoreConfig('advanced/modules_disable_output/Wsnyc_LeavingPage')) {
            return false;
        }
        $storeAllowed = array_intersect(array(Mage::app()->getStore()->getStoreId(), '0'), $this->getCmsBlock()->getStoreId()); 
        if(!$this->getCmsBlock()->getIsActive() || empty($storeAllowed)) {
            return false;
        }
//        if(Mage::getSingleton('core/cookie')->get('pageleave')) {
//            return false;
//        }
        return true;
    }
}
