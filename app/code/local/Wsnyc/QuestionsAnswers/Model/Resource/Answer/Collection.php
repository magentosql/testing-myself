<?php
class Wsnyc_QuestionsAnswers_Model_Resource_Answer_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        $this->_init('wsnyc_questionsanswers/answer');
    }
}