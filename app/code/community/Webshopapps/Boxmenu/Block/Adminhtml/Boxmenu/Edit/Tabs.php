<?php

class Webshopapps_Boxmenu_Block_Adminhtml_Boxmenu_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{

  public function __construct()
  {
      parent::__construct();
      $this->setId('boxmenu_tabs');
      $this->setDestElementId('edit_form');
      $this->setTitle(Mage::helper('boxmenu')->__('WebShopApps Box Information'));
  }

  protected function _beforeToHtml()
  {
      $this->addTab('form_section', array(
          'label'     => Mage::helper('boxmenu')->__('WebShopApps Box Information'),
          'title'     => Mage::helper('boxmenu')->__('WebShopApps Box Information'),
          'content'   => $this->getLayout()->createBlock('boxmenu/adminhtml_boxmenu_edit_tab_form')->toHtml(),
      ));

      return parent::_beforeToHtml();
  }
}