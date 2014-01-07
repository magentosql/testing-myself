<?php
class Wsnyc_CheckoutCustomization_Model_Total_Giftwrap
    extends Mage_Sales_Model_Quote_Address_Total_Abstract
{

    public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
        if (($address->getAddressType() == 'billing')) {
            return $this;
        }
        $session = Mage::getSingleton('checkout/session');

        $allowGiftMessages = $session->getData('allow_gift_messages');
//        $allowGiftMessagesForItems = $session->getData('allow_gift_messages_for_items');


        if(!$allowGiftMessages){
            return $this;
        }

        $items = $address->getAllItems();
        if (!count($items)) {
            return $this;
        }

        $giftwrapAmount = $session->getData('gift_wrap_fee');
//        $giftBoxedItems = $session->getData('gift_boxed_items');

        $wrapTotal = 0;
//        if($allowGiftMessagesForItems) {
//            foreach ($items as $item){
//                if ($item->getProduct()->isVirtual() || $item->getParentItem()) {
//                    continue;
//                }
//                foreach($giftBoxedItems as $id=>$boxed)
//                {
//                    if($item->getId() == $id && $boxed)
//                    {
//                        $wrapTotal += $giftwrapAmount;// * ($item->getQty());
//                    }
//                }
//            }
//        }
//        else {
//            $wrapTotal = $giftwrapAmount;
//        }

        $session->setData('giftwrapTotalAmount',$giftwrapAmount);

        $address->setSubtotal($address->getSubtotal()+$giftwrapAmount);
        $address->setBaseSubtotal($address->getBaseSubtotal()+$giftwrapAmount);

        $address->setGrandTotal($address->getGrandTotal() + $giftwrapAmount);
        $address->setBaseGrandTotal($address->getBaseGrandTotal() + $giftwrapAmount);
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        if (($address->getAddressType() == 'billing')) {
            $session = Mage::getSingleton('checkout/session');

            $allowGiftMessages = $session->getData('allow_gift_messages');
            $allowGiftMessagesForOrder = $session->getData('allow_gift_messages_for_order');
            $allowGiftMessagesForItems = $session->getData('allow_gift_messages_for_items');
            $holidayGiftWrapOrder = $session->getData('gift_wrap_order');
            $holidayGiftWrapItems = $session->getData('gift_wrap_items');
            $giftBoxedAmount = $session->getData('giftwrapTotalAmount');

            $giftWrapFee = $session->getData('gift_wrap_fee');

            if (!$allowGiftMessages)
            {
                return;
            }

            //$amount = $giftWrapFee;
            $amount = $giftBoxedAmount;

            $giftwrapType = 'regular';
            if($allowGiftMessagesForOrder)
            {
                if($holidayGiftWrapOrder && $holidayGiftWrapOrder == 'holiday') {
                    $giftwrapType = 'holiday';
                }
            }
            else if($allowGiftMessagesForItems) {

                if($holidayGiftWrapItems && $holidayGiftWrapItems == 'holiday') {
                    $giftwrapType = 'holiday';
                }
            }

            $title = Mage::helper('sales')->__('Gift Wrap (type: ' . $giftwrapType . ')');

            if ($amount!=0) {
                $address->addTotal(array(
                    'code'  => $this->getCode(),
                    'title' => $title,
                    'value' => $amount
                ));
            }
        }

        return $this;
    }
}
