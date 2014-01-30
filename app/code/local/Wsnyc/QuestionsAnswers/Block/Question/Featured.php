<?php
class Wsnyc_QuestionsAnswers_Block_Question_Featured
    extends Mage_Core_Block_Template
{

    public function __construct(){
        //add filter by category
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/question_collection');
        $collection->addFieldToFilter('published',array('eq'=>'1'));
        $collection->addFieldToFilter('featured',array('eq'=>'1'));
        $collection->clear()->setPageSize(5)->load();
        $this->setQuestionCollection($collection);
        unset($collection);
    }

}