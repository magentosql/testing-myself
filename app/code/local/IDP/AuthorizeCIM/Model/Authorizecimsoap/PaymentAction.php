<?php
/**
 */
 
class IDP_AuthorizeCIM_Model_authorizecimsoap_PaymentAction
{
	public function toOptionArray()
	{
		return array(
			array(
				'value' => 'authorize',
				'label' => Mage::helper('authorizeCIM')->__('Authorize')
			),
			array(
				'value' => 'authorize_capture',
				'label' => Mage::helper('authorizeCIM')->__('Authorize and Capture')
			)
		);
	}
}

?>

