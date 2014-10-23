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

            Mage::register('rule_data', $ruleModel);

            $this->loadLayout();
            $this->_setActiveMenu('catalog/category');

            $this->_addBreadcrumb(Mage::helper('wsnyc_categorydescriptions')->__('Category Description Manager'), Mage::helper('adminhtml')->__('Category Description Manager'));
            $this->_addBreadcrumb(Mage::helper('wsnyc_categorydescriptions')->__('Rule'), Mage::helper('adminhtml')->__('Rule'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('wsnyc_categorydescriptions/adminhtml_rule_edit'))
                    ->_addLeft($this->getLayout()->createBlock('wsnyc_categorydescriptions/adminhtml_rule_edit_tabs'));

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
            try {
                $postData = $this->getRequest()->getPost();

                if ($postData['rule_id'] == '') {
                    //empty string won't save new dealer
                    $postData['rule_id'] = null;
                }
                /**
                 * @var Zefir_Dealers_Model_Dealer $dealer
                 */
                $dealer = Mage::getModel('wsnyc_categorydescriptions/rule');
                $dealer->setData($postData)
                        ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('wsnyc_categorydescriptions')->__('Rule was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setDealerData(false);

                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setDealerData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

}
