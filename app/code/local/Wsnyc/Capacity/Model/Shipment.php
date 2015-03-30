<?php

class Wsnyc_Capacity_Model_Shipment extends Mage_Core_Model_Abstract {
    public function handleShipmentResponse() {
        if($this->getLocalDebug() == true) {
            $filePath = Mage::getBaseDir() . '/' . $this->getPathToFile();
            $file = fopen($filePath, "r");
        } else {
            $ftp = Mage::getModel('capacity/ftp');
            $ftp->connect($ftp->getFtpServer());
            $content = $ftp->getDirContent('/OUT/Shipconfirm/');
            $ftp->close();
            rsort($content);
            $file = $this->getShipmentFile($content[0]);
        }
        $shippedOrders = $this->processShipmentFile($file);
        $result = $this->createMagentoShipments($shippedOrders);
        return $result;
    }
    
    public function getShipmentFile($filename) {
        $ftp = Mage::getModel('capacity/ftp');
        return $ftp->getFile('/OUT/Shipconfirm/' . $filename);
    }
    
    /**
     * Creates an array containing order increment_id's as keys and an array with order items data as a value 
     * Example: 
     * [100015939] => array(4) {
     *   ["DuoBF"] => object(Varien_Object)#144 (7) {
     *     ["_data":protected] => array(3) {
     *       ["ordered"] => string(1) "1"
     *       ["shipped"] => string(1) "1"
     *       ["is_correct"] => bool(true)
     *     }
     *     ["_hasDataChanges":protected] => bool(true)
     *     ["_origData":protected] => NULL
     *     ["_idFieldName":protected] => NULL
     *     ["_isDeleted":protected] => bool(false)
     *     ["_oldFieldsMap":protected] => array(0) {
     *     }
     *     ["_syncFieldsMap":protected] => array(0) {
     *     }
     *   }
     * ...
     * }
     * @param resource $file a file pointer
     */
    public function processShipmentFile($file) {
        $headers = fgetcsv($file);
        $content = array();
        while (($data = fgetcsv($file, 0, "\t")) !== false) {
            if(empty($content[$data[1]])) {
                $content[$data[1]] = array();
            }
            $item = new Varien_Object;
            $item->setOrdered($data[17])
                    ->setShipped($data[19])
                    ->setTrackingNumbers($data[26]) //can a be comma-limited list
                    ->setCarrierCode($this->_getCarrier($data[25]))
                    ->setCarrierName($data[24]);
            if($item->getOrdered() == $item->getShipped()) {
                $item->setIsCorrect(true);
            } else {
                $item->setIsCorrect(false);
            }
            $content[$data[1]][$data[22]] = $item;             
            unset($data);
        }
        fclose($file);
        return $content;
    }
    
    /**
     * Creates shipments from a prepared array with shipments data from Capacity
     * @see Wsnyc_Capacity_Model_Shipment::processShipmentFile()
     * @param array $orders
     */
    public function createMagentoShipments($orders) {
        if(empty($orders)) {
            return;
        }
        $shipmentIds = array();
        foreach($orders as $orderId => $orderData) {
            $magentoOrder = $this->_retrieveMagentoOrder($orderId);
            if(!$magentoOrder->getEntityId()) {
                Mage::log('There is a shipping data available for a non-existent order: ' . $orderId, 0, 'capacity-shipconfirm.log');
                continue;
            }
            $shippedItems = array();
            $trackingCodes = array();
            foreach($magentoOrder->getAllItems() as $item) {
                foreach($orderData as $sku => $data) {
                    if($sku == $item->getSku()) {
                        $shippedItems[$item->getItemId()] = $data->getShipped();
                        $trackingCodes[$data->getTrackingNumbers()] = array('name' => $data->getCarrierName(), 'code' => $data->getCarrierCode(), 'numbers' => $data->getTrackingNumbers());
                    }
                }
            }
            $shipment = Mage::getModel('sales/service_order', $magentoOrder)
                    ->prepareShipment($shippedItems);
            try {
                $shipment->save();
            } catch(Exception $e) {
                Mage::log('There was a problem with creating a shipment for order ' . $orderId, 0, 'capacity-shipconfirm.log');
            }
            if($shipment->getId()) {
                foreach($trackingCodes as $code) {
                    $numbers = explode(',', $code['numbers']);
                    if(!empty($numbers)) {
                        foreach($numbers as $number) {
                            try {
                                Mage::getModel('sales/order_shipment_api')->addTrack($shipment->getIncrementId(), $code['code'], $code['name'], $number);
                            } catch (Exception $ex) {
                                Mage::log('There was a problem with creating a tracking number for shipment ' . $shipment->getId() . ' : ' . $ex->getMessage(), 0, 'capacity-shipconfirm.log');
                            }
                        }
                    }                
                }
                Mage::getModel('sales/order_shipment_api')->sendInfo($shipment->getIncrementId());
                        
            } else {
                Mage::log('There was a problem with creating tracking numbers for order ' . $orderId, 0, 'capacity-shipconfirm.log');
            }
        }
    }
    
    protected function _retrieveMagentoOrder($incrementId) {
        return Mage::getModel('sales/order')->getCollection()
            ->addAttributeToFilter('state', array('neq' => Mage_Sales_Model_Order::STATE_CANCELED))
            ->addAttributeToFilter('increment_id', $incrementId)
            ->getFirstItem();
    }
    
    protected function _getCarrier($code) {
        $code = strtolower($code);
        if(empty($code)) {
            return 'custom';
        }
        if(strpos($code, 'fed') !== false) {
            return 'fedex';
        } elseif(strpos($code, 'ups') !== false) {
            return 'ups';
        } elseif(strpos($code, 'usps') !== false) {
            return 'usps';
        } elseif(strpos($code, 'dhl') !== false) {
            return 'dhlint';
        } else {
            return 'custom';
        }
    }
}
