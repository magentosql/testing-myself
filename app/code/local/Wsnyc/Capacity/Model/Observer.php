<?php

class Wsnyc_Capacity_Model_Observer {
    
    /**
     * Process shipment object on save
     * 
     * @param Varien_Event_Observer $observer
     */
    public function processShipment(Varien_Event_Observer $observer) {
        
        /**
         * @var Mage_Sales_Model_Order_Shipment $shipment
         */
        $shipment = $observer->getEvent()->getShipment(); 
    }
}