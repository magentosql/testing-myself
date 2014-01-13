<?php
class Wsnyc_QuestionsAnswers_Block_Question
    extends Mage_Core_Block_Template
{

    public function __construct(){
        //add filter by category
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/question_collection');
        $collection->addFieldToFilter('category_id',array('eq'=>Mage::app()->getRequest()->getParam('id')));
        $collection->addFieldToFilter('published',array('eq'=>'1'));
        $this->setQuestionCollection($collection);

        $questionParent = $collection->getFirstItem()->getCategoryId();
        unset($collection);

        $category = Mage::getModel('wsnyc_questionsanswers/category')->load($questionParent);

        $collection = Mage::getResourceModel('wsnyc_questionsanswers/category_collection');
        $collection->addFieldToFilter('parent_id',array('eq'=>$category->getParentId()));
        $this->setSiblingCategoryCollection($collection);
        unset($collection);
    }
}