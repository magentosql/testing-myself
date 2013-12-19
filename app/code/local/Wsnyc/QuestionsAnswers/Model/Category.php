<?php
class Wsnyc_QuestionsAnswers_Model_Category extends Mage_Core_Model_Abstract {

    public function _construct()
    {
        $this->_init('wsnyc_questionsanswers/category');
    }

    public function getQuestionsCollection(){
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/question_collection');
        $collection->addFieldToFilter('category_id',array('eq'=>$this->getCategoryId()));
        return $collection;
    }

    public function getSubcategoriesCollection(){
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/category_collection');
        $collection->addFieldToFilter('parent_id',array('eq'=>$this->getCategoryId()));
        return $collection;
    }

}