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
        $commentInfo = parent::getOnestepcheckoutOrderComment();
        $parts = explode(' : ',$commentInfo);
        if(isset($parts[1])){
            return strip_tags($parts[1]);
        }
        return '';
    }
    
    public function getSignatureRequired(){
        $flag = parent::getSignatureRequired();
        if ($flag){
            return 'Yes';
        }
        return 'No';
    }
}
