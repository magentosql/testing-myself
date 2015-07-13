<?php
class Wsnyc_QuestionsAnswers_Model_Answer extends Mage_Core_Model_Abstract {

    protected $_eventPrefix = 'questionsanswers_answer';
    
    public function _construct()
    {
        $this->_init('wsnyc_questionsanswers/answer');
    }

    public function hasHtml() {
        return trim($this->getAnswerText()) != trim(strip_tags($this->getAnswerText()));
    }
}