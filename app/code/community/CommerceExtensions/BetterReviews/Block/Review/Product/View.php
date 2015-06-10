<?php

class CommerceExtensions_BetterReviews_Block_Review_Product_View extends Mage_Review_Block_Product_View
{
  public function getReviewsCollection()
  {
	// THIS REWRITTEN FUNCTION DOES NOT CHANGE THE CORE FUNCTION, I JUST ADDED THE ->setData() METHOD SO THAT AN OBSERVER CAN CHANGE THE COLLECTION
	if (!$this->hasData('_reviewsCollection')) {
		$collection = parent::getReviewsCollection();
		$this->setData('_reviewsCollection',$collection);
	}
	return $this->getData('_reviewsCollection');
  }	
}