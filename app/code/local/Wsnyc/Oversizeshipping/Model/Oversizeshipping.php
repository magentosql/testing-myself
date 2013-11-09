<?php

class Wsnyc_Oversizeshipping_Model_Oversizeshipping
{
    protected $_address;

    public function updateShippingMethods(Mage_Sales_Model_Quote_Address $address)
    {
        $this->_address = $address;
        $price = $this->_calculateOversizedShippingMethodPrice();
        if($this->_cartHasOversizedItems($price)) {
            $this->_updateShippingMethodPrices($price);
        }
    }

    protected function _calculateOversizedShippingMethodPrice()
    {
        $price = 0;
        $resource = Mage::getResourceModel('catalog/product');
        foreach($this->_address->getAllItems() as $item) {
            try {
                $oversizePrice = (float) $resource->getAttributeRawValue($item->getProduct()->getId(), 'oversize_shipping_price', $item->getStoreId());
            } catch( Exception $e ) {
                $oversizePrice = 0;
            }
            if($oversizePrice > 0) {
                $price += $oversizePrice*$item->getQty();
            }
        }
        return $price;
    }

    protected function _cartHasOversizedItems($price)
    {
        return ((float)$price > 0);
    }

    protected function _updateShippingMethodPrices($price)
    {
        foreach($this->_address->getAllShippingRates() as $rate) {
            $rate->setPrice($price);
        }
    }
}