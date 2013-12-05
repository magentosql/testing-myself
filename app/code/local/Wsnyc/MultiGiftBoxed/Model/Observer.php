<?php
class Wsnyc_MultiGiftBoxed_Model_Observer
    extends Magestore_Onestepcheckout_Model_Observer
{
    public function saveGiftBoxedInfo($observer)
    {
        $order = $observer->getEvent()->getOrder();
        $request = Mage::getSingleton('core/app')->getRequest();
        $items = $order->getAllItems();
        $post = $request->getPost();
        foreach($items as $item)
        {
            $item->setGiftBoxed($post['gift'][$item->getProductId()]);
            $item->save();
        }

        $order->setGiftBoxedSeparately($post['gift_boxed_separately']);
        $order->save();

    }
}