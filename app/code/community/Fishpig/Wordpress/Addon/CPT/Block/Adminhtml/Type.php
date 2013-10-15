<?php
/**
 * @category    Fishpig
 * @package    Fishpig_AttributeSplashPro
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_CPT_Block_Adminhtml_Type extends Mage_Adminhtml_Block_Widget_Grid_Container
{
	/**
	 * Set the block options
	 *
	 * @return void
	 */
	public function __construct()
	{	
		parent::__construct();		
                
		$this->_controller = 'adminhtml_type';
		$this->_blockGroup = 'wp_addon_cpt';
		$this->_headerText = $this->__('Manage Custom Post Types');
		$this->_addButtonLabel = $this->__('Add New Post Type');
	}
}