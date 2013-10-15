<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Addon_CPT_Model_Type extends Mage_Core_Model_Abstract
{
	protected $_collection = null;
	
	public function _construct()
	{
            
		$this->_init('wp_addon_cpt/type');
	}
	
	/**
	 * Retrieve the URL to the cpt page
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return Mage::helper('wordpress')->getUrl($this->getSlug()) . '/';
	}
	
	public function getPostCollection()
	{
		if (is_null($this->_collection)) {
			$this->_collection = Mage::getResourceModel('wordpress/post_collection')
				->addPostTypeFilter($this->getPostType());
				
			if ("fabric" == $this->getPostType() || "material" == $this->getPostType()) {
				if (null != ($fabric = Mage::getSingleton('core/app')->getRequest()->getParam('fabric'))
					&& ctype_alpha($fabric) && 1 == strlen($fabric)) {
					$this->_collection->addFieldToFilter('post_title', 
						array('like' => $fabric . '%'));
				}
				if (null != ($material = Mage::getSingleton('core/app')->getRequest()->getParam('material'))
					&& ctype_alpha($material) && 1 == strlen($material)) {
					$this->_collection->addFieldToFilter('post_title', 
						array('like' => $material . '%'));
				}
				$this->_collection->setOrder('post_title', 'asc');
			}
		}	
		
		return $this->_collection;	
	}
}
