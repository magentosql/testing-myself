<?php
class Wsnyc_QuestionsAnswers_Model_Resource_Question
    extends Mage_Core_Model_Resource_Db_Abstract {

    public function _construct()
    {
        $this->_init('wsnyc_questionsanswers/question','question_id');
    }

}