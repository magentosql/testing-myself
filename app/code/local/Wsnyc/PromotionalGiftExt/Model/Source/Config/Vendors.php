<?php

class Wsnyc_PromotionalGiftExt_Model_Source_Config_Vendors {
    
    public function toOptionArray(){
        $vendors = array(array('value' => 0, 'label' => ''));
        foreach(Mage::getModel('udropship/vendor')->getCollection() as $vendor) {
            $vendors[] = array('value' => $vendor->getId(), 'label'=> $vendor->getVendorName());
        }
        
        return $vendors;
    }
}