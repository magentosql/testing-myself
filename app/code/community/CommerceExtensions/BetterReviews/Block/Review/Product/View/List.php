<?php

class CommerceExtensions_BetterReviews_Block_Review_Product_View_List extends CommerceExtensions_BetterReviews_Block_Review_Product_View
{
  protected $_forceHasOptions = false;

  public function getProductId()
  {
	return Mage::registry('product')->getId();
  }

  protected function _prepareLayout()
  {
	parent::_prepareLayout();

	if ($toolbar = $this->getLayout()->getBlock('product_review_list.toolbar')) {
		$toolbar->setCollection($this->getReviewsCollection());
		$this->setChild('toolbar', $toolbar);
	}
	return $this;
  }

  protected function _beforeToHtml()
  {
	$this->getReviewsCollection()
		->load()
		->addRateVotes();
	return parent::_beforeToHtml();
  }

  public function getReviewUrl($id)
  {
	return Mage::getUrl('review/*/view', array('id' => $id));
  }	
  
  public function getReviewedItem($productId)
  {
	$dataId = "product_{$productId}";
	if(!$this->hasData($dataId)){
	  $product = Mage::getModel('catalog/product')->load($productId);
	  $this->setData($dataId,$product);
	}
	return $this->getData($dataId);
  }
}