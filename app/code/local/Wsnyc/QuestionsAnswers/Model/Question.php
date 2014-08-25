<?php
class Wsnyc_QuestionsAnswers_Model_Question
    extends Mage_Core_Model_Abstract {
    
    protected $_eventPrefix = 'questionsanswers_question';

    public function _construct()
    {
        $this->_init('wsnyc_questionsanswers/question');
    }

    public function getAnswersCollection(){
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/answer_collection');
        $collection->addFieldToFilter('question_id',array('eq'=>$this->getQuestionId()));
        return $collection;
    }

    public function getProductsCollection(){
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/questionproduct_collection');
        $collection->addFieldToFilter('question_id',array('eq'=>$this->getQuestionId()));
        return $collection;
    }
}