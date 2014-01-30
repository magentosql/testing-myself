<?php
class Wsnyc_QuestionsAnswers_Model_Source_Subcategory {

    public function toOptionArray()
    {
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/category_collection');
        $collection->addFieldToFilter('parent_id',array('eq'=>0));
        $options = array();

        foreach($collection as $parent){

            $subCollection = Mage::getResourceModel('wsnyc_questionsanswers/category_collection');
            $subCollection->addFieldToFilter('parent_id',array('eq'=>$parent->getCategoryId()));

            $optionValues = array();
            foreach($subCollection as $cat){
                $optionValues[] = array(
                    'value' => $cat->getCategoryId(),
                    'label' => $cat->getName(),
                );
            }

            $option = array(
                'label' => $parent->getName(),
                'value' => $optionValues,
            );

            $options[] = $option;

        }
        return $options;
    }
}