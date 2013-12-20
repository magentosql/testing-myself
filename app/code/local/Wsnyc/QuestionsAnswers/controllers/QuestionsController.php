<?php
class Wsnyc_QuestionsAnswers_QuestionsController
    extends Mage_Core_Controller_Front_Action
{
    /**
     * Category Listing
     */
    public function indexAction ()
    {
        $this->loadLayout();
        $this->renderLayout();

    }

    public function askAction(){

        $request = $this->getRequest();
        if($request->isPost()){
            //Mage::getSingleton('core/session')->addMessage('some message');

            $questionText = $request->getParam('ask_title').PHP_EOL.$request->getParam('ask_text');
            $question = Mage::getModel('wsnyc_questionsanswers/question');

            try{
                $question->setQuestionText($questionText)->save();
                Mage::getSingleton('core/session')->addError($this->__('Your question has been successfully sent'));
            } catch(Exception $e) {
                Mage::getSingleton('core/session')->addSuccess($this->__('There was an error sending the question, please try again later'));
            }


        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function searchAction(){
        $this->loadLayout();
        $this->renderLayout();
    }
}