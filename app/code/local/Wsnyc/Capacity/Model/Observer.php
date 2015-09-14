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
        $invoice = $observer->getEvent()->getInvoice();
        if (!Mage::getStoreConfig(self::XML_PATH_ENABLED, $invoice->getStoreId())) {
            return;
        }

        /**
         * @var Mage_Sales_Model_Order_Invoice $invoice
         */
        if (!$this->_shouldSendInfo($invoice)) {
            //shipment already send or not yet shipped
            //return;
        }        
        $filename = $this->_prepareData($invoice);
        try {
            $this->_sendData($filename, $invoice->getStoreId());
            $invoice->getOrder()->setCapacitySendStatus(1)->save();
        }
        catch (Exception $e) {
            return;
        }
    }
    
    public function handleShipmentResponse() {
        $shipment = Mage::getModel('capacity/shipment');
        $shipment->handleShipmentResponse();
    }
    
    /**
     * Prepare data
     * 
     * @param Mage_Sales_Model_Order_Invoice $invoice
     * @return string
     */
    protected function _prepareData(Mage_Sales_Model_Order_Invoice $invoice) {
        $storeId = $invoice->getStoreId();
        $this->_checkDir();
        $filename = $this->_tmpCsvDir . DS . $this->_getFilename($invoice);
        $fp = fopen($filename, 'w');
        $this->_putCsv($fp, $this->_cols, "\t", '"');
        $i = 1;        
        $shipping = $invoice->getOrder()->getShippingAddress();
        $billing = $invoice->getOrder()->getBillingAddress();
        foreach($invoice->getAllItems() as $item) {
            if (!$this->_isVisibleItem($item)) {
                continue;
            }

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
                Mage::getStoreConfig(self::XML_PATH_RETAILER_CODE, $storeId), //RetailerCode
                null, //StoreNumber
                null, //ShipToLocation
                Mage::getStoreConfig(self::XML_PATH_CLIENT_CODE, $storeId), //ClientCode
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
            $this->_putCsv($fp, $fields, "\t", '"');
        }
        fclose($fp);
        
        return $filename;
    }
    
    /**
     * Upload data to the server 
     * 
     * @param string $filename
     * @return boolean
     */
    protected function _sendData($filename, $storeId) {
        
        /**
         * @var Wsnyc_Capacity_Model_Ftp $ftp
         */
        $ftp = Mage::getModel('capacity/ftp', array('store_id' => $storeId));
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
                . '_' .Mage::getStoreConfig(self::XML_PATH_FTP_FILENAME, $invoice->getStoreId())
                . '_' . date('Ymd\TGis')
                .'.txt';
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
    
    protected function _putCsv($handle, array $fields, $delimiter = ",", $enclosure = '"') {
        $result = fputcsv($handle, $fields, $delimiter, $enclosure);
        if (!$result) {
            Mage::log('Error when handling the file', null, 'capacity.log');
            throw new Exception('Unable to handle the file');
        }
        
        return $result;
    }

    protected function _isVisibleItem(Mage_Sales_Model_Order_Invoice_Item $item)
    {
        return !($item->getOrderItem()->getParentItemId() > 0);
    }
}