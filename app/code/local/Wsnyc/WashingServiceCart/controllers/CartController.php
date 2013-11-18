<?php

class Wsnyc_WashingServiceCart_CartController 
extends Mage_Core_Controller_Front_Action {

    public function addAction() 
    {

        $cart = Mage::getModel('checkout/cart');
        $postdata = $this->getRequest()->getParam('item');
        /* prepare array of product ids and quantities out of request params */
        foreach ($postdata as $prodid => $prodqty) {
                $productQty[$prodid] = $prodqty['qty'];
        }

        foreach ($productQty as $productId => $qty) {
            $product = Mage::getModel('catalog/product')->load($productId);
            $cart->addProduct($product, $qty);
        }
        $cart->save();

        Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
        $this->getResponse()->setHttpResponseCode(200);
    }

}
