<?php

class CommerceExtensions_BetterReviews_Model_Adminhtml_System_Config_Source_Form_Direction extends Mage_Core_Model_Abstract
{
  public function toOptionArray()
  {
	return array(
	  array('value' => Varien_Db_Select::SQL_ASC, 'label' => 'Ascending'),
	  array('value' => Varien_Db_Select::SQL_DESC, 'label' => 'Descending')
	);	
  }			  
}