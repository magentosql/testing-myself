<?php
class Wsnyc_QuestionsAnswers_Block_Question
    extends Mage_Core_Block_Template
{

    public function __construct(){
        //add filter by category
        $categoryId = Mage::app()->getRequest()->getParam('id');
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/question_collection');
        $collection->addFieldToFilter('category_id',array('eq'=>$categoryId));
        $collection->addFieldToFilter('published',array('eq'=>'1'));
        $this->setCategoryId(Mage::app()->getRequest()->getParam('id'));
        $this->setQuestionCollection($collection);
        unset($collection);

        $category = Mage::getModel('wsnyc_questionsanswers/category')->load($categoryId);

        $collection = Mage::getResourceModel('wsnyc_questionsanswers/category_collection');
        $collection->addFieldToFilter('parent_id',array('eq'=>$category->getParentId()));
        $this->setSiblingCategoryCollection($collection);
        unset($collection);
    }
}
