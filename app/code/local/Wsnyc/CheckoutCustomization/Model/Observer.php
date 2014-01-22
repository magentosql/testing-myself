<?php
class Wsnyc_CheckoutCustomization_Model_Observer {

    public function saveGiFtWrapInfo($observer)
    {
        $event = $observer->getEvent();
        $request = $event->getRequest();

        $allowGiftMessages          = $request->getPost('allow_gift_messages', false);
        $allowGiftMessagesForOrder  = $request->getPost('allow_gift_messages_for_order', false);
        $allowGiftMessagesForItems  = $request->getPost('allow_gift_messages_for_items', false);
        $holidayGiftWrapOrder       = $request->getPost('gift_wrap_order', false);
        $holidayGiftWrapItems       = $request->getPost('gift_wrap_items', false);
        $giftBoxedItems             = $request->getPost('gift', false);

        $giftWrapFee = 0;
        if($allowGiftMessages)
        {
            $giftWrapFee = Mage::getModel('wsnyc_checkoutcustomization/config')->getRegularGiftWrapFee();
            if($allowGiftMessagesForOrder)
            {
                if($holidayGiftWrapOrder && $holidayGiftWrapOrder == 'holiday'){
                    $giftWrapFee = Mage::getModel('wsnyc_checkoutcustomization/config')->getHolidayGiftWrapFee();
                }
            }
            else if($allowGiftMessagesForItems) {
                if($holidayGiftWrapItems && $holidayGiftWrapItems == 'holiday') {
                    $giftWrapFee = Mage::getModel('wsnyc_checkoutcustomization/config')->getHolidayGiftWrapFee();
                }
            }
        }

        $session = Mage::getSingleton('checkout/session');
        $session->setData('allow_gift_messages', $allowGiftMessages);
        $session->setData('allow_gift_messages_for_order', $allowGiftMessagesForOrder);
        $session->setData('allow_gift_messages_for_items', $allowGiftMessagesForItems);
        $session->setData('gift_wrap_order', $holidayGiftWrapOrder);
        $session->setData('gift_wrap_items', $holidayGiftWrapItems);
        $session->setData('gift_wrap_fee', $giftWrapFee);
        $session->setData('gift_boxed_items', $giftBoxedItems);
    }

    public function saveCommentAndSignature($observer)
    {
        $event = $observer->getEvent();
        $request = $event->getRequest();

        $laundressComment          = $request->getPost('laundress_comment', false);
        $requiresSignature         = $request->getPost('requires_signature', false);

        $session = Mage::getSingleton('checkout/session');
        $session->setData('laundress_comment', $laundressComment);
        $session->setData('requires_signature', $requiresSignature);

    }

    public function addGiftWrapInfoToOrder($observer)
    {
        $session = Mage::getSingleton('checkout/session');
        $order = $observer->getEvent()->getOrder();
        //$countedItems = count($order->getAllVisibleItems());

        $allowGiftMessages = $session->getData('allow_gift_messages');
        $allowGiftMessagesForOrder = $session->getData('allow_gift_messages_for_order');
        $allowGiftMessagesForItems = $session->getData('allow_gift_messages_for_items');
        $holidayGiftWrapOrder = $session->getData('gift_wrap_order');
        $holidayGiftWrapItems = $session->getData('gift_wrap_items');
        $giftWrapFee = $session->getData('gift_wrap_fee');
        $giftBoxedItems = $session->getData('gift_boxed_items');

        $giftWrapTotal = 0;
        if($allowGiftMessages)
        {
            $giftwrapType = 'regular';

            if($allowGiftMessagesForOrder)
            {
                $order->setOnestepcheckoutGiftwrapAmount($giftWrapFee);
                if($holidayGiftWrapOrder && $holidayGiftWrapOrder == 'holiday') {
                    $giftwrapType = 'holiday';
                }
            }
            else if($allowGiftMessagesForItems) {

                $giftBoxedAmount = $session->getData('giftwrapTotalAmount');

                $order->setOnestepcheckoutGiftwrapAmount($giftBoxedAmount);
                if($holidayGiftWrapItems && $holidayGiftWrapItems == 'holiday') {
                    $giftwrapType = 'holiday';
                }
            }
            $order->setOnestepcheckoutGiftwrapType($giftwrapType);
        }
    }

    public function setShippingAddress($observer)
    {
        $quote = $observer->getEvent()->getQuote();
        $customer = $quote->getCustomer();

        $customerShippingAddressId = $customer->getDefaultShipping();
        $customerShippingAddress = Mage::getModel('customer/address')->load($customerShippingAddressId);

        if($customerShippingAddressId){
            $shippingAddress = $quote->getShippingAddress();
            $shippingAddress->setStreet($customerShippingAddress->getStreet())
                ->setFirstname($customerShippingAddress->getFirstname())
                ->setLastname($customerShippingAddress->getLastname())
                ->setRegionId($customerShippingAddress->getRegionId())
                ->setCountryId($customerShippingAddress->getCountryId())
                ->setPostcode($customerShippingAddress->getPostcode())
                ->setRegion($customerShippingAddress->getRegion())
                ->setCity($customerShippingAddress->getCity())
                ->setCustomerId($customer->getId());
        }

        $rates = $shippingAddress->collectShippingRates()
            ->getGroupedAllShippingRates();

        $firstMethod='';
        foreach ($rates as $carrier) {
            foreach ($carrier as $rate) {
                if ($rate->getCode() == 'freeshipping_freeshipping'){
                    $firstMethod = $rate->getCode();
                }
                $allrates[$rate->getCode()] = $rate->getPrice();
            }
        }


        if ($firstMethod=='freeshipping_freeshipping') {
            $shippingAddress->setShippingMethod($firstMethod)->setShippingAmount(0)
                ->setBaseShippingAmount(0)
                ->setSubtotalWithDiscount(0)
                ->setBaseSubtotalWithDiscount(0)
                ->setShippingInclTax(0);
            $quote->setShippingAddress($shippingAddress);
            $quote->getShippingAddress()->setFreeShipping(1);
            $quote->setTotalsCollectedFlag(false);
        } else {
            //no freeshipiing found use other cheapest
	    if(!is_array($allrates)){
                $allrates = array(''=>'');
            }
            asort($allrates);
            $allrates = array_filter($allrates);
            foreach($allrates as $key => $price){
                $shippingAddress->setShippingMethod($key);
                $quote->setShippingAddress($shippingAddress);
                $quote->getShippingAddress()->setFreeShipping(0);
                $quote->setTotalsCollectedFlag(false);
                break;
            }
        }

    }
}
