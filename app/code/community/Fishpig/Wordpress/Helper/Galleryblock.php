<?php

class Fishpig_Wordpress_Helper_Galleryblock extends Fishpig_Wordpress_Helper_Abstract {

	public function getForPost($postType, $postId) {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = sprintf("SELECT post_id FROM `%spostmeta` where meta_value=%d and meta_key='_wpcf_belongs_%s_id'",
				$dbInfo->getTablePrefix(),
				(int)$postId,
				$postType
			);
		$query_result = $table->query($query);
		
		$result = array();
		while ($row = $query_result->fetch()) {
			$result_item = Mage::getResourceModel('wp_addon_cpt/type')->getPostByIdAndPostType($row['post_id'], array('gallery-block'));
				
			if ($result_item != false) array_push($result, $result_item);
		}
		return $result;	
	}
	
	public function getDescription($post) {
		return $this->getKey($post->getId(), 'wpcf-gallery-block-description');
	}
	
	public function getReadMoreURL($post) {
		return $this->getKey($post->getId(), 'wpcf-read-more-url');
	}
	
	public function getShopNowURL($post) {
		return $this->getKey($post->getId(), 'wpcf-shop-now-url');
	}
	
	public function getImage($post) {
		return $this->getKey($post->getId(), 'wpcf-hot-linked-image');
	}
	
	private function getKey($postId, $key) {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = sprintf("SELECT meta_value FROM `%spostmeta` where meta_key = :key and post_id = :postId",
				$dbInfo->getTablePrefix()
			);
		$query_result = $table->query($query, array(
					":key" => $key,
					":postId" => $postId
				));
		
		while ($row = $query_result->fetch()) {
			return $row['meta_value'];
		}
		
		return false;
	}
	

}
