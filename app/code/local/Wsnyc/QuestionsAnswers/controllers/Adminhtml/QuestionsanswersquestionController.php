<?php

class Wsnyc_QuestionsAnswers_Adminhtml_QuestionsanswersquestionController
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
        $id  = $this->getRequest()->getParam('question_id');
        $model = Mage::getModel('wsnyc_questionsanswers/question');

        if ($id) {
            // Load record
            $model->load($id);

            // Check if record is loaded
            if (!$model->getQuestionId()) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('This question no longer exists.'));
                $this->_redirect('*/*/');

                return;
            }
        }

        $this->_title($model->getQuestionId() ? $model->getName() : $this->__('New Question'));

        $data = Mage::getSingleton('adminhtml/session')->getQuestionData(true);
        if (!empty($data)) {
            $model->setData($data);
        }

        Mage::register('wsnyc_questionsanswers', $model);

        $this->_initAction()
            ->_addBreadcrumb($id ? $this->__('Edit Question') : $this->__('New Question'), $id ? $this->__('Edit Question') : $this->__('New Question'))
            ->_addContent($this->getLayout()->createBlock('wsnyc_questionsanswers/adminhtml_questionsanswersquestion_edit')->setData('action', $this->getUrl('*/*/save')))
            ->renderLayout();
    }

    public function saveAction()
    {
        if ($postData = $this->getRequest()->getPost()) {

            $model = Mage::getSingleton('wsnyc_questionsanswers/question');
            $model->setData($postData);

            try {
                //save question
                $model->save();

                $questionId = $model->getId();

                //save answer
                $answer = Mage::getModel('wsnyc_questionsanswers/answer')->load($questionId,'question_id');
                if(!$answer){
                    $answer = Mage::getModel('wsnyc_questionsanswers/answer');
                }
                $answerText = $postData['answer_text'];
                $answer->setQuestionId($questionId)->setAnswerText($answerText)
                    ->save();

                //save associated products
                $newAssociatedProducts = $postData['products'];
                $currentProducts = Mage::getResourceModel('wsnyc_questionsanswers/questionproduct_collection');
                $currentProducts->addFieldToFilter('question_id',array('eq'=>$model->getQuestionId()));
                foreach($currentProducts as $qp){
                    $qp->delete();
                }

                $newAssociatedProducts = explode(PHP_EOL,$newAssociatedProducts);
                foreach($newAssociatedProducts as $nap){
                    if(trim($nap)!=''){
                        $questionproduct =  Mage::getModel('wsnyc_questionsanswers/questionproduct');
                        $questionproduct->setQuestionId($model->getQuestionId())
                            ->setData('product_sku',trim($nap));
                        $questionproduct->save();
                        unset($questionproduct);
                    }
                }


                Mage::getSingleton('adminhtml/session')->addSuccess($this->__('The question has been saved.'));
                $this->_redirect('*/*/');

                return;
            }
            catch (Mage_Core_Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
            catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($this->__('An error occurred while saving this question.'));
            }

            Mage::getSingleton('adminhtml/session')->setQuestionData($postData);
            $this->_redirectReferer();
        }
    }

    public function messageAction()
    {
        $data = Mage::getModel('wsnyc_questionsanswers/question')->load($this->getRequest()->getParam('question_id'));
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
            ->_setActiveMenu('questionsanswers/question')
            ->_title($this->__('Q&A'))->_title($this->__('Questions'))
            ->_addBreadcrumb($this->__('Question'), $this->__('Question'));
        //->_addBreadcrumb($this->__('sub'), $this->__('sub'));

        return $this;
    }

    /**
     * Delete the question
     *
     */
    public function deleteAction()
    {
        if ($objectId = $this->getRequest()->getParam('question_id')) {
            $object = Mage::getModel('wsnyc_questionsanswers/question')->load($objectId);

            if ($object->getId()) {
                try {
                    $object->delete();
                    $this->_getSession()->addSuccess($this->__('The question was deleted.'));
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
        return Mage::getSingleton('admin/session')->isAllowed('questionsanswers/question');
    }
}