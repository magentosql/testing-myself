<?php

class Infortis_Ultimo_Model_Monogram extends Mage_Core_Model_Abstract
{

    public function addMonogram($observer)
    {
        $request = Mage::app()->getFrontController()->getRequest();
        $check = $request->getParam('monogram-check', 'off');

        if($check == 'on'):
        
            $event = $observer->getEvent();
            $product = $event->getProduct();
            $item = $event->getQuoteItem();

            $type = $request->getParam('monogram-type', '');
            $color = $request->getParam('monogram-color', '');
            $initials = $request->getParam('monogram-initials', '');
  
            $item->setData('monogram-type', $type);
            $item->setData('monogram-color', $color);
            $item->setData('monogram-initials', $initials);
            $item->setData('monogram-check', $check);
            $item->getQuote()->save();

            $specialPrice = $product->getPrice() + 16;

            $item->setCustomPrice($specialPrice);
            $item->setOriginalCustomPrice($specialPrice);
            $item->getProduct()->setIsSuperMode(true);
            $item->save();

        endif;


    }
}