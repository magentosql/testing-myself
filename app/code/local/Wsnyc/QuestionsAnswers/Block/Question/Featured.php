<?php
class Wsnyc_QuestionsAnswers_Block_Question_Featured
    extends Mage_Core_Block_Template
{

    public function __construct(){
        //add filter by category
        $collection = Mage::getResourceModel('wsnyc_questionsanswers/question_collection');
        $collection->addFieldToFilter('published',array('eq'=>'1'));
        $collection->addFieldToFilter('featured',array('eq'=>'1'));
        $this->setQuestionCollection($collection);
        unset($collection);
    }


    public function getSlideshowCfg()
    {
        $h = Mage::helper('wsnyc_homepagebanner');

        $cfg = array();
        $cfg['navigation']			= $h->getCfg('general/navigation');
        $cfg['slideSpeed']		= $h->getCfg('general/slideSpeed');
        $cfg['paginationSpeed']		= $h->getCfg('general/paginationSpeed');
        $cfg['singleItem']		= $h->getCfg('general/singleItem');

        return $cfg;
    }
}