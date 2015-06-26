<?php
// EVEN THOUGH THIS IS A CLASS REWRITE OF Mage_Rating_Block_Entity_Detailed, WE DO NOT EXTEND THAT CLASS OR IT THROWS THINGS OFF
class CommerceExtensions_BetterReviews_Block_Rating_Entity_Detailed extends Mage_Core_Block_Template
{
  public function __construct()
  {
	parent::__construct();
	$this->setTemplate('rating/detailed.phtml');
  }
				
  protected function _toHtml()
  {
	$entityId = Mage::app()->getRequest()->getParam('id');
	if (intval($entityId) <= 0) {
		return '';
	}
	
	$product = ($product = Mage::registry('current_product')) ? $product : Mage::getModel('catalog/product')->load($entityId);
	$dataModel = Mage::getSingleton('betterreviews/data');
	$canInclude = $dataModel->getCanIncludeAssociatedProducts($product);
	if(!$canInclude){
	  return $this->_parentToHtml();
	}	

	$entityId = $dataModel->getChildIds($product);
	if(empty($entityId) || !$entityId){
	  return $this->_parentToHtml();
	}	

	$reviewsCount = Mage::getModel('review/review')
		->getTotalReviews($entityId, true);
		
	if ($reviewsCount == 0) {
	  #return Mage::helper('rating')->__('Be the first to review this product');
	  $this->setTemplate('rating/empty.phtml');
	  return $this->_parentToHtml();
	}

	$ratingCollection = Mage::getModel('rating/rating')
		->getResourceCollection()
		->addEntityFilter('product') # TOFIX
		->setPositionOrder()
		->setStoreFilter(Mage::app()->getStore()->getId())
		->addRatingPerStoreName(Mage::app()->getStore()->getId())
		->load();

	if ($entityId) {
	  $ratingCollection->addEntitySummaryToItem($entityId, Mage::app()->getStore()->getId());
	}

	$this->assign('collection', $ratingCollection);
	return parent::_toHtml();
  }
	
  protected function _parentToHtml()
  {
	  $entityId = Mage::app()->getRequest()->getParam('id');
	  if (intval($entityId) <= 0) {
		  return '';
	  }

	  $reviewsCount = Mage::getModel('review/review')
		  ->getTotalReviews($entityId, true);
	  if ($reviewsCount == 0) {
		  #return Mage::helper('rating')->__('Be the first to review this product');
		  $this->setTemplate('rating/empty.phtml');
		  return parent::_toHtml();
	  }

	  $ratingCollection = Mage::getModel('rating/rating')
		  ->getResourceCollection()
		  ->addEntityFilter('product') # TOFIX
		  ->setPositionOrder()
		  ->setStoreFilter(Mage::app()->getStore()->getId())
		  ->addRatingPerStoreName(Mage::app()->getStore()->getId())
		  ->load();

	  if ($entityId) {
		  $ratingCollection->addEntitySummaryToItem($entityId, Mage::app()->getStore()->getId());
	  }

	  $this->assign('collection', $ratingCollection);
	  return parent::_toHtml();	
  }
}