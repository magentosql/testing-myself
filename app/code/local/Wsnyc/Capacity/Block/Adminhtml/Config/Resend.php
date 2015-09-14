<?php

class Wsnyc_Capacity_Block_Adminhtml_Config_Resend extends Mage_Adminhtml_Block_System_Config_Form_Field
{
    /*
     * Set template
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('wsnyc/capacity/config/button.phtml');
    }

    /**
     * Return element html
     *
     * @param  Varien_Data_Form_Element_Abstract $element
     * @return string
     */
    protected function _getElementHtml(Varien_Data_Form_Element_Abstract $element)
    {
        return $this->_toHtml();
    }

    /**
     * Return ajax url for button
     *
     * @return string
     */
    public function getActionUrl()
    {
        return Mage::helper('adminhtml')->getUrl('adminhtml/capacity/resend');
    }

    /**
     * Generate button html
     *
     * @return string
     */
    public function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'id' => 'wsnyc_capacity_button',
                'label' => $this->helper('adminhtml')->__('Resend'),
                'onclick' => "window.location.href='{$this->getActionUrl()}'"
            ));

        return $button->toHtml();
    }
}