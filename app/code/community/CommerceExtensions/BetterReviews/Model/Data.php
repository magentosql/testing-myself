<?php

class CommerceExtensions_BetterReviews_Model_Data extends Mage_Core_Model_Abstract
{
  public function getHandles()
  {
	if(!$this->hasData('_handles')){
	  $handles = Mage::app()->getLayout()->getUpdate()->getHandles();	  
	  $this->setData('_handles',$handles);
	}
	return $this->getData('_handles');	
  }
  
  public function getFullActionName()
  {
	if(!$this->hasData('_action')){
	  $action = Mage::app()->getFrontController()->getAction()->getFullActionName();	  
	  $this->setData('_action',$action);
	}
	return $this->getData('_action');	
  }
  
  public function getChildIds($product,$includeParentId = true)
  {
	$productId = $product->getId();
	$dataId = "_child_ids_{$productId}";
	if(!$this->hasData($dataId)){
		
	  $productType = $product->getTypeId();
	  if(!in_array($productType,array('grouped','configurable'))){
		return array($productId);
	  }		
		
	  $model = Mage::getModel("catalog/product_type_{$productType}");
	  $childIdsArray = $model->getChildrenIds($productId);
	  $key = key($childIdsArray);
	  $childIds = $childIdsArray[$key];
	  if($includeParentId) {
		array_push($childIds,$productId);
	  }
	  $childIds = array_unique($childIds);
	  $this->setData($dataId,$childIds);
	}
	return $this->getData($dataId);
  }
  
  public function getCanIncludeAssociatedProducts($product)
  {
	if(!Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_ASSOCIATED_PRODUCT_REVIEWS_ENABLED)){
	  return false;
	}	  
	
	if(!$product || !in_array($product->getTypeId(),array('grouped','configurable'))){
	  return false;
	}	
	
	$productType = $product->getTypeId();
	$xmlPath = $productType == 'grouped' ? CommerceExtensions_BetterReviews_Model_Config::XML_PATH_GROUPED_PRODUCT_REVIEWS_ENABLED : CommerceExtensions_BetterReviews_Model_Config::XML_PATH_CONFIGURABLE_PRODUCT_REVIEWS_ENABLED;
	
	if(!Mage::getStoreConfig($xmlPath)){
	  return false;
	}			
	return true;
  }
  
  public function getAssociatedReviewsCollection($product,$includeParentId = true)
  {
	// includes parent product and associated products reviews
	$productId = $product->getId();
	$dataId = "_reviews_{$productId}";
	if(!$this->hasData($dataId)){
		
	  // this collection is meant to be used in place of Mage_Review_Block_Product_View::getReviewsCollection();	 			
	  $collection = Mage::getModel('review/review')->getCollection()
		  ->addStoreFilter(Mage::app()->getStore()->getId())
		  ->addStatusFilter(Mage_Review_Model_Review::STATUS_APPROVED)
		  ->addFieldToFilter('entity_pk_value',array('in' => $this->getChildIds($product,$includeParentId)))
		  ->setDateOrder();		  
	  $this->setData($dataId,$collection);
	}
	return $this->getData($dataId);
  }
}