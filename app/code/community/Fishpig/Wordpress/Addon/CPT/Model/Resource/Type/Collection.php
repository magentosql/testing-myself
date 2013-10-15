<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Addon_CPT_Model_Resource_Type_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
	/**
	 * Init the entity type
	 *
	 */
	public function _construct()
	{
		$this->_init('wp_addon_cpt/type');

		$idFieldName = $this->getResource()->getIdFieldName();
		
		$this->_map['fields'][$idFieldName] = 'main_table.' . $idFieldName;
		$this->_map['fields']['store'] = 'store_table.store_id';
	}
	
	/**
	 * Add filter by store
	 *
	 * @param int|Mage_Core_Model_Store $store
	 * @param bool $withAdmin
	 * @return Mage_Core_Model_Resource_Db_Collection_Abstract
	*/
	public function addStoreFilter($store, $withAdmin = true)
	{
		if (!$this->getFlag('store_filter_added')) {
			if ($store instanceof Mage_Core_Model_Store) {
				$store = array($store->getId());
			}
	
			if (!is_array($store)) {
				$store = array($store);
			}
	
			if ($withAdmin) {
				$store[] = Mage_Core_Model_App::ADMIN_STORE_ID;
			}
	
			$this->addFilter('store', array('in' => $store), 'public');
		}

		return $this;
	}

	/**
	 * Join store relation table if there is store filter
	 *
	 * @return $this
	*/
	protected function _renderFiltersBefore()
	{
		if ($this->getFilter('store')) {
			$resource = $this->getResource();
			
			$this->getSelect()->join(
				array('store_table' => $resource->getTable('wp_addon_cpt/type_store')),
				"`main_table`.`{$resource->getIdFieldName()}` = `store_table`.`{$resource->getIdFieldName()}`",
				array()
			)->group('main_table.' . $resource->getIdFieldName());
		}

		return parent::_renderFiltersBefore();
	}
	
	/**
	 * Filter the collection by the display_on_blog field
	 *
	 * @param int $value = 1
	 * @return $this
	 */
	public function addDisplayOnBlogFilter($value = 1)
	{
		return $this->addFieldToFilter('display_on_blog', $value);
	}
}
