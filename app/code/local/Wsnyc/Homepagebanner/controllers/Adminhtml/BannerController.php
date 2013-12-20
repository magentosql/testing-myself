<?php

class Wsnyc_Homepagebanner_Adminhtml_BannerController
    extends  Mage_Adminhtml_Controller_Action
{
    /**
     * init layout and set active for current menu
     *
     * @return Wsnyc_Homepagebanner_Adminhtml_BannerController
     */
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('cms/banner')
            ->_addBreadcrumb(
                Mage::helper('adminhtml')->__('Homepage Banner'),
                Mage::helper('adminhtml')->__('Homepage Banner')
            );
        return $this;
    }

    public function indexAction()
    {
        /*
		 * Reditect user via 302 http redirect (the browser url changes)
		 */
        $this->_redirect('*/*/list');
    }

    public function listAction()
    {
        // housekeeping
        $this->_getSession()->setFormData(array());

        $this->_title($this->__('Homepage Banner Dashboard'))
            ->_title($this->__('Homepage Banner Dashboard'));
        $this->_initAction()->renderLayout();
    }

    public function newAction()
    {
        /*
         * Redirect the user via a magento internal redirect
         */
        $this->_forward('edit');
    }

    public function editAction() {
        $id = $this->getRequest()->getParam('id');
        $model = Mage::getModel('wsnyc_homepagebanner/banner')->load($id);

        if ($model->getId() || $id == 0) {
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                $model->setData($data);
            }

            Mage::register('wsnyc_homepagebanner_data', $model);

            $this->_title($this->__('Home Page Banner'))
                ->_title($this->__('Manage banner'));
            if ($model->getId()) {
                $this->_title($model->getTitle());
            } else {
                $this->_title($this->__('New Banner'));
            }
            //echo "test";die;
            $this->loadLayout();
            $this->_setActiveMenu('cms/banner');
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
            $this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

            $this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

            $this->_addContent($this->getLayout()->createBlock('wsnyc_homepagebanner/adminhtml_banner_edit'));
            $this->_addLeft($this->getLayout()->createBlock('wsnyc_homepagebanner/adminhtml_banner_edit_tabs'));

            $this->renderLayout();

        } else {
            Mage::getSingleton('adminhtml/session')->addError(Mage::helper('wsnyc_homepagebanner')->__('Item does not exist'));
            $this->_redirect('*/*/');
        }
    }
}