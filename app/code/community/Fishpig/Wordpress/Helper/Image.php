<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Image extends Fishpig_Wordpress_Helper_Abstract
{
	
	function getImageSizeByURL($url, $size) {
			$dbInfo = Mage::helper('wordpress/database');
			$dataHelper = Mage::helper('wordpress/data');
			
			$dbInfo->connect();
			$table = $dbInfo->getReadAdapter();
			$query = sprintf("SELECT wposts.ID FROM %sposts wposts, %spostmeta wpostmeta WHERE wposts.ID = wpostmeta.post_id AND wpostmeta.meta_key = '_wp_attached_file' AND  wposts.post_type = 'attachment' AND (meta_value = :url OR guid = :url)",
				$dbInfo->getTablePrefix(), $dbInfo->getTablePrefix());
			
			$url = preg_replace( '/-\d+x\d+(?=\.(jpg|jpeg|png|gif)$)/i', '', $url );
			$url = str_replace($dataHelper->getFileUploadUrl(), '', $url );			                        
                        
			$query_result = $table->query($query, array(":url" => $url));
			$rawdata = $query_result->fetch();
			if (!$rawdata) return false;
			return $this->getImageSizeWithID($rawdata['ID'], $size);
	}
	
	private function getImageSizeWithID($imgId, $thumbnail) {
		
		try {
			$dbInfo = Mage::helper('wordpress/database');
			$dbInfo->connect();
			$table = $dbInfo->getReadAdapter();
			$query = sprintf("SELECT post.meta_value FROM `%spostmeta` as post LEFT JOIN `%spostmeta` as image ON post.meta_value = image.post_id WHERE post.post_id = %s  AND post.meta_key='_wp_attachment_metadata'",
					  $dbInfo->getTablePrefix(), $dbInfo->getTablePrefix(), $imgId);

			$query_result = $table->query($query);
			$rawdata = $query_result->fetch();
			$val = unserialize($rawdata['meta_value']);                        
                                                
                        if($val['width'] == 230 && ($thumbnail == 'thumbnail' || $thumbnail == 'post-list-thumb-weloveitem')) {
                            $file = $val['file'];
                        } else {                                
                            $path = dirname($val['file']);
                            $file = $val['sizes'][$thumbnail]['file'];
                        }
			if (!$file) return false;
		} catch (Exception $e) { return false; }
		
		$dataHelper = Mage::helper('wordpress/data');
		return $dataHelper->getFileUploadUrl() . $path . '/' . $file;
		
	}
	
}
