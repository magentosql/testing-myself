<?php

class Wsnyc_CategoryDescriptions_Block_Adminhtml_Rule_Edit_Form extends Mage_Adminhtml_Block_Widget_Form {

    protected function _prepareLayout() {
        $this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
        $this->getLayout()->getBlock('head')->setCanLoadRulesJs(true);
        return parent::_prepareLayout();
    }

    protected function _prepareForm() {
        $form = new Varien_Data_Form(array(
            'id' => 'edit_form',
            'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
            'method' => 'post',
                )
        );

        $form->setUseContainer(true);
        $this->setForm($form);
        parent::_prepareForm();
    }

}
