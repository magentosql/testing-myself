<?php
class Webshopapps_Boxmenu_Block_Adminhtml_Boxmenu extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_boxmenu';
    $this->_blockGroup = 'boxmenu';
    $this->_headerText = Mage::helper('boxmenu')->__('WebShopApps Dimensional Box Manager');
    $this->_addButtonLabel = Mage::helper('boxmenu')->__('Add Box Definition');
    parent::__construct();
  }
}