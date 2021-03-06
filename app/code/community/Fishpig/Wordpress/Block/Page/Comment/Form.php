<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Page_Comment_Form extends Fishpig_Wordpress_Block_Post_View_Comment_Form
{
	/**
	 * Retrieve the page object
	 * This is overridden to save duplicating all post comment code
	 *
	 * @return Fishpig_Wordpress_Model_Page
	 */
	public function getPost()
	{
		return $this->getPage();
	}

	/**
	 * Retrieve the page object
	 *
	 * @return Fishpig_Wordpress_Model_Page
	 */	
	public function getPage()
	{
		return $this->_getData('page') ? $this->_getData('page') : Mage::registry('wordpress_page');
	}
	
	/**
	 * Retrieve the comment form action
	 *
	 * @return string
	 */
	public function getCommentFormAction()
	{
		return $this->helper('wordpress')->getUrl('post-comment') . '?is_page=1';
	}
}
