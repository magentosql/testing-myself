<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_AsksearchController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	  * Initialise the current category
	  */
	public function indexAction()
	{
		$this->_rootTemplates[] = 'template_post_list';
		
		$this->_addCustomLayoutHandles(array(
			'wordpress_asksearch_index',
		));
		
		$this->_initLayout();
		
		$helper = Mage::helper('wordpress/search');
		$routerHelper = $this->getRouterHelper();
		
		if ($searchValue = $routerHelper->getTrimmedUri('ask-search')) {
			$this->getRequest()->setParam($helper->getQueryVarName(), $searchValue);
		}
		
		$this->_title($this->__("Search results for: '%s'", $helper->getEscapedSearchString()));
		
		$this->addCrumb('search_label', array('link' => '', 'label' => $this->__('Search')));
		$this->addCrumb('search_value', array('link' => '', 'label' => $helper->getEscapedSearchString()));
		
		$this->renderLayout();
	}
	
	public function askAction() {
		if (!Mage::helper('customer')->isLoggedIn()) {
			$this->getResponse()->setHttpResponseCode(403);
			return;
		}
		
		$title = Mage::app()->getRequest()->getParam('title');
		$text = Mage::app()->getRequest()->getParam('text');
		
		$customer = Mage::getSingleton('customer/session')->getCustomer();
		$name = $customer->getName();
		$email = $customer->getEmail();
		if (null==$name) $name = "";
		if (null==$email) $email = "";
		
		if (empty($title) || empty($text)) {
			$this->getResponse()->setHttpResponseCode(500);
			return;
		}
		$post = Mage::getModel('wordpress/post_comment');
		$post->setData('comment_post_ID', '6987');
		$post->setCommentAuthor($name);
		$post->setCommentAuthorEmail($email);
		$post->setCommentContent("Title: ".$title."\nText: ".$text);
		$post->setData('comment_author_IP', Mage::helper('core/http')->getRemoteAddr(false));
		$post->setData('comment_date', date('Y-m-d G:i:s'));
		$post->setData('comment_date_gmt', gmdate('Y-m-d G:i:s'));
		$post->setCommentApproved(0);
		$post->save();
	}
}
