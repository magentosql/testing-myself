<?php
/**
 * @category    Fishpig
 * @package    Fishpig_AttributeSplashPro
 * @license      http://fishpig.co.uk/license.txt
 * @author       Ben Tideswell <ben@fishpig.co.uk>
 */

class Fishpig_Wordpress_Addon_CPT_Block_Adminhtml_Type_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
{
	/**
	 * Set the form parameters
	 *
	 * @return $this
	 */
	protected function _prepareForm()
	{
		$form = new Varien_Data_Form(array(
			'id' => 'edit_form',
			'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
			'method' => 'post',
		));
		
		$form->setUseContainer(true);
		$this->setForm($form);

		return parent::_prepareForm();
	}
}
