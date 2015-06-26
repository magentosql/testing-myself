<?php

class CommerceExtensions_BetterReviews_Model_Adminhtml_System_Config_Source_Form_Attribute extends Mage_Core_Model_Abstract
{
  public function toOptionArray()
  {
	$options = array();	
	$attributes = Mage::getResourceModel('catalog/product_attribute_collection');
	$attributes->addFieldToFilter('frontend_input',array('select','multiselect','text'));
	$attributes->getSelect()->where('LENGTH(frontend_label) > 0');
	$attributes->getSelect()->order('frontend_label','ASC');	
	
	foreach($attributes as $attribute){
	  $options[] = array('value' => $attribute->getAttributecode(),'label' => $attribute->getFrontendLabel());
	}
	return $options;
  }			  
}