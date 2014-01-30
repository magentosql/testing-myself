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

    public function saveAction() {
        if ($data = $this->getRequest()->getPost()) {
            if (array_key_exists('delete', $data['filename']) && $data['filename']['delete'] == 1) {
                $data['filename'] = '';
            } elseif (is_array($data['filename'])) {
                $data['filename'] = $data['filename']['value'];
            }

            if (isset($_FILES['filename']['name']) && $_FILES['filename']['name'] != '') {
                try {
                    /* Starting upload */
                    $uploader = new Varien_File_Uploader('filename');

                    // Any extention would work
                    $uploader->setAllowedExtensions(array('jpg', 'jpeg', 'gif', 'png'));
                    $uploader->setAllowRenameFiles(false);

                    // Set the file upload mode
                    // false -> get the file directly in the specified folder
                    // true -> get the file in the product like folders
                    //	(file.jpg will go in something like /media/f/i/file.jpg)
                    $uploader->setFilesDispersion(false);

                    // We set media as the upload dir
                    $path = Mage::getBaseDir('media') . DS . 'banner' . DS;
                    $result = $uploader->save($path, $_FILES['filename']['name']);
                    $data['filename'] = 'banner' . DS . $result['file'];
                } catch (Exception $e) {
                    $data['filename'] = 'banner' . DS . $_FILES['filename']['name'];
                }
            }


            $model = Mage::getModel('wsnyc_homepagebanner/banner');
            $model->setData($data)
                ->setId($this->getRequest()->getParam('id'));

            try {
                if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
                    $model->setCreatedTime(now())
                        ->setUpdateTime(now());
                } else {
                    $model->setUpdateTime(now());
                }

                $model->setStores(implode(',', $data['stores']));
                if (isset($data['category_ids'])) {
                    $model->setCategories(implode(',', array_unique(explode(',', $data['category_ids']))));
                }

                $model->save();
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('wsnyc_homepagebanner')->__('Item was successfully saved'));
                Mage::getSingleton('adminhtml/session')->setFormData(false);

                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('*/*/edit', array('id' => $model->getId()));
                    return;
                }
                $this->_redirect('*/*/');
                return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('wsnyc_homepagebanner')->__('Unable to find item to save'));
        $this->_redirect('*/*/');
    }
}