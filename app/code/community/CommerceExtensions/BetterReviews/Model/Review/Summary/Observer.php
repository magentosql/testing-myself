<?php

class CommerceExtensions_BetterReviews_Model_Review_Summary_Observer extends Varien_Event_Observer
{
  public function addAssociatedProductsToSummary($observer)
  {
	// THIS OBSERVER SETS PRODUCT PAGE REVIEW SUMMARY.
	$product = $observer->getProduct();

	$productId = $product->getId();
	$storeId = Mage::app()->getStore()->getStoreId();

	$dataModel = Mage::getSingleton('betterreviews/data');
	$canInclude = $dataModel->getCanIncludeAssociatedProducts($product);
	if(!$canInclude){
	  return;
	}	

	$productIds = $dataModel->getChildIds($product);
	if(empty($productIds) || !$productIds){
	  return;
	}	  

	$data = $this->_getReviewSummary($productIds,$storeId);
	if(!$data){
	  return;
	}
	$product->setData('rating_summary',new Varien_Object());
	$product->getRatingSummary()->setReviewsCount($data->getReviewsCount());
	$product->getRatingSummary()->setRatingSummary($data->getRatingSummary());   

  }
  
  public function addAssociatedProductsToSummaryHelper($observer)
  {	
	// THIS OBSERVER SETS CATEGORY PRODUCT LIST SUMMARY.	
	$storeId = Mage::app()->getStore()->getStoreId();
	$dataModel = Mage::getSingleton('betterreviews/data');
		
	$collection = $observer->getCollection();
	foreach($collection as $product){
	  $productId = $product->getId();
	  $canInclude = $dataModel->getCanIncludeAssociatedProducts($product);
	  if(!$canInclude){
		continue;
	  }	  
	  $productIds = $dataModel->getChildIds($product);
	  if(empty($productIds) || !$productIds){
		continue;
	  }  
	  $data = $this->_getReviewSummary($productIds,$storeId);
	  if(!$data){
		continue;
	  } 
	  $product->setData('rating_summary',new Varien_Object());
	  $product->getRatingSummary()->setReviewsCount($data->getReviewsCount());
	  $product->getRatingSummary()->setRatingSummary($data->getRatingSummary());   
	}
	return;  
  }  
  
  protected function _getReviewSummary($productIds,$storeId=0)
  {
	$productIds = !is_array($productIds) ? array($productIds) : $productIds;
	$storeId = !is_array($storeId) ? array($storeId) : $storeId;
	
	$summaryModel = Mage::getModel('review/review_summary');
	$collection = $summaryModel->getCollection()
					->addFieldToFilter('entity_pk_value',array('in' => $productIds))
					->addFieldToFilter('store_id',array('in' => $storeId));
	$collection->getSelect()->columns('SUM(reviews_count) AS reviews_count');
	$collection->getSelect()->columns('ROUND((SUM(rating_summary)/COUNT(rating_summary)),0) AS rating_summary');
	$collection->getSelect()->group('store_id');

	$data = $collection->getFirstItem();  
	return $data;
  }
  
  public function fixUrl($observer)
  {
	$block = $observer->getBlock();
	if(!$block instanceof CommerceExtensions_BetterReviews_Block_Review_Helper){
	  return;
	}
	
	$transport = $observer->getTransport();
	$html = $transport->getHtml();
	
	$reviewsSuffix = CommerceExtensions_BetterReviews_Model_Config::REVIEWS_HASH_SUFFIX;
	$formSuffix = "#review-form";
	
	$html = str_replace($reviewsSuffix.$formSuffix,$formSuffix,$html);
	$transport->setHtml($html);	
	return;	
  }
  
  public function addRichSnippets($observer)
  {
	$block = $observer->getBlock();
	if(!$block instanceof Mage_Review_Block_Helper &&
	   !$block instanceof CommerceExtensions_BetterReviews_Block_Review_Helper){
	  return;
	}
	
	if(!Mage::registry('current_product')){
	  return;
	}
	
	if(!Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_RICHSNIPPETS_ENABLED)){
	  return;
	}
	
	$product = $block->getProduct();
	$mainProduct = Mage::registry('current_product');
	if($product->getId() != $mainProduct->getId()){
	  // we do this here to make sure that we only make changes
	  // on the main product's review summary block but not for other blocks
	  // containing products on the page
	  return;
	}
	
	// now that we have conducted all checks, we have our block isolated and do what is necessary below this line	
	$block->setAddRichSnippets(true);
	$template = basename($block->getTemplate());
	if($template == 'summary.phtml' || $template == 'summary_short.phtml'){
	  $block->setTemplate("betterreviews/{$template}");
	}
	return;		  
  } 
  
  public function setShortSummaryTemplate($observer)
  {
	$block = $observer->getBlock();
	if(!$block instanceof Mage_Review_Block_Helper &&
	   !$block instanceof CommerceExtensions_BetterReviews_Block_Review_Helper){
	  return;
	}
	
	if(!Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_PRODUCTPAGEREVIEWS_ENABLED)){
	  return;
	}
			
	$block->addTemplate('short','betterreviews/summary_short.phtml');
  }
}