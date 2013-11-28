<?php

class Wsnyc_CouponcodeMaxdiscount_Model_Observer {

    protected function _getCart() {
        return Mage::getSingleton('checkout/cart');
    }

    protected function _getQuote() {
        return $this->_getCart()->getQuote();
    }

    public function checkoutCartCouponPost($observer) {

        $request = Mage::app()->getRequest();
        $actionName = Mage::app()->getRequest()->getActionName();
        $key = $request->getModuleName().'_'.$request->getControllerName().'_'.$request->getActionName();
        //Mage::log($key);

        //run this code on following places
        if (in_array(
                $key,
                array(
                        'onestepcheckout_index_add_coupon',
                        'checkout_cart_index',
                        'checkout_cart_updatePost',
                        'onestepcheckout_index_save_shipping'  
                )
            )
        ){  

            $request = Mage::getSingleton('core/app')->getRequest();
            $quote = $observer->getEvent()->getQuote();
            $usedCouponCode = (string) $request->getParam('coupon_code');

            //this is given with $ value - 100 means $100 is max discount client can get
            $coupon = Mage::getModel('salesrule/coupon')->loadByCode($usedCouponCode);
            $rule = Mage::getModel('salesrule/rule')->load($coupon->getRuleId());
            $maxAllowedDiscount = $rule->getMaxdiscount();
            if($maxAllowedDiscount<=0){
                //don't process further
                return $this;
            }

            $canAddItems = $quote->isVirtual() ? ('billing') : ('shipping');

            foreach ($quote->getAllAddresses() as $address) {

                if ( $address->getSubtotal() - $address->getSubtotalWithDiscount() > $maxAllowedDiscount) {


                    $address->setSubtotal(0);
                    $address->setBaseSubtotal(0);

                    $address->setGrandTotal(0);
                    $address->setBaseGrandTotal(0);

                    $address->collectTotals();

                    $quote->setSubtotal((float) $quote->getSubtotal() + $address->getSubtotal());
                    $quote->setBaseSubtotal((float) $quote->getBaseSubtotal() + $address->getBaseSubtotal());

                    $quote->setSubtotalWithDiscount(
                            (float) $quote->getSubtotalWithDiscount() + $address->getSubtotalWithDiscount()
                    );
                    $quote->setBaseSubtotalWithDiscount(
                            (float) $quote->getBaseSubtotalWithDiscount() + $address->getBaseSubtotalWithDiscount()
                    );

                    $quote->setGrandTotal((float) $quote->getGrandTotal() + $address->getGrandTotal());
                    $quote->setBaseGrandTotal((float) $quote->getBaseGrandTotal() + $address->getBaseGrandTotal());

                    $quote->save();

                    $quote->setGrandTotal($quote->getBaseSubtotal() - $maxAllowedDiscount)
                            ->setBaseGrandTotal($quote->getBaseSubtotal() - $maxAllowedDiscount)
                            ->setSubtotalWithDiscount($quote->getBaseSubtotal() - $maxAllowedDiscount)
                            ->setBaseSubtotalWithDiscount($quote->getBaseSubtotal() - $maxAllowedDiscount)
                            ->save();


                    if ($address->getAddressType() == $canAddItems) {
                        //echo $address->setDiscountAmount; exit;
                        $address->setSubtotalWithDiscount((float) $address->getSubtotal() - $maxAllowedDiscount);
                        $address->setGrandTotal((float) $address->getSubtotal() - $maxAllowedDiscount);
                        $address->setBaseSubtotalWithDiscount((float) $address->getBaseSubtotalWithDiscount() - $maxAllowedDiscount);
                        $address->setBaseGrandTotal((float) $address->getBaseGrandTotal() - $maxAllowedDiscount);
                        if ($address->getDiscountDescription()) {
                            $address->setDiscountAmount($maxAllowedDiscount*-1);
                            $address->setDiscountDescription($address->getDiscountDescription());
                            $address->setBaseDiscountAmount($maxAllowedDiscount*-1);
                        } 
                        $address->save();
                    }
                } 
            }
        }
    }
}
