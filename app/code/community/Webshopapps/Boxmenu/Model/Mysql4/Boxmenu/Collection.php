<?php

class Webshopapps_Boxmenu_Model_Mysql4_Boxmenu_Collection extends Mage_Core_Model_Mysql4_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('boxmenu/boxmenu');
    }

    public function setBoxType($boxType)
    {
        $this->_select->where("box_type = ?", $boxType);

        return $this;
    }
}