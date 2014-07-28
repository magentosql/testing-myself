<?php

class Wsnyc_Redirects_Model_Observer {
    public function redirectToNiceUrl($observer) {
        $request = Mage::app()->getRequest();
        if($request->getRouteName() == 'catalog') {
            if($request->getPathInfo() == $request->getOriginalPathInfo()) {
                $redirectCollection = Mage::getModel('core/url_rewrite')->getCollection()
                        ->addFieldToFilter('target_path', array('eq' => ltrim($request->getPathInfo(),'/')))
                        ->addFieldToFilter('store_id', array('eq' => Mage::app()->getStore()->getStoreId()));
                if(count($redirectCollection)) {
                    $redirect = $redirectCollection->getFirstItem();
                    if(substr('product/view', $request->getPathInfo())) {
                        $rewrittenUrl = Mage::getBaseUrl() . $redirect->getRequestPath() . Mage::getConfig('catalog/seo/product_url_suffix');
                    } else {
                        $rewrittenUrl = Mage::getBaseUrl() . $redirect->getRequestPath() . Mage::getConfig('catalog/seo/category_url_suffix');
                    }
                    Mage::app()->getResponse()->setRedirect($rewrittenUrl, 301)->sendResponse();
                }                
            }
        } elseif ($request->getRouteName() == 'asklaundress') {
            if(ltrim($request->getPathInfo(),'/') == ltrim($request->getOriginalPathInfo(),'/')) {
                $redirectCollection = Mage::getModel('core/url_rewrite')->getCollection()
                        ->addFieldToFilter('target_path', array('eq' => ltrim($request->getPathInfo(),'/')))
                        ->addFieldToFilter('store_id', array('in' => array(Mage::app()->getStore()->getStoreId(),0)));
                if(count($redirectCollection)) {
                    $redirect = $redirectCollection->getFirstItem();
                    $rewrittenUrl = Mage::getBaseUrl() . $redirect->getRequestPath();
                    Mage::app()->getResponse()->setRedirect($rewrittenUrl, 301)->sendResponse();
                } 
            }
        }
    }
}

