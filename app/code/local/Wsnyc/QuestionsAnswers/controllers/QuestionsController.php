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
        $success = 0;
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
                $success = 1;
            } catch(Exception $e) {
                Mage::getSingleton('core/session')->addError($this->__('There was an error sending the question, please try again later'));
            }
            if($request->getParam('ask_newsletter') == true) {
                $client = new Varien_Http_Client('http://thelaundress.us6.list-manage1.com/subscribe/post?u=d3d48e75efd637e646b0beb3c&id=dbfb7e7934');
                $client->setMethod(Varien_Http_Client::POST);
                $client->setParameterPost('FNAME', $request->getParam('ask_name'))
                        ->setParameterPost('EMAIL', $request->getParam('ask_email'))
                        ->setParameterPost('b_d3d48e75efd637e646b0beb3c_dbfb7e7934', "");
                try {
                    $response = $client->request();
                    if ($response->isSuccessful()) {
                        if(strpos($response->getBody(), 'There are errors below') !== false) {
                            Mage::getSingleton('core/session')->addNotice($this->__('There was an error when subscribing to newsletter. Please try again later or check if you\'re not subscribed already.'));
                        }
                    }
                } catch (Exception $e) {
                    Mage::getSingleton('core/session')->addError($this->__('There was an error when subscribing to newsletter.'));
                }
            }
        }

        $this->loadLayout();
        if ($request->isPost() && $success) {
          $this->getLayout()->getBlock('questionsanswers_questions_ask')->setEvent('question_subscription');
        }
        $this->renderLayout();
    }

    public function searchAction(){
        $this->loadLayout();
        $this->renderLayout();
    }

    private function _sendEmailToAdmin(Wsnyc_QuestionsAnswers_Model_Question $question){

        $emailTemplate  = Mage::getModel('core/email_template')->loadDefault('admin_question_notifier');
        $emailTemplate->setTemplateSubject('New Question in Ask The Laundress!');
        $emailTemplate->setSenderEmail(Mage::getStoreConfig('trans_email/wsnyc_questionsanswers_admin_notify/email'));
        $emailTemplate->setSenderName(Mage::getStoreConfig('trans_email/wsnyc_questionsanswers_admin_notify/name'));

        $templateVariables = array();
        $templateVariables['asked_email'] = $question->getAskedEmail();
        $templateVariables['asked_name'] = $question->getAskedName();
        $templateVariables['question_text'] = $question->getQuestionText();

        $emailTemplate->send(
            Mage::getStoreConfig('trans_email/wsnyc_questionsanswers_admin_notify/email'),
            Mage::getStoreConfig('trans_email/wsnyc_questionsanswers_admin_notify/name'),
            $templateVariables
        );
    }
}
