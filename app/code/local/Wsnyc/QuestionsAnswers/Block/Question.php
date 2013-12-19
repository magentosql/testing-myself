<?php
class Wsnyc_QuestionsAnswers_Block_Question
    extends Mage_Core_Block_Template
{

    public function __construct(){
        //add filter by category
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/question_collection');
        $this->setQuestionCollection($collection);
    }
}