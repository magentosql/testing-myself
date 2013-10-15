<?php
/**
 * @category    Fishpig
 * @package    Fishpig_AttributeSplashPro
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_CPT_Block_Adminhtml_Type_Edit_Tab_Template extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Prepare the form
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('cpt_');
        $form->setFieldNameSuffix('cpt');
        
		$this->setForm($form);
		
		$fieldset = $form->addFieldset('wp_addon_cpt_template', array(
			'legend'=> $this->helper('adminhtml')->__('Templates'),
			'class' => 'fieldset-wide',
		));
		
		$fieldset->addField('post_list_template', 'text', array(
			'name' => 'post_list_template',
			'label' => $this->helper('adminhtml')->__('List Template'),
			'title' => $this->helper('adminhtml')->__('List Template'),
			'note' => $this->__('eg. wordpress/post/list/renderer/default.phtml'),
		));
		
		$fieldset->addField('post_view_template', 'text', array(
			'name' => 'post_view_template',
			'label' => $this->helper('adminhtml')->__('View Template'),
			'title' => $this->helper('adminhtml')->__('View Template'),
			'note' => $this->__('eg. wordpress/post/view/renderer/default.phtml'),
		));
		
		$form->setValues($this->_getFormData());

		return parent::_prepareForm();
	}
	
	/**
	 * Retrieve the data used for the form
	 *
	 * @return array
	 */
	protected function _getFormData()
	{
		if (($object = Mage::registry('wordpress_post_type')) !== null) {
			return $object->getData();
		}

		return array(
			'is_public' => 1,
			'has_archive' => 1,
			'store_id' => array(0),
		);
	}
}
