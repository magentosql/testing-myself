<?php

class Wsnyc_PromoModals_Model_Observer {
    
    public function saveModalData(Varien_Event_Observer $observer) {
        $rule = $observer->getEvent()->getRule();
        
        $modal = Mage::getModel('promomodals/modal')->load($rule->getId(), 'rule_id');
        
        $modal->addData(array(
            'modal_name' => $rule->getModalName(),
            'modal_is_active' => $rule->getModalIsActive(),
            'modal_link_name' => $rule->getModalLinkName(),
            'modal_description' => $rule->getModalDescription(),
            'rule_id' => $rule->getId()
        ));
        $modal->save();
        
    }
}
