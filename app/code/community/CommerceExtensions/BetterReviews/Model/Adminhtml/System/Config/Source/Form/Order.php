<?php

class CommerceExtensions_BetterReviews_Model_Adminhtml_System_Config_Source_Form_Order extends Mage_Core_Model_Abstract
{
  public function toOptionArray()
  {
	return array(
	  array('value' => 'created_at', 'label' => 'Date Review Created(Magento Default)'),
	  array('value' => 'LENGTH(`detail`.`detail`)', 'label' => 'Review Text Length(Good for SEO)')
	);	
  }			  
}