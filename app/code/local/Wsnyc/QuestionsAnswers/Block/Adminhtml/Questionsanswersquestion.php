<?php
class Wsnyc_QuestionsAnswers_Block_Adminhtml_Questionsanswersquestion
    extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        // The blockGroup must match the first half of how we call the block, and controller matches the second half
        $this->_blockGroup = 'wsnyc_questionsanswers';
        $this->_controller = 'adminhtml_questionsanswersquestion';
        $this->_headerText = $this->__('Questions');

        parent::__construct();
    }
}