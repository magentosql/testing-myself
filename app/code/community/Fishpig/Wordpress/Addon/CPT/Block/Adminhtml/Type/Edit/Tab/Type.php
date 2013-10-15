<?php
/**
 * @category    Fishpig
 * @package    Fishpig_AttributeSplashPro
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_CPT_Block_Adminhtml_Type_Edit_Tab_Type extends Mage_Adminhtml_Block_Widget_Form
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
		
		$fieldset = $form->addFieldset('wp_addon_cpt_type', array(
			'legend'=> $this->helper('adminhtml')->__('Type'),
		));
		
		$fieldset->addField('post_type', 'text', array(
			'name' => 'post_type',
			'label' => $this->helper('adminhtml')->__('Post Type'),
			'title' => $this->helper('adminhtml')->__('Post Type'),
			'required' => true,
			'class' => 'required-entry',
			'note' => $this->__('eg. article'),
		));
		
		$fieldset->addField('singular_name', 'text', array(
			'name' => 'singular_name',
			'label' => $this->helper('adminhtml')->__('Name'),
			'title' => $this->helper('adminhtml')->__('Name'),
			'required' => true,
			'class' => 'required-entry',
			'note' => $this->__('eg. Article'),
		));
		
		$fieldset->addField('name', 'text', array(
			'name' => 'name',
			'label' => $this->helper('adminhtml')->__('Name (Plural)'),
			'title' => $this->helper('adminhtml')->__('Name (Plural)'),
			'required' => true,
			'class' => 'required-entry',
			'note' => $this->__('eg. Articles'),
		));
		
		$fieldset->addField('slug', 'text', array(
			'name' => 'slug',
			'label' => $this->helper('adminhtml')->__('Rewrite Slug'),
			'title' => $this->helper('adminhtml')->__('Rewrite Slug'),
            'note' => Mage::helper('wordpress')->__('Leave empty to use the type as the slug'),
			'class' => 'validate-identifier',
		));
		
		if (!Mage::app()->isSingleStoreMode()) {
			$field = $fieldset->addField('store_id', 'multiselect', array(
				'name' => 'stores[]',
				'label' => Mage::helper('cms')->__('Store View'),
				'title' => Mage::helper('cms')->__('Store View'),
				'required' => true,
				'values' => Mage::getSingleton('adminhtml/system_store')->getStoreValuesForForm(false, true),
			));

			$renderer = $this->getLayout()->createBlock('adminhtml/store_switcher_form_renderer_fieldset_element');
			
			if ($renderer) {
				$field->setRenderer($renderer);
			}
		}
		else {
			$fieldset->addField('store_id', 'hidden', array(
				'name'      => 'stores[]',
				'value'     => Mage::app()->getStore(true)->getId()
			));
		}

		$fieldset->addField('has_archive', 'select', array(
			'name' => 'has_archive',
			'title' => $this->helper('adminhtml')->__('Has Archive'),
			'label' => $this->helper('adminhtml')->__('Has Archive'),
			'required' => true,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
		));
		
		$fieldset->addField('display_on_blog', 'select', array(
			'name' => 'display_on_blog',
			'title' => $this->helper('adminhtml')->__('Display on Blog'),
			'label' => $this->helper('adminhtml')->__('Display on Blog'),
            'note' => Mage::helper('wordpress')->__('If Yes, posts will be displayed on the blog homepage'),
			'required' => true,
			'values' => Mage::getModel('adminhtml/system_config_source_yesno')->toOptionArray(),
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
			$data = $object->getData();
			
			if (isset($data['store_id']) && is_array($data['store_id'])) {
				$data['store_id'] = array_shift($data['store_id']);	
			}
			
			return $data;
		}

		return array(
			'is_public' => 1,
			'has_archive' => 1,
			'store_id' => Mage::app()->isSingleStoreMode() ? 0 : array(0),
		);
	}
}
