<?php

class Wsnyc_Capacity_Model_Observer {

    /**
     * Xpath for settings parameters
     */
    const XML_PATH_ENABLED = 'shipping/capacity/active';
    const XML_PATH_RETAILER_CODE = 'shipping/capacity/retailer_code';
    const XML_PATH_CLIENT_CODE = 'shipping/capacity/client_code';
    const XML_PATH_FTP_FILENAME = 'shipping/capacity/ftp_filename';
    
    /**
     *
     * @var string
     */
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
        
        if (!Mage::getStoreConfig(self::XML_PATH_ENABLED)) {
            return;
        }
        
        /**
         * @var Mage_Sales_Model_Order_Invoice $invoice
         */
        $invoice = $observer->getEvent()->getInvoice();
        if (!$this->_shouldSendInfo($invoice)) {
            //shipment already send or not yet shipped
            return;
        }        
        $filename = $this->_prepareData($invoice);
        $this->_sendData($filename);
        $invoice->getOrder()->setCapacitySendStatus(1)->save();
    }
    
    /**
     * Prepare data
     * 
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return string
     */
    protected function _prepareData(Mage_Sales_Model_Order_Invoice $invoice) {
        $this->_checkDir();
        $filename = $this->_tmpCsvDir . DS . $this->_getFilename($invoice);
        $fp = fopen($filename, 'w');
        fputcsv($fp, $this->_cols, "\t", '"');
        $i = 1;        
        $shipping = $invoice->getOrder()->getShippingAddress();
        $billing = $invoice->getOrder()->getBillingAddress();
        foreach($invoice->getAllItems() as $item) {
            /**
             * @var Mage_Sales_Model_Order_Shipment_Item $item
             */
            $fields = array(
                $invoice->getOrder()->getIncrementId(), //PickTicketID
                $invoice->getOrder()->getIncrementId(), //MasterOrderID
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
                $this->helper()->getShippingMethod($invoice->getOrder()), //ShipMethod
                null, //ThirdPartyAccount
                substr($invoice->getOrder()->getSignatureRequired(),0,1), //SignatureRequired
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
                $invoice->getOrder()->getCreatedAt(), //OrderDate
                null, //RequestedShipDate
                null, //CancelDate
                null, //RequestedDeliveryDate
                null, //DeliveryByDate
                $invoice->getShippingAmount(), //ShippingAmount
                null, //ShippingTaxRate
                null, //OrderDiscountRate
                null, //OrderDiscountAmount
                $i++, //ItemLineNumber
                $item->getSku(), //ItemProductID
                $item->getPrice(), //ItemUnitAmount
                null, //ItemDiscountRate
                null, //ItemTaxRate
                $item->getQty(), //ItemOrderQuantity
                $item->getQty(), //ItemShipQuantity
                null, //BuyerItemNumber
                "EOL" //EndOfLine
            );
            fputcsv($fp, $fields, "\t", '"');
        }
        fclose($fp);
        
        return $filename;
    }
    
    /**
     * Upload data to the server 
     * 
     * @param Mage_Sales_Model_Order_Shipment $filename
     * @return boolean
     */
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
    
    /**
     * Create filename
     * 
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return string
     */
    protected function _getFilename(Mage_Sales_Model_Order_Invoice $invoice) {
        return  $invoice->getOrder()->getIncrementId()
                . '_' .Mage::getStoreConfig(self::XML_PATH_FTP_FILENAME)
                . '_' . date('Ymd\TGis')
                .'.csv';
    }
    
    /**
     * Check if info should be sent
     * 
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return boolean
     */
    protected function _shouldSendInfo(Mage_Sales_Model_Order_Invoice $invoice) {
        return $invoice->getState() == Mage_Sales_Model_Order_Invoice::STATE_PAID && !$invoice->getOrder()->getCapacitySendStatus();
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