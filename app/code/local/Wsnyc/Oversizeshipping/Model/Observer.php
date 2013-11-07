<?php

class Wsnyc_Oversizeshipping_Model_Observer
    extends Varien_Event_Observer
{
    public function appendPriceToShipping($observer)
    {
        $address = $observer->getQuoteAddress();
        #Mage::log($address->getAddressType(), Zend_Log::DEBUG, 'event.log', true);
        if($address->getAddressType() === $address::TYPE_SHIPPING){
            $price = 0;
            $resource = Mage::getResourceModel('catalog/product');
            foreach($address->getAllItems() as $item) {
                try {
                    $oversizePrice = $resource->getAttributeRawValue($item->getProduct()->getId(), 'oversize_shipping_price', $item->getStoreId());
                } catch( Exception $e ) {
                    $oversizePrice = 0;
                }
                if($oversizePrice > 0) {
                    $price += $oversizePrice;
                }
            }

            $address->setCollectShippingRates(false);
            $rates = $address->collectShippingRates()
                     ->getGroupedAllShippingRates();
            foreach ($rates as $carrier) {
                foreach ($carrier as $rate) {
                    $rate->setPrice((float)$rate->getPrice()+$price);
                    $rate->save();
                }
            }
            $address->save();
        }
    }
}