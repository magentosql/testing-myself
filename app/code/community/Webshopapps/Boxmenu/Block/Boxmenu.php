<?php
class Webshopapps_Boxmenu_Block_Boxmenu extends Mage_Core_Block_Template
{
	public function _prepareLayout()
    {
		return parent::_prepareLayout();
    }

     public function getBoxmenu()
     {
        if (!$this->hasData('boxmenu')) {
            $this->setData('boxmenu', Mage::registry('boxmenu'));
        }
        return $this->getData('boxmenu');

    }
}