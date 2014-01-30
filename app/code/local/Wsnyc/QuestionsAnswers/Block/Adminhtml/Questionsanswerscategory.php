<?php
class Wsnyc_QuestionsAnswers_Block_Adminhtml_Questionsanswerscategory
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        $this->_blockGroup = 'wsnyc_questionsanswers';
        $this->_controller = 'adminhtml_questionsanswerscategory';
        $this->_headerText = $this->__('Categories');

        parent::__construct();
    }
}