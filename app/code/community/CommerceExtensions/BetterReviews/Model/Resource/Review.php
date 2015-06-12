<?php

class CommerceExtensions_BetterReviews_Model_Resource_Review extends Mage_Review_Model_Resource_Review
{
  public function getTotalReviews($entityPkValue, $approvedOnly = false, $storeId = 0)
  {
	$adapter = $this->_getReadAdapter();
	$inProductIds = $adapter->prepareSqlCondition("{$this->_reviewTable}.entity_pk_value", array(
		'in' => $entityPkValue
	));		
	
	$select = $adapter->select()
		->from($this->_reviewTable,
			array(
				'review_count' => new Zend_Db_Expr('COUNT(*)')
			))
		->where($inProductIds);

	$bind = array();
	if ($storeId > 0) {
		$select->join(array('store'=>$this->_reviewStoreTable),
			$this->_reviewTable.'.review_id=store.review_id AND store.store_id = :store_id',
			array());
		$bind[':store_id'] = (int)$storeId;
	}
	if ($approvedOnly) {
		$select->where("{$this->_reviewTable}.status_id = :status_id");
		$bind[':status_id'] = 1;
	}
	return $adapter->fetchOne($select, $bind);
  }	
}