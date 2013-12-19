<?php
class Wsnyc_QuestionsAnswers_Model_Source_Category {

    public function toOptionArray()
    {
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/category_collection');
        $collection->addFieldToFilter('parent_id',array('eq'=>0));
        $options = array();
        $options[] =array(
            'value' => 0,
            'label' => Mage::helper('wsnyc_questionsanswers')->__('No parent'),
        );
        foreach($collection as $cat){
            $options[] = array(
                'value' => $cat->getCategoryId(),
                'label' => $cat->getName(),
            );
        }
        return $options;
    }
}