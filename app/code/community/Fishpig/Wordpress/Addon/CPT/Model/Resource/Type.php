<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Addon_CPT_Model_Resource_Type extends Mage_Core_Model_Resource_Db_Abstract
{
	public function _construct()
	{
		$this->_init('wp_addon_cpt/type', 'type_id');
	}
	
	/**
	 * Retrieve select object for load object data
	 * This gets the default select, plus the attribute id and code
	 *
	 * @param   string $field
	 * @param   mixed $value
	 * @return  Zend_Db_Select
	*/
	protected function _getLoadSelect($field, $value, $object)
	{
		$select = $this->_getReadAdapter()->select()
			->from(array('main_table' => $this->getMainTable()))
			->where("`main_table`.`{$field}` = ?", $value)
			->where('`main_table`.`post_type` NOT IN (?)', array('post', 'page', 'attachment', 'revision', 'nav_menu_item'))
			->limit(1);
			
		$adminId = (int)Mage_Core_Model_App::ADMIN_STORE_ID;
		$storeId = (int)$object->getStoreId();
		
		if ($storeId !== $adminId) {
			$select->join(
				array('store' => $this->getStoreTable()),
				$this->_getReadAdapter()->quoteInto("`store`.`{$this->getIdFieldName()}` = `main_table`.`{$this->getIdFieldName()}` AND `store`.`store_id` IN (?)", array($adminId, $storeId))
			);
			
			$select->order('store.store_id DESC');
		}

		return $select;
	}
	
	/**
	 * Function called after a model is loaded (but not when a collection of models are loaded)
	 * If filters set, unserialize (convert to an array)
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterLoad(Mage_Core_Model_Abstract $object)
	{
		if ($object->getId()) {
			$stores = $this->lookupStoreIds($object->getId());

			$object->setData('store_id', $stores);
		}
		
		return parent::_afterLoad($object);
	}
	
	/**
	 * Function called before a model is saved
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if (!$object->hasSlug()) {
			$object->setSlug($object->getPostType());
		}

		return parent::_beforeSave($object);
	}

	/**
	 * Function called after a model is saved
	 * Save store associations
	 *
	 * @param Mage_Core_Model_Abstract $object
	 * @return $this
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		if ($object->getId()) {
			$oldStores = $this->lookupStoreIds($object->getId());
			$newStores = (array)$object->getStores();
	
			if (empty($newStores)) {
				$newStores = (array)$object->getStoreId();
			}
	
			$table  = $this->getStoreTable();
			$insert = array_diff($newStores, $oldStores);
			$delete = array_diff($oldStores, $newStores);
			
			if ($delete) {
				$this->_getWriteAdapter()->delete($table, array($this->getIdFieldName() . ' = ?' => (int) $object->getId(), 'store_id IN (?)' => $delete));
			}
			
			if ($insert) {
				$data = array();
			
				foreach ($insert as $storeId) {
					$data[] = array(
						$this->getIdFieldName()  => (int) $object->getId(),
						'store_id' => (int) $storeId
					);
				}

				$this->_getWriteAdapter()->insertMultiple($table, $data);
			}
		}
		
		return parent::_afterSave($object);
	}
	
	/**
	 * Get store ids to which specified item is assigned
	 *
	 * @param int $id
	 * @return array
	*/
	public function lookupStoreIds($pageId)
	{
		$select = $this->_getReadAdapter()->select()
			->from($this->getStoreTable(), 'store_id')
			->where($this->getIdFieldName() . ' = ?', (int)$pageId);
	
		return $this->_getReadAdapter()->fetchCol($select);
	}
	
	/**
	 * Retrieve the store table name
	 *
	 * @return string
	 */
	public function getStoreTable()
	{
		return $this->getTable('wp_addon_cpt/type_store');
	}
	
	/**
	 * Get a post based on it's URI
	 *
	 * @param string $uri
	 * @return false|Fishpig_Wordpress_Model_Post
	 */
	public function getPostByUri($uri)
	{	
		if (strpos($uri, '/') === false) {
			return false;
		}
		
		$uriParts = explode('/', $uri);
		
		if (count($uriParts) !== 2) {
			return false;
		}
		
		$type = Mage::getModel('wp_addon_cpt/type')
			->setStoreId(Mage::app()->getStore()->getId())
			->load($uriParts[0], 'slug');
			
		if ($type === false) {
			return false;
		}
		
		$posts = Mage::getResourceModel('wordpress/post_collection')
			->addIsPublishedFilter()
			->addPostTypeFilter($type->getPostType())
			->setPageSize(1)
			->addFieldToFilter('post_name', $uriParts[1])
			->load();

		if (count($posts) > 0) {
			return $posts->getFirstItem();
		}
		
		return false;
	}
		
	public function getPostByIdAndPostType($id, $query_types) {
		try {
			
			$posts = Mage::getResourceModel('wordpress/post_collection')
				 ->addPostTypeFilter($query_types)
				 ->addIsPublishedFilter()
				 ->setPageSize(1)
				 ->addFieldToFilter('ID', $id)
				 ->load();
			if (count($posts) > 0) return $posts->getFirstItem();
		} catch (Exception $e) {
		}
		
		return false;
	}
	
	/**
	 * Retrieve an array of post types
	 *
	 * @param int $storeId = null
	 * @param int $displayOnBlog = 1
	 * @return false|array
	 */
	public function getPostTypes($storeId = null, $displayOnBlog = 1)
	{
		$types = Mage::getResourceModel('wp_addon_cpt/type_collection')
			->addStoreFilter($storeId, true)
			->addDisplayOnBlogFilter($displayOnBlog)
			->load();
			
		return count($types) > 0 ? $types : false;
	}

	/**
	 * Retrieve an array of post types
	 *
	 * @param int $storeId = null
	 * @return false|array
	 */
	public function getAllPostTypes($storeId = null)
	{
		$types = Mage::getResourceModel('wp_addon_cpt/type_collection')
			->addStoreFilter($storeId, true)
			->load();
			
		return count($types) > 0 ? $types : false;
	}


}
