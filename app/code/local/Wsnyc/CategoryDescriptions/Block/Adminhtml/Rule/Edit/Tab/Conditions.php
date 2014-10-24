<?php

class Wsnyc_CategoryDescriptions_Block_Adminhtml_Rule_Edit_Tab_Conditions extends Mage_Adminhtml_Block_Widget_Form implements Mage_Adminhtml_Block_Widget_Tab_Interface {
    
    /**
     * Prepare content for tab
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('wsnyc_categorydescriptions')->__('Conditions');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('wsnyc_categorydescriptions')->__('Conditions');
    }

    /**
     * Returns status flag about this tab can be showen or not
     *
     * @return true
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Returns status flag about this tab hidden or not
     *
     * @return true
     */
    public function isHidden()
    {
        return false;
    }

    protected function _prepareForm() {
        $model = Mage::registry('rule_data');
        $form = new Varien_Data_Form();

        $form->setHtmlIdPrefix('rule_');

        $renderer = Mage::getBlockSingleton('adminhtml/widget_form_renderer_fieldset')
            ->setTemplate('promo/fieldset.phtml')
            ->setNewChildUrl($this->getUrl('*/category_description/newConditionHtml'));

        $fieldset = $form->addFieldset('conditions_fieldset', array(
            'legend'=>Mage::helper('wsnyc_categorydescriptions')->__('Apply the rule only if the following conditions are met (leave blank for all products)')
        ))->setRenderer($renderer);

        $fieldset->addField('conditions', 'text', array(
            'name' => 'conditions',
            'label' => Mage::helper('wsnyc_categorydescriptions')->__('Conditions'),
            'title' => Mage::helper('wsnyc_categorydescriptions')->__('Conditions'),
        ))->setRule($model)->setRenderer(Mage::getBlockSingleton('wsnyc_categorydescriptions/renderer_conditions'));

        $form->setValues($model->getData());

        $this->setForm($form);

        return parent::_prepareForm();
    }

}
