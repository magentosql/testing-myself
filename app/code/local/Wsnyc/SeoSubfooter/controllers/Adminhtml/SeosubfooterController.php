<?php

class Wsnyc_SeoSubfooter_Adminhtml_SeosubfooterController extends Mage_Adminhtml_Controller_Action {

    /**
     * Basic initialization 
     * @return \Zefir_Dealers_Adminhtml_DealersController
     */
    protected function _initAction() {
        $this->loadLayout()
                ->_setActiveMenu('cms/seoblurbs')
                ->_addBreadcrumb(Mage::helper('adminhtml')->__('Manage Blurbs'), Mage::helper('adminhtml')->__('Manage Blurbs'));
        return $this;
    }

    /**
     * List all dealers
     */
    public function indexAction() {
        $this->_initAction();
        $this->_addContent($this->getLayout()->createBlock('seosubfooter/adminhtml_blurb'));
        $this->renderLayout();
    }

    /**
     * Create new dealer
     */
    public function newAction() {
        $this->_forward('edit');
    }

    /**
     * Show edit form for new and existing dealer
     */
    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $blurb = Mage::getModel('seosubfooter/blurb')->load($id);
        if ($blurb->getId() || $id == 0) {
            Mage::register('blurb_data', $blurb);
            $this->_initAction();
            $this->_addBreadcrumb(Mage::helper('seosubfooter')->__('Edit Blurb'), Mage::helper('seosubfooter')->__('Edit Blurb'));                        
            $this->_addContent($this->getLayout()->createBlock('seosubfooter/adminhtml_blurb_edit'))
                    ->_addLeft($this->getLayout()->createBlock('seosubfooter/adminhtml_blurb_edit_tabs'));
            $this->renderLayout();
        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosubfooter')->__('Blurb does not exist'));
            $this->_redirect('*/*/');
        }
    }

    /**
     * Save edit form
     */
    public function saveAction() {
        if ($this->getRequest()->getPost()) {
            try {
                $postData = $this->getRequest()->getPost();

                if ($postData['blurb_id'] == '') {
                    //empty string won't save new dealer
                    $postData['blurb_id'] = null;
                }
                /**
                 * @var Zefir_Dealers_Model_Dealer $dealer
                 */
                $blurb = Mage::getModel('seosubfooter/blurb');
                $blurb->setData($postData)
                        ->save();

                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('seosubfooter')->__('Blurb was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setBlurbData(false);
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setBlurbData($this->getRequest()->getPost());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Delete dealer
     */
    public function deleteAction() {
        if ($this->getRequest()->getParam('id') > 0) {
            try {
                $blurb = Mage::getModel('seosubfooter/blurb');
                $blurb->setId($this->getRequest()->getParam('id'))->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('seosubfooter')->__('Blurb was successfully deleted'));
                $this->_redirect('*/*/');
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
            }
        }
        $this->_redirect('*/*/');
    }

    /**
     * Mass delete
     */
    public function massDeleteAction() {
        $blurbIds = $this->getRequest()->getParam('blurb_id');
        if (!is_array($blurbIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosubfooter')->__('Please select blurb(s).'));
        } else {
            $blurb = Mage::getModel('seosubfooter/blurb');
            foreach ($blurbIds as $blurbId) {
                $blurb->load($blurbId)->delete();
            }
            try {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('seosubfooter')->__('Total of %d blurb(s) were deleted.', count($blurbIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Mass status update
     */
    public function massStatusAction() {
        $blurbIds = $this->getRequest()->getParam('blurb_id');
        if (!is_array($blurbIds)) {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('seosubfooter')->__('Please select blurb(s).'));
        } else {
            $newStatus = $this->getRequest()->getParam('status') == '1' ? 1 : 0;
            $blurb = Mage::getModel('seosubfooter/blurb');
            foreach ($blurbIds as $blurbId) {
                $blurb->load($blurbId)->setStatus($newStatus)->save();
            }
            try {
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('seosubfooter')->__('Total of %d blurb(s) were deleted.', count($blurbIds)));
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    /**
     * Ajax grid refresh
     */
    public function gridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('seosubfooter/adminhtml_blurb_grid')->toHtml()
        );
    }
}
