<?php
class Wsnyc_QuestionsAnswers_Block_Adminhtml_Questionsanswersquestion_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_objectId = 'question_id';
        $this->_blockGroup = 'wsnyc_questionsanswers';
        $this->_controller = 'adminhtml_questionsanswersquestion';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Question'));
        $this->_updateButton('delete', 'label', $this->__('Delete Question'));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('wsnyc_questionsanswers')->getQuestionId()) {
            return $this->__('Edit Question');
        }
        else {
            return $this->__('New Question');
        }
    }
}