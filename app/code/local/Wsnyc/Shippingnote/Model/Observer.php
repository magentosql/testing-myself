<?php 

class Wsnyc_Shippingnote_Model_Observer{

    public function salesOrderSaveBefore(){
        Mage::log('firing my observer');
    }

}