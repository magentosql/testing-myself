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

            $questionText = $request->getParam('ask_title').PHP_EOL.$request->getParam('question_text');
            $question = Mage::getModel('wsnyc_questionsanswers/question');

            try{
                $question
                    ->setQuestionText($questionText)
                    ->setAskedName($request->getParam('ask_name'))
                    ->setAskedEmail($request->getParam('ask_email'))
                    ->save();

                $this->_sendEmailToAdmin($question);

                Mage::getSingleton('core/session')->addSuccess($this->__('Your question has been successfully sent'));
            } catch(Exception $e) {
                Mage::getSingleton('core/session')->addError($this->__('There was an error sending the question, please try again later'));
            }
        }

        $this->loadLayout();
        $this->renderLayout();
    }

    public function searchAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    private function _sendEmailToAdmin(Wsnyc_QuestionsAnswers_Model_Question $question){

        $emailTemplate  = Mage::getModel('core/email_template')->loadDefault('admin_question_notifier');
        $emailTemplate->setTemplateSubject('New Question in Ask The Laundress!');
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/ident_support/email'));
        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/ident_support/name'));

        $templateVariables = array();
        $templateVariables['asked_email'] = $question->getAskedEmail();
        $templateVariables['asked_name'] = $question->getAskedName();
        $templateVariables['question_text'] = $question->getQuestionText();

        $emailTemplate->send(
            Mage::getStoreConfig('trans_email/ident_support/email'),
            Mage::getStoreConfig('trans_email/ident_support/name'),
            $templateVariables
        );
    }
}
