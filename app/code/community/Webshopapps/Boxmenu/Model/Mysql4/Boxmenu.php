<?php

class Webshopapps_Boxmenu_Model_Mysql4_Boxmenu extends Mage_Core_Model_Mysql4_Abstract
{
    public function _construct()
    {
        // Note that the boxmenu_id refers to the key field in your database table.
        $this->_init('boxmenu/boxmenu', 'boxmenu_id');
    }



}