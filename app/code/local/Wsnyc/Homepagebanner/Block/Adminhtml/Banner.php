<?php

class Wsnyc_Homepagebanner_Block_Adminhtml_Banner
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    protected function _construct()
    {
        $this->_blockGroup = 'wsnyc_homepagebanner';
        $this->_controller = 'adminhtml_banner'; // _grid
        $this->_headerText = $this->__('List Banners');

        parent::_construct();
    }
}