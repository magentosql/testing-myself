<?php 
class Wsnyc_WashingServiceCart_CartController 
    extends Mage_Core_Controller_Front_Action
{
    public function addAction(){
 
    $cart = Mage::getModel('checkout/cart');
    $postdata = $this->getRequest()->getPost();
    
    /*prepare array of product ids and quantities out of request params*/
    foreach($postdata as $key => $qty){
        $parts = explode('_',$key);
        if ($parts[0]=='item' && $parts[2]=='qty'){
            $productQty[$parts[1]] = $qty;
        }
    }
    
    foreach($productQty as $productId => $qty){
        $product = Mage::getModel('catalog/product')->load($productId);
        $cart->addProduct($product, $qty);
    }
    $cart->save();
    
    Mage::getSingleton('checkout/session')->setCartWasUpdated(true);
    }
}