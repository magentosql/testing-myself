<?php

class CommerceExtensions_BetterReviews_Model_Product_View_Observer extends Varien_Event_Observer
{
  public function updateHandles($observer)
  {
	if(!Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_PRODUCTPAGEREVIEWS_ENABLED)){
	  return;
	}
	
	$handles = Mage::getSingleton('betterreviews/data')->getHandles();
	if(!in_array('catalog_product_view',$handles)){
	  return;
	}
	
	$update = Mage::getSingleton('core/layout')->getUpdate();
	$update->addHandle('product_page_reviews');	
	return;
  }
}