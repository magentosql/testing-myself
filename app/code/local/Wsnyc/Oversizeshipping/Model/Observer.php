<?php

class Wsnyc_Oversizeshipping_Model_Observer
    extends Varien_Event_Observer
{
    protected $_vendors = array();
    protected $_vendorsPrice = array();

    public function updateShippingMethods($observer)
    {
        $event = $observer->getEvent();
        $rate = $event->getRate();
        $vendor = $event->getVendor();
        if($this->_isOversizeMethod($rate)) {
            if(!$this->_isOversizeCalculatedforVendor($vendor)) {
                $items = $event->getRequest()->getAllItems();
                $stdRatePrice = (float) Mage::getStoreConfig('carriers/'.$rate->getMethod().'/price');
                $finalPrice = 0;
                $resource = Mage::getResourceModel('catalog/product');
                foreach($items as $item) {
                    $oversizePrice = 0;
                    try {
                        $oversizePrice = (float) $resource->getAttributeRawValue($item->getProduct()->getId(), 'oversize_shipping_price', $item->getStoreId());
                    } catch( Exception $e ) {
                        $oversizePrice = 0;
                    }
                    if(0 >= $oversizePrice) {
                        $oversizePrice = $stdRatePrice;
                    }
                    $finalPrice += $oversizePrice*$item->getQty();
                }
                
                $this->_vendors[$vendor->getId()] = true;
                $this->_vendorsPrice[$vendor->getId()] = $finalPrice;
            }
            $rate->setPrice($this->_vendorsPrice[$vendor->getId()]);
        }
        return;
    }

    protected function _isOversizeMethod($rate)
    {
        $oversizeModel = Mage::getModel('oversizeshipping/carrier_shippingmethod');
        return ($oversizeModel && $oversizeModel::CODE === $rate->getMethod());
    }
    protected function _isOversizeCalculatedforVendor($vendor)
    {
        return (isset($this->_vendors[$vendor->getId()]));
    }
}