<?php

class Wsnyc_TierDiscount_Block_Adminhtml_Renderer_Tier extends Mage_Adminhtml_Block_Widget
    implements Varien_Data_Form_Element_Renderer_Interface
{

    /**
     * Initialize block
     */
    public function __construct()
    {
        $this->setTemplate('wsnyc/tier_discount/amounts.phtml');
    }
    
    public function render(Varien_Data_Form_Element_Abstract $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }
    
    public function getAddButtonHtml()
    {
        return $this->getChildHtml('add_button');
    }
    
    public function getTypes() {
        return array(
            'fixed' => $this->__('Fixed amount for product'),
            'percent' => $this->__('Percent of Product Price')
        );
    }
    
    public function getValues() {
        return unserialize($this->getElement()->getValue());
    }

    /**
     * Prepare global layout
     * Add "Add tier" button to layout
     *
     * @return Mage_Adminhtml_Block_Catalog_Product_Edit_Tab_Price_Tier
     */
    protected function _prepareLayout()
    {
        $button = $this->getLayout()->createBlock('adminhtml/widget_button')
            ->setData(array(
                'label' => Mage::helper('tier_discount')->__('Add Tier'),
                'onclick' => 'return tierDiscountControl.addItem()',
                'class' => 'add'
            ));
        $button->setName('add_tier_discount_item_button');

        $this->setChild('add_button', $button);
        return parent::_prepareLayout();
    }
}