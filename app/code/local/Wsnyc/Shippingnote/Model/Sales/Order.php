<?php
/**
 * Adds two new methods to be used in email templates
 */
class Wsnyc_Shippingnote_Model_Sales_Order extends Mage_Sales_Model_Order {
    
    public function getGiftMessageText() {
        $giftMessageId = parent::getGiftMessageId();
        return Mage::getModel('giftmessage/message')->load($giftMessageId)->getMessage();
    }
    
    public function getGiftwrapBoxType(){
        //$commentInfo = parent::getOnestepcheckoutOrderComment();
        //$parts = explode(' : ',$commentInfo);
        //if(isset($parts[1])){
        //    return strip_tags($parts[1]);
        //}
        return parent::getOnestepcheckoutGiftwrapType();
    }
    
    public function getSignatureRequired(){
        $flag = parent::getSignatureRequired();
        if ($flag){
            return 'Yes';
        }
        return 'No';
    }

    public function getBoxedSeparately()
    {
        $separately = parent::getGiftBoxedSeparately();
        if($separately){
            return 'Yes';
        }
        return 'No';
    }
    
    /**
     * Overriden to order them by shipment vendor
     * @return type
     */
    public function getAllItems()
    {
        $items = array();
        $itemsCollection = Mage::getResourceModel('sales/order_item_collection');
        $itemsCollection = $itemsCollection->addFieldToFilter('order_id',$this->getId());
        $itemsCollection = $itemsCollection->setOrder('udropship_vendor')->load();

        foreach ($itemsCollection as $item) {

            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

}
