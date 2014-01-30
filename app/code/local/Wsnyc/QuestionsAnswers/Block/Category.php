<?php
class Wsnyc_QuestionsAnswers_Block_Category
    extends Mage_Core_Block_Template
{

    public function __construct(){
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/category_collection');
        $collection->addFieldToFilter('parent_id',array('eq'=>0));
        $this->setCategoryCollection($collection);
    }

}