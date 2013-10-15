<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_CPT_Block_Type_View extends Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract
{
	/**
	 * Returns the current Wordpress category
	 * This is just a wrapper for getCurrentCategory()
	 *
	 * @return Fishpig_Wordpress_Model_Post_Categpry
	 */
	public function getPostType()
	{
		return Mage::registry('wordpress_post_type');
	}
		
	/**
	 * Generates and returns the collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected function _getPostCollection()
	{
		if ($this->getPostType()) {
			return $this->getPostType()->getPostCollection();
		}
		
		return false;
	}
}
