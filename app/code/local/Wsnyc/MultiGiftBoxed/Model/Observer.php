<?php
class Wsnyc_MultiGiftBoxed_Model_Observer
    extends Magestore_Onestepcheckout_Model_Observer
{
    public function saveGiftBoxedInfo($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $session = Mage::getSingleton('checkout/session');
        $order = $observer->getEvent()->getOrder();
        $items = $order->getAllItems();
        $giftBoxedItems = $session->getData('gift_boxed_items');
        $allowGiftMessagesForItems  = (int)$session->getData('allow_gift_messages_for_items');
        foreach($items as $item)
        {
            $item->setGiftBoxed($giftBoxedItems[$item->getQuoteItemId()]);
            $item->save();
        }

        $order->setGiftBoxedSeparately($allowGiftMessagesForItems);
        $order->save();

        $session->unsetData('allow_gift_messages');
        $session->unsetData('allow_gift_messages_for_order');
        $session->unsetData('allow_gift_messages_for_items');
        $session->unsetData('gift_wrap_order');
        $session->unsetData('gift_wrap_items');
        $session->unsetData('gift_wrap_fee');

    }
}