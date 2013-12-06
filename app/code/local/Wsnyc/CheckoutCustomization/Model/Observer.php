<?php
class Wsnyc_CheckoutCustomization_Model_Observer extends Mage_Core_Controller_Varien_Action {

    public function saveGiFtWrapInfo($observer)
    {
        $event = $observer->getEvent();
        $request = $event->getRequest();

        $allowGiftMessages          = $request->getPost('allow_gift_messages', false);
        $allowGiftMessagesForOrder  = $request->getPost('allow_gift_messages_for_order', false);
        $allowGiftMessagesForItems  = $request->getPost('allow_gift_messages_for_items', false);
        $holidayGiftWrapOrder       = $request->getPost('gift_wrap_order', false);
        $holidayGiftWrapItems       = $request->getPost('gift_wrap_items', false);

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
    }

    public function addGiftWrapInfoToOrder($observer)
    {
        $session = Mage::getSingleton('checkout/session');
        $order = $observer->getEvent()->getOrder();
        $countedItems = count($order->getAllVisibleItems());

        $allowGiftMessages = $session->getData('allow_gift_messages');
        $allowGiftMessagesForOrder = $session->getData('allow_gift_messages_for_order');
        $allowGiftMessagesForItems = $session->getData('allow_gift_messages_for_items');
        $holidayGiftWrapOrder = $session->getData('gift_wrap_order');
        $holidayGiftWrapItems = $session->getData('gift_wrap_items');
        $giftWrapFee = $session->getData('gift_wrap_fee');

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

                $giftWrapFee = $giftWrapFee * $countedItems;
                $order->setOnestepcheckoutGiftwrapAmount($giftWrapFee);
                if($holidayGiftWrapItems && $holidayGiftWrapItems == 'holiday') {
                    $giftwrapType = 'holiday';
                }
            }
            $order->setOnestepcheckoutGiftwrapType($giftwrapType);
        }

        $session->unsetData('allow_gift_messages');
        $session->unsetData('allow_gift_messages_for_order');
        $session->unsetData('allow_gift_messages_for_items');
        $session->unsetData('gift_wrap_order');
        $session->unsetData('gift_wrap_items');
        $session->unsetData('gift_wrap_fee');
    }
}