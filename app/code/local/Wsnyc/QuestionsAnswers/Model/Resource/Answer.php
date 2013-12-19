<?php
class Wsnyc_QuestionsAnswers_Model_Resource_Answer
    extends Mage_Core_Model_Resource_Db_Abstract {

    public function _construct()
    {
        $this->_init('wsnyc_questionsanswers/answer','answer_id');
    }

}