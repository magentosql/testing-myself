<?php

class CommerceExtensions_BetterReviews_Model_Rating_Detailed_Observer extends Varien_Event_Observer
{
  public function addRichSnippets($observer)
  {
	$block = $observer->getBlock();
	if(!$block instanceof Mage_Rating_Block_Entity_Detailed &&
	   !$block instanceof CommerceExtensions_BetterReviews_Block_Rating_Entity_Detailed){
	  return;
	}
	
	if(!Mage::registry('current_product')){
	  return;
	}
	
	if(!Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_RICHSNIPPETS_ENABLED)){
	  return;
	}
	
	$product = Mage::registry('current_product') ? Mage::registry('current_product') : Mage::getModel('catalog/product')->load(Mage::app()->getRequest()->getParam('id'));

	// now that we have conducted all checks, we have our block isolated and do what is necessary below this line	
	$block->setAddRichSnippets(true);
	$block->setReviewsCount(Mage::app()->getLayout()->getBlockSingleton('review/product_view_list')->getReviewsCollection()->getSize());
	$block->setTemplate("betterreviews/detailed.phtml");
	return;		  
  } 	
}