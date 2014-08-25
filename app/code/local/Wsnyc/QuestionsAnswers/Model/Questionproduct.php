<?php
class Wsnyc_QuestionsAnswers_Model_Questionproduct extends Mage_Core_Model_Abstract {

    protected $_eventPrefix = 'questionsanswers_questionproduct';
    
    public function _construct()
    {
        $this->_init('wsnyc_questionsanswers/questionproduct');
    }

}