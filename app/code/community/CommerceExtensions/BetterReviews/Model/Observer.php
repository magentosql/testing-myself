<?php

class CommerceExtensions_BetterReviews_Model_Observer extends Varien_Event_Observer
{
  public function redirectToReviews($observer)
  {
	if(!Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_PRODUCTPAGEREVIEWS_ENABLED)){
	  return;
	}
	
	$product = Mage::registry('current_product');
	if(!$product){		
	  $productId = Mage::app()->getRequest()->getParam('id');
	  $product = Mage::getModel('catalog/product')->load($productId);
	}
	
	Mage::app()->getResponse()
	  ->setRedirect($product->getProductUrl().CommerceExtensions_BetterReviews_Model_Config::REVIEWS_HASH_SUFFIX, 301)
	  ->sendResponse();
	exit;			
  }
}