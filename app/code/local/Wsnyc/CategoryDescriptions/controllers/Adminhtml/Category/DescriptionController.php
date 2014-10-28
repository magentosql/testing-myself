<?php

class Wsnyc_CategoryDescriptions_Adminhtml_Category_DescriptionController extends Mage_Adminhtml_Controller_Action {

    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('catalog/category')
                ->_addBreadcrumb(Mage::helper('wsnyc_categorydescriptions')->__('Category Description Manager'), Mage::helper('wsnyc_categorydescriptions')->__('Category Description Manager'));
        return $this;
    }

    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('wsnyc_categorydescriptions/adminhtml_rule'));
        $this->renderLayout();
    }

    public function editAction() {
        $ruleId = $this->getRequest()->getParam('id');
        $ruleModel = Mage::getModel('wsnyc_categorydescriptions/rule')->load($ruleId);

        if ($ruleModel->getId() || $ruleId == 0) {
            
            $ruleModel->getConditions()->setJsFormObject('rule_conditions_fieldset');

            Mage::register('rule_data', $ruleModel);

            $this->loadLayout();
            
            $this->_setActiveMenu('catalog/category');

            $this->_addBreadcrumb(Mage::helper('wsnyc_categorydescriptions')->__('Category Description Manager'), Mage::helper('adminhtml')->__('Category Description Manager'));
            $this->_addBreadcrumb(Mage::helper('wsnyc_categorydescriptions')->__('Rule'), Mage::helper('adminhtml')->__('Rule'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('wsnyc_categorydescriptions')->__('Rule does not exist'));
            $this->_redirect('*/*/');
        }
    }

    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * Save edit form
     */
    public function saveAction() {
        if ($this->getRequest()->getPost()) {
            
            $postData = $this->getRequest()->getPost();
            if ($postData['rule_id'] == '') {
                //empty string won't save new rule
                $postData['rule_id'] = null;
            }
            $session = Mage::getSingleton('adminhtml/session');
            /**
             * @var Wsnyc_CategoryDescriptions_Model_Rule $rule
             */
            $rule = Mage::getModel('wsnyc_categorydescriptions/rule');            
            if (isset($postData['rule']['conditions'])) {
                $postData['conditions'] = $postData['rule']['conditions'];
            }
            unset($postData['rule']);
            $rule->loadPost($postData);
            $session->setRuleData($rule->getData());
            
            $rule->save();
            $session->addSuccess(Mage::helper('wsnyc_categorydescriptions')->__('The rule has been saved.'));
            $session->setRuleData(false);
            if ($this->getRequest()->getParam('back')) {
                $this->_redirect('*/*/edit', array('id' => $rule->getId()));
                return;
            }
            $this->_redirect('*/*/');
            return;
        }
        
        $this->_redirect('*/*/');
    }
    
    public function newConditionHtmlAction() {
        $id = $this->getRequest()->getParam('id');
        $typeArr = explode('|', str_replace('-', '/', $this->getRequest()->getParam('type')));
        $type = $typeArr[0];

        $model = Mage::getModel($type)
                ->setId($id)
                ->setType($type)
                ->setRule(Mage::getModel('wsnyc_categorydescriptions/rule'))
                ->setPrefix('conditions');
        if (!empty($typeArr[1])) {
            $model->setAttribute($typeArr[1]);
        }

        if ($model instanceof Mage_Rule_Model_Condition_Abstract) {
            $model->setJsFormObject($this->getRequest()->getParam('form'));
            $html = $model->asHtmlRecursive();
        } else {
            $html = '';
        }
        $this->getResponse()->setBody($html);
    }

}
