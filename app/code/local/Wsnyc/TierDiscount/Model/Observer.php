<?php

class Wsnyc_TierDiscount_Model_Observer {
    
    public function addTierActionOption(Varien_Event_Observer $observer) {
        $form = $observer->getEvent()->getForm();
        $options = $form->getElement('simple_action')->getValues();
        $options[] = array(
            'value' => Wsnyc_TierDiscount_Model_Observer::TIER_DISCOUNT_ACTION,
            'label' => Mage::helper('tier_discount')->__('Tier Discount'),
        );
        $form->getElement('simple_action')->setValues($options);
        
        $fieldset = $form->getElement('action_fieldset');
        $fieldset->addField('tier_discount', 'text', array(
            'name' => 'tier_discount',
            'required' => true,            
            'label' => Mage::helper('tier_discount')->__('Tier Discount Amount'),
        ), 'discount_amount');
        
        $form->getElement('tier_discount')->setRenderer(
            Mage::app()->getLayout()->createBlock('tier_discount/adminhtml_renderer_tier')
        );

        return $this;        
    }
    
    /**
     * Prepare tier data for saving
     * 
     * @param Varien_Event_Observer $observer
     */
    public function prepareTierFields(Varien_Event_Observer $observer) {
        /**
         * @var Mage_Core_Controller_Request_Http $request
         */
        $request = $observer->getEvent()->getRequest();
        $tier = $request->getPost('tier_discount', array());
        $request->setPost('tier_discount', serialize($tier));
    }

}