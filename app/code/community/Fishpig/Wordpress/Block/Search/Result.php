<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Search_Result extends Fishpig_Wordpress_Block_Post_List_Wrapper_Abstract
{
	/**
	 * Generates and returns the collection of posts
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Collection
	 */
	protected function _getPostCollection()
	{
		if (is_null($this->_postCollection)) {
			$helper = Mage::helper('wordpress/search');
                      
			$this->_postCollection = parent::_getPostCollection()
				->addSearchStringFilter($helper->getParsedSearchString(), $helper->getSearchableFields(), $helper->getLogicalOperator());
			
			if ("asksearch" != Mage::app()->getRequest()->getControllerName())
				$this->_postCollection->addPostTypeFilter(array('howto', 'post', 'welovepost', 'byfabric', 'materials', 'as-seen-in', 'steps-fabric', 'speciality-resource'));
			else
				$this->_postCollection->addPostTypeFilter(array('question'));
		}
		
		return $this->_postCollection;
	}
	
	/**
	 * Retrieve the custom no result text
	 * 
	 * @return string
	 */
	public function getNoResultText()
	{
		return Mage::helper('wordpress')->__($this->getData('no_result_text'));
	}
}
