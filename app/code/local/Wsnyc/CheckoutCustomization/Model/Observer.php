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
}
