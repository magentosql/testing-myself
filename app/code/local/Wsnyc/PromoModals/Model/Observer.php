<?php

class Wsnyc_PromoModals_Model_Observer {
    
    /**
     * Save modal header fields
     * 
     * @param Varien_Event_Observer $observer
     */
    public function saveModalData(Varien_Event_Observer $observer) {
        $rule = $observer->getEvent()->getRule();
        
        /**
         * @var Wsnyc_PromoModals_Model_Modal $modal
         */
        $modal = Mage::getModel('promomodals/modal')->load($rule->getId(), 'rule_id');        
        $modal->addData(array(
            'modal_name' => $rule->getModalName(),
            'modal_is_active' => $rule->getModalIsActive(),
            'modal_link_name' => $rule->getModalLinkName(),
            'modal_description' => $rule->getModalDescription(),
            'modal_sort_order' => $rule->getModalSortOrder(),
            'rule_id' => $rule->getId()
        ));
        $modal->save();
        
    }
}
