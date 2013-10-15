<?php
/**
 * @category    Fishpig
 * @package    Fishpig_AttributeSplashPro
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_CPT_Block_Adminhtml_Type_Edit  extends Mage_Adminhtml_Block_Widget_Form_Container
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
		$this->_headerText = $this->_getHeaderText();
		
		$this->_addButton('save_and_edit_button', array(
			'label' => Mage::helper('catalog')->__('Save and Continue Edit'),
			'onclick' => 'editForm.submit(\''. $this->getUrl('*/*/save', array('_current' => true, 'back' => 'edit')) .'\')',
			'class' => 'save',
		));
		
		$this->_removeButton('reset');
	}
    
    /**
     * Retrieve the header text
     * If splash page exists, use name
     *
     * @return string
     */
	protected function _getHeaderText()
	{
		if (($object = Mage::registry('wordpress_post_type')) !== null) {
			return $this->__("Edit Custom Post Type '%s'", $object->getLabel());
		}
	
		return $this->__('New Custom Post Type');
	}
}
