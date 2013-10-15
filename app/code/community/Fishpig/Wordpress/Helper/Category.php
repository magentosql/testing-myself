<?php

class Fishpig_Wordpress_Helper_Category extends Fishpig_Wordpress_Helper_Abstract {
	
	public function getCategory($catId) {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = sprintf("SELECT t.term_id, t.parent, p.name, p.slug FROM %sterms as p left join %sterm_taxonomy as t on p.term_id = t.term_id WHERE t.term_id = :catId", 
				$dbInfo->getTablePrefix(), 
				$dbInfo->getTablePrefix()
				);
		$query_result = $table->query($query, array(":catId" => $catId));
		return $query_result->fetch();
	}

	public function getSubCategories($catId) {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = sprintf("SELECT t.term_id, t.parent, p.name, p.slug FROM %sterms as p left join %sterm_taxonomy as t on p.term_id = t.term_id WHERE t.parent= :catId", 
				$dbInfo->getTablePrefix(), 
				$dbInfo->getTablePrefix()
				);
		$query_result = $table->query($query, array(":catId" => $catId));

		$result = array();
		while ($row = $query_result->fetch()) {
			array_push($result, $row);
		}
		return $result;
	}
	
	public function getRelatedPosts($catId, $postId, $count) {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = sprintf("SELECT p.* FROM %sterm_relationships AS t LEFT JOIN %sposts AS p ON t.object_id = p.id WHERE t.term_taxonomy_id = :postId AND t.object_id != :catId AND p.post_status = 'publish' GROUP BY t.object_id ORDER BY RAND( ) LIMIT 0 , %d",
				$dbInfo->getTablePrefix(), 
				$dbInfo->getTablePrefix(),
				(int) $count
				);
		$query_result = $table->query($query, array(":catId" => $catId, ":postId" => $postId));
				
		$result = array();
		while ($row = $query_result->fetch()) {
			array_push($result, $row);
		}
		return $result;
	}
	
	public function getAllPosts($tagId) {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = sprintf("SELECT p.id FROM  `%sterm_relationships` AS t LEFT JOIN %sposts AS p ON t.object_id = p.id WHERE t.`term_taxonomy_id` = :tagId and p.post_status = 'publish'",
				$dbInfo->getTablePrefix(), 
				$dbInfo->getTablePrefix()
				);
		$query_result = $table->query($query, array(":tagId" => $tagId));
				
		$result = array();
		while ($row = $query_result->fetch()) {
			array_push($result, Mage::getModel('wordpress/post')->load($row['id']));
		}
		return $result;
	}

	public function getAllPostsByTag($tagId) {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = sprintf("SELECT d.id, d.post_type, c.slug, d.post_name FROM %sterm_relationships a left join %sterm_taxonomy b on a.term_taxonomy_id=b.term_taxonomy_id left join %sterms c on b.term_id=c.term_id left join %sposts d on a.object_id = d.id WHERE c.term_id = :tagId AND d.post_status='publish'",
				$dbInfo->getTablePrefix(), 
				$dbInfo->getTablePrefix(), 
				$dbInfo->getTablePrefix(), 
				$dbInfo->getTablePrefix()
				);
		$query_result = $table->query($query, array(":tagId" => $tagId));
		$result = array();
		while ($row = $query_result->fetch()) {
			if ($row['post_type'] == 'post') $result_item = Mage::getModel('wordpress/post')->load($row['id']);
			else $result_item = Mage::getResourceModel('wp_addon_cpt/type')->getPostByIdAndPostType($row['id'], $row['post_type']);
			
			if ($result_item != false) array_push($result, $result_item);
		}
		return $result;
	}

	public function getCurrentContext() {
		$urlParser = parse_url(Mage::helper('core/url')->getCurrentUrl());
		$pathInfo = $urlParser['path'];
		$urlCurrent = array_reverse(explode('/', $pathInfo));
		foreach ($urlCurrent as $url) {
			if ($url == "") continue;
			if (($slug = $this->getSlugId($url)) != NULL) return $slug;
		}
		return -1;
	}

	public function getSlugId($slug) {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = sprintf("SELECT a.term_id as id FROM %sterms a left join %sterm_taxonomy b on a.term_id=b.term_id where slug = :slug",
				$dbInfo->getTablePrefix(),
				$dbInfo->getTablePrefix()
				);
		$query_result = $table->query($query, array(":slug" => $slug));
		$row = $query_result->fetch();
		return $row['id'];
	}
	
	public function getStepsFor($section, $postId) {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = sprintf("SELECT pm.post_id FROM `%spostmeta` as pm RIGHT OUTER JOIN `%spostmeta` as pmo ON pm.post_id = pmo.post_id AND pmo.meta_key='wpcf-step-order' where pm.meta_value=%d AND pm.meta_key='_wpcf_belongs_%s_id' ORDER BY cast(pmo.meta_value as unsigned) ASC",
			$dbInfo->getTablePrefix(),
			$dbInfo->getTablePrefix(),
			(int)$postId,
			$section
			);
		$query_result = $table->query($query);
		
		$result = array();
		while ($row = $query_result->fetch()) {
			$result_item = Mage::getResourceModel('wp_addon_cpt/type')->getPostByIdAndPostType($row['post_id'], array('steps-fabric', 'steps-material', 'step-how-to'));
				
			if ($result_item != false) array_push($result, $result_item);
		}
		return $result;
		
	}
	
	public function filterByTermId($list, $id) {
		foreach ($list as $cat) {
			if ($cat['term_id'] == $id) return $cat;
		}
		return false;
	}
	
	public function getAllPostsByPostType($type) {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = sprintf("SELECT id,post_type from %sposts WHERE post_type = :type AND post_status='publish'",
				$dbInfo->getTablePrefix()
				);
		$query_result = $table->query($query, array(":type" => $type));
		$result = array();
		while ($row = $query_result->fetch()) {
			$result_item = Mage::getResourceModel('wp_addon_cpt/type')->getPostByIdAndPostType($row['id'], $row['post_type']);
			if ($result_item != false) array_push($result, $result_item);
		}
		return $result;
	}
	
	public function getYearList() {
		$dbInfo = Mage::helper('wordpress/database');
		$dbInfo->connect();
		$table = $dbInfo->getReadAdapter();
		$query = "SELECT *, DATE_FORMAT(post_date, '%Y') as year FROM " 
		       . $dbInfo->getTablePrefix() . "posts"
		       . " GROUP BY year ORDER BY year DESC";
	        $query_result = $table->query($query);
	        $result = array();
	        while ($row = $query_result->fetch()) {
	        	array_push($result, $row['year']);
	        }
	        return $result;
	}

}
