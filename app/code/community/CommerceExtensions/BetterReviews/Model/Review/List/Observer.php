<?php

class CommerceExtensions_BetterReviews_Model_Review_List_Observer extends Varien_Event_Observer
{
  public function updateBlockTitle($observer)
  {
	$block = $observer->getBlock();
	if(!$block instanceof Mage_Review_Block_Product_View_List &&
	   !$block instanceof CommerceExtensions_BetterReviews_Block_Review_Product_View_List){
	  return;
	}
	
	if(!Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_UPDATE_BLOCK_TITLE_ENABLED)){
	  return;
	}
	
	$product = $block->getProduct();

	$transport = $observer->getTransport();
	$html = $transport->getHtml();
	
	$patternType = in_array($product->getTypeId(),array('grouped','configurable')) ? CommerceExtensions_BetterReviews_Model_Config::XML_PATH_UPDATE_BLOCK_TITLE_PATTERN_GROUPED : CommerceExtensions_BetterReviews_Model_Config::XML_PATH_UPDATE_BLOCK_TITLE_PATTERN_SIMPLE;
	$blockTitlePattern = Mage::getStoreConfig($patternType);
	if(!$blockTitlePattern || !strlen($blockTitlePattern)){
	  return;
	}
	
	$array = array(
	  '{{product_name}}' => $product->getName(),
	  '{{sku}}' => $product->getSku()
	);
	$originals = array_keys($array);
	$replacements = array_values($array);
	$title = str_ireplace($originals,$replacements,$blockTitlePattern);
	$title = Mage::helper('betterreviews')->__($title);

	$html = preg_replace("/<h2(.*?)>(.*?)<\/h2>/","<h2$1>{$title}</h2>",$html,1);
	$transport->setHtml($html);	
  }
  
  public function updateToolbarText($observer)
  {
	$block = $observer->getBlock();
	if(!$block instanceof Mage_Page_Block_Html_Pager){
	  return;
	}	
	
	if($block->getNameInLayout() != 'product_review_list.toolbar'){
	  return;
	}
	
	$transport = $observer->getTransport();
	$html = $transport->getHtml();	  
	$html = preg_replace('/Item/', 'Review', $html, 1);
	$transport->setHtml($html);	
  }
  
  public function addAttributeToReview($observer)
  {
	$block = $observer->getBlock();
	if(!$block instanceof Mage_Review_Block_Product_View_List &&
	   !$block instanceof CommerceExtensions_BetterReviews_Block_Review_Product_View_List){
	  return;
	}
	
	$product = $block->getProduct();	
	if(!in_array($product->getTypeId(),array('grouped','configurable'))){
	  return;
	}
	
	$canDisplay = Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_ASSOCIATED_PRODUCT_ATTRIBUTE_ENABLED);
	$attributeCode = Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_ASSOCIATED_PRODUCT_ATTRIBUTE);
	
	$block->setCanDisplayAttribute($canDisplay);
	if($canDisplay){
	  $block->setProductAttribute($attributeCode);
	  $block->setTemplate('betterreviews/list.phtml');
	}
	return;
  }
  
  public function addRichSnippets($observer)
  {
	$block = $observer->getBlock();
	if(!$block instanceof Mage_Review_Block_Product_View_List &&
	   !$block instanceof CommerceExtensions_BetterReviews_Block_Review_Product_View_List){
	  return;
	}
	
	if(!Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_RICHSNIPPETS_ENABLED)){
	  return;
	}
	$block->setAddRichSnippets(true);
	$block->setTemplate('betterreviews/list.phtml');	  
  }
  
  public function addAssociatedProductsReviews($observer)
  {
	$block = $observer->getBlock();
	if(!$block instanceof Mage_Review_Block_Product_View &&
	   !$block instanceof Mage_Review_Block_Product_View_List &&
	   !$block instanceof CommerceExtensions_BetterReviews_Block_Review_Product_View &&
	   !$block instanceof CommerceExtensions_BetterReviews_Block_Review_Product_View_List){
	  return;
	}		
	
	$product = $block->getProduct();
	$dataModel = Mage::getSingleton('betterreviews/data');
	$canInclude = $dataModel->getCanIncludeAssociatedProducts($product);
	if(!$canInclude){
	  return;
	}
	$collection = $dataModel->getAssociatedReviewsCollection($product);	
	
	$order = Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_REVIEWS_SORT_ORDER);
	$dir = Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_REVIEWS_SORT_DIR);

	if($order && $dir){	
	  $collection->getSelect()->reset(Zend_Db_Select::ORDER);	
	  $collection->getSelect()->order("$order $dir");	  
	}
	$block->setData('_reviewsCollection',$collection);
	return;
  }    
}