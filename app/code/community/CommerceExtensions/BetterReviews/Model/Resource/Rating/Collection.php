<?php

class CommerceExtensions_BetterReviews_Model_Resource_Rating_Collection extends Mage_Rating_Model_Resource_Rating_Collection
{	    
  public function addEntitySummaryToItem($entityPkValue, $storeId)
  {
	// WE ONLY UPDATED THIS FUNCTION TO ACCEPT $entityPkValue AS BOTH AN ARRAY AND A STRING	
	$arrRatingId = $this->getColumnValues('rating_id');
	if (count($arrRatingId) == 0) {
		return $this;
	}

	$adapter = $this->getConnection();

	$inCond = $adapter->prepareSqlCondition('rating_option_vote.rating_id', array(
		'in' => $arrRatingId
	));
	$inProductIds = $adapter->prepareSqlCondition('rating_option_vote.entity_pk_value', array(
		'in' => $entityPkValue
	));			

	$sumCond = new Zend_Db_Expr("SUM(rating_option_vote.{$adapter->quoteIdentifier('percent')})");
	$countCond = new Zend_Db_Expr('COUNT(*)');
	$select = $adapter->select()
		->from(array('rating_option_vote'  => $this->getTable('rating/rating_option_vote')),
			array(
				'rating_id' => 'rating_option_vote.rating_id',
				'sum'         => $sumCond,
				'count'       => $countCond
			))
		->join(
			array('review_store' => $this->getTable('review/review_store')),
			'rating_option_vote.review_id=review_store.review_id AND review_store.store_id = :store_id',
			array())
		->join(
			array('rst' => $this->getTable('rating/rating_store')),
			'rst.rating_id = rating_option_vote.rating_id AND rst.store_id = :rst_store_id',
			array())
		->join(array('review'              => $this->getTable('review/review')),
			'review_store.review_id=review.review_id AND review.status_id=1',
			array())
		->where($inCond)
		->where($inProductIds)
		->group('rating_option_vote.rating_id');
	$bind = array(
		':store_id' => (int)$storeId,
		':rst_store_id' => (int)$storeId,
	);
	$data = $this->getConnection()->fetchAll($select, $bind);
	foreach ($data as $item) {
		$rating = $this->getItemById($item['rating_id']);
		if ($rating && $item['count']>0) {
			$rating->setSummary($item['sum']/$item['count']);
		}
	}
	return $this;
  }  
}