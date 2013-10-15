<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Addon_CPT_Model_Observer extends Varien_Object
{
	/**
	 * Attempt to match a WP route to a custom post type
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function matchRoutesObserver(Varien_Event_Observer $observer)
	{
		$router = $observer->getEvent()->getRouter();
		$uri = $observer->getEvent()->getUri();
		$post = $this->_getResource()->getPostByUri($uri);
		
		if ($post !== false) {
			Mage::register('wordpress_post', $post);

			$router->setRoutePath('*/post/view');
			
			return true;
		}
		
		if (strpos($uri, '/') === false) {
			$type = Mage::getModel('wp_addon_cpt/type')->load($uri, 'slug');
			
			if ($type->getId()) {
				Mage::register('wordpress_post_type', $type);
				
				$router->setRoutePath('*/post_customtype/view');
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Add custom post types to the collection
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function postCollectionLoadBeforeObserver(Varien_Event_Observer $observer)
	{
		$posts = $observer->getEvent()->getPosts();
		try {
			if (!$posts->hasPostTypeFilter() && $postTypes = $this->getPostTypesOnBlogArray(true)) {
				$keys = array_keys($postTypes);

				if ("index" == Mage::app()->getRequest()->getActionName() &&
				    "index" == Mage::app()->getRequest()->getActionName()) {
					unset($keys[array_search("material", $keys)]);
					unset($keys[array_search("speciality-resource", $keys)]);
					unset($keys[array_search("ingredients", $keys)]);
					unset($keys[array_search("fabric", $keys)]);
				    }
				    
				if (false === strpos(Mage::helper('core/url')->getCurrentUrl(), "ask-the-laundress")) {
				    unset($keys[array_search("question", $keys)]);
				}
				
				$posts->addPostTypeFilter($keys);
			}
			
			if (false !== strpos(Mage::helper('core/url')->getCurrentUrl(), "archive")) {
				$year_filter = Mage::app()->getRequest()->getParam('year');
				if ($year_filter) {
					$posts->addYearFilter((int)$year_filter);
				}
				
				$cat_filter = Mage::app()->getRequest()->getParam('category');
				if ($cat_filter) {
					$posts->addCategoryIdFilter($cat_filter);
				}
			}
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e);
		}
	}

	/**
	 * Add custom post types to the collection
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function postCollectionAdminLoadBeforeObserver(Varien_Event_Observer $observer)
	{
		$posts = $observer->getEvent()->getPosts();
		try {
			if (!$posts->hasPostTypeFilter() && $postTypes = $this->getPostTypesArray(true)) {
				$posts->addPostTypeFilter(array_keys($postTypes));
			}
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e);
		}
	}


	
	/**
	 * Add the URL to posts with custom post types
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function addDataToPostCollectionObserver(Varien_Event_Observer $observer)
	{
		try {
			if (($types = $this->getPostTypesArray(false)) !== false) {
				$collection = $observer->getEvent()->getPosts();
				$helper = Mage::helper('wordpress');
				
				foreach($collection as $post) {
					if (isset($types[$post->getPostType()])) {
						$this->prepareCustomPost($post, $types[$post->getPostType()]);
					}
				}
			}
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e);
		}
	}

	/**
	 * Add the URL to a post if it has a custom type
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function addDataToPostObserver(Varien_Event_Observer $observer)
	{
		try {
			if (($types = $this->getPostTypesArray(false)) !== false) {
				$post = $observer->getEvent()->getPost();
				$helper = Mage::helper('wordpress');
				
				if (isset($types[$post->getPostType()])) {
					$this->prepareCustomPost($post, $types[$post->getPostType()]);
				}
			}
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e);
		}
	}
	
	/**
	 * Prepare a custom post
	 *
	 * @param Fishpig_Wordpress_Model_Post_Abstract
	 * @param Fishpig_Wordpress_Addon_CPT_Model_Type $type
	 * @return $this
	 */
	public function prepareCustomPost($post, $type)
	{
		if ($post->getPostType() === $type->getPostType()) {
			$post->setPermalink(Mage::helper('wordpress')->getUrl($type->getSlug() . '/' . $post->getPostName() . '/'));
			$post->setPostListTemplate($type->getPostListTemplate());
			$post->setPostViewTemplate($type->getPostViewTemplate());
		}

		return $this;		
	}
	
	/**
	 * Retrieve an array of custom post types for this store
	 *
	 * @param bool $includeDefault = false
	 * @return array|false
	 */
	public function getPostTypesArray($includeDefault = false)
	{
		$filters = $includeDefault ? array('post' => 'post') : array();
		$types = $this->_getResource()->getAllPostTypes(Mage::app()->getStore()->getId());
		
		if ($types !== false) {
			foreach($types as $type) {
				$filters[$type->getPostType()] = $type;
			}
		}
		
		return count($filters) > 0 ? $filters : false;
	}


	/**
	 * Retrieve an array of custom post types which are displayed on the blog for this store
	 *
	 * @param bool $includeDefault = false
	 * @return array|false
	 */
	public function getPostTypesOnBlogArray($includeDefault = false)
	{

		$filters = $includeDefault ? array('post' => 'post') : array();
		$types = $this->_getResource()->getPostTypes(Mage::app()->getStore()->getId(),1);
		
		if ($types !== false) {
			foreach($types as $type) {
				$filters[$type->getPostType()] = $type;
			}
		}
		
		return count($filters) > 0 ? $filters : false;
	}

	
	/**
	 * Retrieve the resource model
	 *
	 * @return Fishpig_Wordpress_Addon_CPT_Model_Resource_Type
	 */
	protected function _getResource()
	{
		return Mage::getResourceModel('wp_addon_cpt/type');
	}
}
