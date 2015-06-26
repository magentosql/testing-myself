<?php

class CommerceExtensions_BetterReviews_Block_Review_Helper extends Mage_Review_Block_Helper
{
  public function getReviewsUrl()
  {
	if(Mage::getStoreConfig(CommerceExtensions_BetterReviews_Model_Config::XML_PATH_PRODUCTPAGEREVIEWS_ENABLED)){
	  return $this->getProduct()->getProductUrl().CommerceExtensions_BetterReviews_Model_Config::REVIEWS_HASH_SUFFIX;
	}	  
	return parent::getReviewsUrl();
  }	
}