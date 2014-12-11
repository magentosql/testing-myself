<?php

class Wsnyc_Capacity_Model_Observer {
    
    const XML_PATH_RETAILER_CODE = 'shipping/capacity/retailer_code';
    const XML_PATH_CLIENT_CODE = 'shipping/capacity/client_code';
    
    protected $_tmpCsvDir;
    
    protected $_cols = array('PickTicketID', 'MasterOrderID', 'ReleaseFlag', 'Reship850Flag', 'OrderType', 'SpecialProcessingFlag', 
        'CapacityImportID', 'PONumber', 'RetailerCode', 'StoreNumber', 'ShipToLocation', 'ClientCode', 'DropshipID', 
        'ExternalCustomerID', 'ShipBusinessName', 'ShipName', 'ShipAddress1', 'ShipAddress2', 'ShipCity', 'ShipState', 
        'ShipZip', 'ShipCountry', 'ShipPhone', 'ShipEmail', 'ShipResidentialFlag', 'ShipMethod', 'ThirdPartyAccount', 
        'SignatureRequired', 'ShipComment', 'GiftWrapFlag', 'GiftFrom', 'GiftTo', 'GiftMessage', 'BillBusinessName', 
        'BillName', 'BillAddress1', 'BillAddress2', 'BillCity', 'BillState', 'BillZip', 'BillCountry', 'BillPhone', 
        'PaymentTerms', 'OrderDate', 'RequestedShipDate', 'CancelDate', 'RequestedDeliveryDate', 'DeliveryByDate', 
        'ShippingAmount', 'ShippingTaxRate', 'OrderDiscountRate', 'OrderDiscountAmount', 'ItemLineNumber', 'ItemProductID', 
        'ItemUnitAmount', 'ItemDiscountRate', 'ItemTaxRate', 'ItemOrderQuantity', 'ItemShipQuantity', 'BuyerItemNumber', 'EndOfLine');
    
    public function __construct() {
        $this->_tmpCsvDir = BP . DS . 'var' . DS . 'tmp';
    }

    /**
     * Process shipment object on save
     * 
     * @param Varien_Event_Observer $observer
     */
    public function processShipment($observer = null) {
        
        /**
         * @var Mage_Sales_Model_Order_Shipment $shipment
         */
        $shipment = $observer->getEvent()->getShipment();         
        
        if (!$this->_shouldSendInfo($shipment)) {
            //shipment already send or not yet shipped
            return;
        }        
        $filename = $this->_prepareData($shipment);
        $this->_sendData($filename);
        $shipment->setCapacitySendStatus(1)->save();
    }
    
    protected function _prepareData($shipment) {
        $this->_checkDir();
        $filename = $this->_tmpCsvDir . DS . 'capacity_csv_' . $shipment->getId() . '.csv';
        $fp = fopen($filename, 'w');
        fputcsv($fp, $this->_cols, "\t", '"');
        $i = 1;        
        $shipping = $shipment->getOrder()->getShippingAddress();
        $billing = $shipment->getOrder()->getBillingAddress();
        foreach($shipment->getOrder()->getAllItems() as $item) {            
            $fields = array(
                $shipment->getIncrementId(), //PickTicketID
                $shipment->getOrder()->getIncrementId(), //MasterOrderID
                'N', //ReleaseFlag
                'N', //Reship850Flag
                null, //OrderType
                'N', //SpecialProcessingFlag
                null, //CapacityImportID
                null, //PONumber
                Mage::getStoreConfig(self::XML_PATH_RETAILER_CODE), //RetailerCode
                null, //StoreNumber
                null, //ShipToLocation
                Mage::getStoreConfig(self::XML_PATH_CLIENT_CODE), //ClientCode
                null, //DropshipID
                null, //ExternalCustomerID
                null, //ShipBusinessName
                $shipping->getName(), //ShipName
                $this->helper()->getStreet($shipping, 1), //ShipAddress1
                $this->helper()->getStreet($shipping, 2), //ShipAddress2
                $shipping->getCity(), //ShipCity
                $this->helper()->getRegion($shipping), //ShipState
                $shipping->getPostcode(), //ShipZip
                $shipping->getCountry(), //ShipCountry
                $shipping->getTelephone(), //ShipPhone
                $shipping->getEmail(), //ShipEmail
                'Y', //ShipResidentialFlag
                $this->helper()->getShippingMethod($shipment), //ShipMethod
                null, //ThirdPartyAccount
                $shipment->getOrder()->getSignatureRequired(), //SignatureRequired
                null, //ShipComment
                null, //GiftWrapFlag
                null, //GiftFrom
                null, //GiftTo
                null, //GiftMessage
                null, //BillBusinessName
                $billing->getName(), //BillName
                $this->helper()->getStreet($billing, 1), //BillAddress1
                $this->helper()->getStreet($billing, 2), //BillAddress2
                $billing->getCity(), //BillCity
                $this->helper()->getRegion($billing), //BillState
                $billing->getPostcode(), //BillZip
                $billing->getCountry(), //BillCountry
                $billing->getTelephone(), //BillPhone
                null, //PaymentTerms
                $shipment->getOrder()->getCreatedAt(), //OrderDate
                null, //RequestedShipDate
                null, //CancelDate
                null, //RequestedDeliveryDate
                null, //DeliveryByDate
                $shipment->getShippingAmount(), //ShippingAmount
                null, //ShippingTaxRate
                null, //OrderDiscountRate
                null, //OrderDiscountAmount
                $i, //ItemLineNumber
                $item->getSku(), //ItemProductID
                $item->getQtyOrdered(), //ItemUnitAmount
                null, //ItemDiscountRate
                null, //ItemTaxRate
                $item->getQtyInvoiced(), //ItemOrderQuantity
                $item->getQtyShipped(), //ItemShipQuantity
                $i++, //BuyerItemNumber
                "EOL" //EndOfLine
            );
            fputcsv($fp, $fields, "\t", '"');
        }
        fclose($fp);
        
        return $filename;
    }
    
    protected function _sendData($filename) {
        
        /**
         * @var Wsnyc_Capacity_Model_Ftp $ftp
         */
        $ftp = Mage::getModel('capacity/ftp');
        $ftp->connect($ftp->getFtpServer());
        $success = $ftp->upload($filename);
        $ftp->close();
        return $success;
    }    
    
    protected function _shouldSendInfo($shipment) {        
        
        $udropship = Mage::getConfig()->getModuleConfig('Unirgy_Dropship')->is('active', 'true') ? $shipment->getUdropshipStatus() : 1;
        
        return $udropship && !$shipment->getCapacitySendStatus();
    }
    
    /**
     * Check if tmp dir for CSV files exists
     * 
     * @return \Wsnyc_Capacity_Model_Observer
     */
    protected function _checkDir() {
        if (!is_dir($this->_tmpCsvDir)) {
            mkdir($this->_tmpCsvDir, 0775, true);
        }
        
        return $this;
    }
    
    /**
     * Get helper instance
     * 
     * @return Wsnyc_Capacity_Helper_Data
     */
    protected function helper() {
        return Mage::helper('capacity');
    }
}