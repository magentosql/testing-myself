<?php
class Wsnyc_QuestionsAnswers_Adminhtml_QuestionsanswerscategoryController
     extends Mage_Adminhtml_Controller_Action
{

    public function indexAction()
    {
        // Let's call our initAction method which will set some basic params for each action
        $this->_initAction()
            ->renderLayout();
    }

    public function newAction()
    {
        // We just forward the new action to a blank edit form
        $this->_forward('edit');
    }

    public function editAction()
    {
        $this->_initAction();

        // Get id if available
        $id  = $this->getRequest()->getParam('category_id');
        $model = Mage::getModel('wsnyc_questionsanswers/category');

        if ($id) {
            // Load record
            $model->load($id);

            // Check if record is loaded
            if (!$model->getCategoryId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This category no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getCategoryId() ? $model->getName() : $this->__('New Category'));

        $data = Mage::getSingleton('adminhtml/session')->getCategoryData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('wsnyc_questionsanswers', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Category') : $this->__('New Category'), $id ? $this->__('Edit Category') : $this->__('New Category'))
            ->_addContent($this->getLayout()->createBlock('wsnyc_questionsanswers/adminhtml_questionsanswerscategory_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {

            if($_FILES['image']['name'] != '') {

                try {

                    $uploader = new Varien_File_Uploader('image');
                    $uploader->setAllowedExtensions(array('jpg','jpeg','gif','png'));
                    $uploader->setAllowRenameFiles(false);
                    $uploader->setFilesDispersion(false);

                    $path = Mage::getBaseDir('media') . DS .'wsnyc_questionsanswers'. DS;

                    $result = $uploader->save($path, $_FILES['image']['name']);
                } catch (Exception $e) {

                }

                $postData['image'] = $result['file'];

            } else {
                if(isset($postData['image']['delete']) && $postData['image']['delete'] == 1)
                    $postData['image'] = '';
                else
                    unset($postData['image']);
            }





            $model = Mage::getSingleton('wsnyc_questionsanswers/category');
            $model->setData($postData);

            //create category url identifier in format: category/subcategory

            try {
                $model->save();

                if ($model->getParentId() > 0) {
                    $parent = Mage::getModel('wsnyc_questionsanswers/category')->load($model->getParentId());
                    $model->setIdentifier(Mage::getSingleton('wsnyc_questionsanswers/category')->formatUrlKey($parent->getName() . '/' . $model->getName()));
                    $model->save();
                }

                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The category has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this category.'));
            }

            Mage::getSingleton('adminhtml/session')->setCategoryData($postData);
            $this->_redirectReferer();
        }
    }

    public function messageAction()
    {
        $data = Mage::getModel('wsnyc_questionsanswers/category')->load($this->getRequest()->getParam('category_id'));
        echo $data->getContent();
    }

    /**
     * Initialize action
     *
     * Here, we set the breadcrumbs and the active menu
     *
     * @return Mage_Adminhtml_Controller_Action
     */
    protected function _initAction()
    {
        $this->loadLayout()
            // Make the active menu match the menu config nodes (without 'children' inbetween)
            ->_setActiveMenu('questionsanswers/category')
            ->_title($this->__('Q&A'))->_title($this->__('Categories'))
            ->_addBreadcrumb($this->__('Category'), $this->__('Category'));
            //->_addBreadcrumb($this->__('sub'), $this->__('sub'));

        return $this;
    }

    /**
     * Delete the question
     *
     */
    public function deleteAction()
    {
        if ($objectId = $this->getRequest()->getParam('category_id')) {
            $object = Mage::getModel('wsnyc_questionsanswers/category')->load($objectId);

            if ($object->getId()) {
                try {
                    $object->delete();
                    $this->_getSession()->addSuccess($this->__('The category was deleted.'));
                }
                catch (Exception $e) {
                    $this->_getSession()->addError($e->getMessage());
                }
            }
        }

        $this->_redirect('*/*');
    }

    /**
     * Check currently called action by permissions for current user
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('questionsanswers/category');
    }



}