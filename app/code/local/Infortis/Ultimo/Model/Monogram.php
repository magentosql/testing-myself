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
      	    $check = $request->getParam('monogram-check','');
            $monogramPrice = 16.00;

            if($product->getTypeId()=='simple'){
                $item->setData('monogram-type', $type);
                $item->setData('monogram-color', $color);
                $item->setData('monogram-initials', $initials);
                $item->setData('monogram-check', $check);
 		$item->getQuote()->save();

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

            } elseif($product->getTypeId()=='grouped') {
		$groupedProducts = $product->getTypeInstance(true)->getAssociatedProducts($product);
		$groupedArray = array();
		foreach($groupedProducts as $gProduct){
		    $groupedArray[] = $gProduct->getId();
		}
		
		foreach($item->getQuote()->getAllItems() as $qitem){
                    if (in_array($qitem->getProductId(),$groupedArray) && $qitem->getData('monogram-type')==''){

                    $monogramCustomPrice =
                        (float)Mage::getResourceModel('catalog/product')
                        ->getAttributeRawValue($qitem->getProductId(), 'monogram_custom_price', $qitem->getStoreId());
                    if($monogramCustomPrice) {
                        $monogramPrice = $monogramCustomPrice;
                    }

                    $specialPrice = (float)$qitem->getProduct()->getFinalPrice() + $monogramPrice;
                    $qitem->setCustomPrice($specialPrice);
                    $qitem->setOriginalCustomPrice($specialPrice);
                    $qitem->getProduct()->setIsSuperMode(true);

                        $qitem->setData('monogram-type', $type);
                        $qitem->setData('monogram-color', $color);
                        $qitem->setData('monogram-initials', $initials);
                        $qitem->setData('monogram-check', $check)->save();
		    } 
		}
            }

            $item->getQuote()->save();

        endif;


    }
}
