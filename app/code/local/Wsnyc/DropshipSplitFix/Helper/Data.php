<?php

class Wsnyc_DropshipSplitFix_Helper_Data extends Unirgy_DropshipSplit_Helper_Data {
    
    public function isActive($store=null) {
        $request = Mage::app()->getRequest();
        if($request->getModuleName() == 'thelaund_admin' && Mage::getSingleton('adminhtml/session_quote')) {
            $store = Mage::getSingleton('adminhtml/session_quote')->getStoreId();
        }
        return Mage::getStoreConfigFlag('carriers/udsplit/active', $store);
    }
}
