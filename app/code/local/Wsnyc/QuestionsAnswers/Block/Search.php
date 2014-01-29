<?php
class Wsnyc_QuestionsAnswers_Block_Search
    extends Mage_Core_Block_Template
{

    public function __construct(){

        $request = Mage::app()->getRequest();
        if($request->getPost()){

            $phrase = $request->getParam('questionsanswersquery');

            $collection = Mage::getResourceModel('wsnyc_questionsanswers/question_collection');
            $collection->addFieldToFilter('question_text',array('like'=>'%'.$phrase.'%'));
            $collection->addFieldToFilter('published',array('eq'=>'1'));
            $select = $collection->getSelect()->joinLeft(
                array(
                    'answers'=> Mage::getSingleton('core/resource')->getTableName('wsnyc_questionsanswers/answer')
                ),
                'main_table.question_id = answers.question_id',
                'answers.*'
            )->orwhere('answer_text LIKE ?', '%'.$phrase.'%');

            $this->setData('questions',$collection);
            $this->setData('phrase', $phrase);

        } else {
            Mage::getSingleton('core/session')->addSuccess($this->__('You have not entered a phrase to search for'));
        }
    }
}


