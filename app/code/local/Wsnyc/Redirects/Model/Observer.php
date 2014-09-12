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
        } elseif ($request->getRouteName() == 'asklaundress' || $request->getRouteName() == 'cms') {
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
    
    public function addCmsRedirect($observer) {
        $page = $observer->getDataObject();
        if($page->getOrigData()) {
            $newUrl = $page->getIdentifier();
            foreach($page->getStores() as $store) {
                $rewrite = Mage::getModel('core/url_rewrite')->load($page->getOrigData('identifier'), 'request_path');                
                $rewrite->setIsSystem(0)
                        ->setOptions('RP')
                        ->setIdPath($newUrl.'-custom-redirect')
                        ->setTargetPath($newUrl)
                        ->setRequestPath($page->getOrigData('identifier'))
                        ->setStoreId($store)
                        ->save();
                Mage::getModel('core/url_rewrite')
                        ->setIsSystem(0)
                        ->setOptions()
                        ->setIdPath($newUrl.'-custom-redirect-2')
                        ->setTargetPath('cms/page/view/id/'.$page->getId())
                        ->setRequestPath($newUrl)
                        ->setStoreId($store)
                        ->save();
            }
        }
    }
    
    public function addCategoryRedirect($observer) {
        
        $category = $observer->getDataObject();
        if($category->getCategoryId()) {
            $originalObject = Mage::getModel('wsnyc_questionsanswers/category')->load($category->getCategoryId(),'category_id');
            $parentObject = Mage::getModel('wsnyc_questionsanswers/category')->load($category->getParentId(),'category_id');
            $newIdentifier = str_replace(" ", "-", strtolower($parentObject->getName())) . "/" . str_replace(" ", "-", strtolower($category->getName()));
            Mage::getModel('core/url_rewrite')
                    ->setIsSystem(0)
                    ->setOptions('RP')
                    ->setIdPath($newIdentifier . '-custom-redirect')
                    ->setTargetPath($newIdentifier)
                    ->setRequestPath($originalObject->getIdentifier())
                    ->setStoreId(0)
                    ->save();        
        }
    }
}

