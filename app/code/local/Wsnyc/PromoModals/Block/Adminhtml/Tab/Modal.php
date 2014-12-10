<?php

class Wsnyc_PromoModals_Block_Adminhtml_Tab_Modal extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel() {
        return Mage::helper('salesrule')->__('Modal Header');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle() {
        return Mage::helper('salesrule')->__('Modal Header');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab() {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden() {
        return false;
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form();
        $model = $this->_getModalModel();

        $fieldset = $form->addFieldset('default_modal_fieldset', array(
            'legend' => Mage::helper('salesrule')->__('Modal Header')
        ));
        
        $fieldset->addField('modal_id', 'hidden', array(
            'name' => 'modal_id',            
        ));
        
        $fieldset->addField('modal_name', 'text', array(
            'name' => 'modal_name',
            'label' => Mage::helper('promomodals')->__('Modal Title'),
            'title' => Mage::helper('promomodals')->__('Modal Title'),
            'required' => false,
        ));
        
        $fieldset->addField('modal_is_active', 'select', array(
            'label'     => Mage::helper('promomodals')->__('Status'),
            'title'     => Mage::helper('promomodals')->__('Status'),
            'name'      => 'modal_is_active',
            'required' => false,
            'options'    => array(
                '1' => Mage::helper('promomodals')->__('Active'),
                '0' => Mage::helper('promomodals')->__('Inactive'),
            ),
        ));
        
        $fieldset->addField('modal_link_name', 'text', array(
            'name' => 'modal_link_name',
            'label' => Mage::helper('promomodals')->__('Header Link Title'),
            'title' => Mage::helper('promomodals')->__('Header Link Title'),
            'required' => false,
        ));
        
        $fieldset->addField('modal_description', 'textarea', array(
            'name' => 'modal_description',
            'label' => Mage::helper('promomodals')->__('Description'),
            'title' => Mage::helper('promomodals')->__('Description'),
            'style' => 'height: 100px;',
        ));

        $form->setValues($model->getData());
        $this->setForm($form);        
        return parent::_prepareForm();
    }

    /**
     * Fetch modal for current promo rule
     * 
     * @return Wsnyc_PromoModals_Model_Modal
     */
    protected function _getModalModel() {
        $rule = Mage::registry('current_promo_quote_rule');
        $modal = Mage::getModel('promomodals/modal')->load($rule->getId(), 'rule_id');
        
        return $modal;
    }
}
