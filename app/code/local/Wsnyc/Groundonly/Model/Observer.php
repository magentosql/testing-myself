<?php

class Wsnyc_Groundonly_Model_Observer
    extends Varien_Event_Observer
{
    public function recollectShippingMethods($observer)
    {
        $shippingMethods = Mage::getConfig()->getXpath('global/wsnyc_groundonly/allowed_shipping_methods');
        if(!$shippingMethods) {
            $shippingMethods = array();
        } else {
            $shippingMethods = current($shippingMethods)->asArray();
        }

        $address = $observer->getQuoteAddress();
        if($address->getAddressType() === $address::TYPE_SHIPPING){
            $groundOnly = false;
            $resource = Mage::getResourceModel('catalog/product');
            foreach($address->getAllItems() as $item) {
                try {
                    $value = $resource->getAttributeRawValue($item->getProduct()->getId(), 'ground_only', $item->getStoreId());
                    if((int)$value) {
                        $groundOnly = true;
                        break;
                    }
                } catch( Exception $e ) {}
            }

            if($groundOnly) {
                $address->setCollectShippingRates(false);
                $rates = $address->collectShippingRates()
                         ->getGroupedAllShippingRates();
    
                foreach ($rates as $carrier) {
                    foreach ($carrier as $rate) {
                        if(!in_array($rate->getCode(), $shippingMethods)) {
                            $rate->isDeleted(true);
                        }
                        $rate->save();
                    }
                }
    
                $address->save();
            }
        }
    }
}