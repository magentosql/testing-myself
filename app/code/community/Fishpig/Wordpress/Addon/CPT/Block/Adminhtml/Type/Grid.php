<?php
/**
 * @category    Fishpig
 * @package    Fishpig_AttributeSplashPro
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */
 
class Fishpig_Wordpress_Addon_CPT_Block_Adminhtml_Type_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	/**
	 * Set the grid block options
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		
		$this->setId('wp_addon_cpt_grid');
		$this->setDefaultSort('type_id');
		$this->setDefaultDir('asc');
		$this->setSaveParametersInSession(false);
	}
	
	/**
	 * Initialise and set the collection for the grid
	 *
	 */
	protected function _prepareCollection()
	{
		$collection = Mage::getResourceModel('wp_addon_cpt/type_collection');

		$this->setCollection($collection);
	
		return parent::_prepareCollection();
	}
	
	/**
	 * Add store information to pages
	 *
	 * @return $this
	 */
	protected function _afterLoadCollection()
	{
		$this->getCollection()->walk('afterLoad');

		parent::_afterLoadCollection();
	}
	
	/**
	 * Apply the store filter
	 *
	 * @param $collection
	 * @param $column
	 * @return void
	 */
    protected function _filterStoreCondition($collection, $column)
    {
        if (!$value = $column->getFilter()->getValue()) {
            return;
        }

        $this->getCollection()->addStoreFilter($value);
    }
    
	/**
	 * Add the columns to the grid
	 *
	 */
	protected function _prepareColumns()
	{
		$this->addColumn('type_id', array(
			'header'	=> $this->__('ID'),
			'align'		=> 'left',
			'width'		=> '60px',
			'index'		=> 'type_id',
		));
		
		$this->addColumn('post_type', array(
			'header'	=> $this->__('Post Type'),
			'align'		=> 'left',
			'index'		=> 'post_type',
		));
		
		$this->addColumn('slug', array(
			'header'	=> $this->__('Slug'),
			'align'		=> 'left',
			'index'		=> 'slug',
		));
		
		if (!Mage::app()->isSingleStoreMode()) {
			$this->addColumn('store_id', array(
				'header'	=> $this->__('Store'),
				'align'		=> 'left',
				'index'		=> 'store_id',
                'type'          => 'store',
                'store_all'     => true,
                'store_view'    => true,
                'sortable'      => false,
                'filter_condition_callback' => array($this, '_filterStoreCondition'),
				'options' 	=> $this->getStores(),
			));
		}
	
		$this->addColumn('action',
			array(
				'width'     => '50px',
				'type'      => 'action',
				'getter'     => 'getId',
				'actions'   => array(
					array(
						'caption' => Mage::helper('catalog')->__('Edit'),
						'url'     => array(
						'base'=>'*/*/edit',
					),
					'field'   => 'id'
					)
				),
				'filter'    => false,
				'sortable'  => false,
				'align' 	=> 'center',
			));

		return parent::_prepareColumns();
	}

	/**
	 * Prepare the massaction block for deleting multiple pages
	 *
	 * @return $this
	 */
	protected function _prepareMassaction()
	{
		$this->setMassactionIdField('type_id');
		$this->getMassactionBlock()->setFormFieldName('type');
	
		$this->getMassactionBlock()->addItem('delete', array(
			'label'=> $this->__('Delete'),
			'url'  => $this->getUrl('*/*/massDelete'),
			'confirm' => Mage::helper('catalog')->__('Are you sure?')
		));
		
		return $this;
	}
	
	/**
	 * Retrieve the URL for the row
	 *
	 */
	public function getRowUrl($row)
	{
		return $this->getUrl('*/*/edit', array('id' => $row->getId()));
	}
	
	/**
	 * Retrieve an array of all of the stores
	 *
	 * @return array
	 */
	protected function getStores()
	{
		$options = array(0 => $this->__('Global'));
		$stores = Mage::getResourceModel('core/store_collection')->load();
		
		foreach($stores as $store) {
			$options[$store->getId()] = $store->getWebsite()->getName() . ' &gt; ' . $store->getName();
		}

		return $options;
	}
}
