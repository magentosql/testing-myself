<?php
/**
 * @category    Fishpig
 * @package    Fishpig_AttributeSplashPro
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_CPT_Block_Adminhtml_Type_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
	/**
	 * Set the tab block options
	 *
	 * @return void
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId('wp_addon_cpt_edit_tabs');
		$this->setDestElementId('edit_form');
		$this->setTitle($this->__('Custom Post Type'));
	}
	
	/**
	 * Add tabs to the tabs block
	 *
	 * @return $this
	 */
	protected function _beforeToHtml()
	{
		$this->addTab('page', array(
			'label' => $this->helper('adminhtml')->__('Type'),
			'title' => $this->helper('adminhtml')->__('Type'),
			'content' => $this->getLayout()->createBlock('wp_addon_cpt/adminhtml_type_edit_tab_type')->toHtml(),
		));
		

		$this->addTab('templates', array(
			'label' => $this->helper('adminhtml')->__('Templates'),
			'title' => $this->helper('adminhtml')->__('Templates'),
			'content' => $this->getLayout()->createBlock('wp_addon_cpt/adminhtml_type_edit_tab_template')->toHtml(),
		));
		
		return parent::_beforeToHtml();
	}
}
