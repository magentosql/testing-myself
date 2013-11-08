<?php

class Infortis_Ultimo_Model_Monogram extends Mage_Core_Model_Abstract
{

    public function addMonogram($observer)
    {
        $request = Mage::app()->getFrontController()->getRequest();
        $event = $observer->getEvent();
        $product = $event->getProduct();
        $item = $event->getQuoteItem();

        if((int)$product->getMonogram()):
            $type = $request->getParam('monogram-type', '');
            $color = $request->getParam('monogram-color', '');
            $initials = $request->getParam('monogram-initials', '');
  
            $item->setData('monogram-type', $type);
            $item->setData('monogram-color', $color);
            $item->setData('monogram-initials', $initials);
            $item->setData('monogram-check', $check);
            $item->getQuote()->save();

            $monogramPrice = 16;

            $monogramCustomPrice =
                (float)Mage::getResourceModel('catalog/product')
                    ->getAttributeRawValue($product->getId(), 'monogram_custom_price', $item->getStoreId());
            if($monogramCustomPrice) {
                $monogramPrice = $monogramCustomPrice;
            }
            $specialPrice = (float)$product->getFinalPrice() + $monogramPrice;

            $item->setCustomPrice($specialPrice);
            $item->setOriginalCustomPrice($specialPrice);
            $item->getProduct()->setIsSuperMode(true);
            $item->save();

        endif;


    }
}