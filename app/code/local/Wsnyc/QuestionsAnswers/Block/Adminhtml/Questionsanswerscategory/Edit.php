<?php
class Wsnyc_QuestionsAnswers_Block_Adminhtml_Questionsanswerscategory_Edit
    extends Mage_Adminhtml_Block_Widget_Form_Container
{
    /**
     * Init class
     */
    public function __construct()
    {
        $this->_blockGroup = 'wsnyc_questionsanswers';
        $this->_controller = 'adminhtml_questionsanswerscategory';

        parent::__construct();

        $this->_updateButton('save', 'label', $this->__('Save Category'));
        $this->_updateButton('delete', 'label', $this->__('Delete Category'));
    }

    /**
     * Get Header text
     *
     * @return string
     */
    public function getHeaderText()
    {
        if (Mage::registry('wsnyc_questionsanswers')->getCategoryId()) {
            return $this->__('Edit Category');
        }
        else {
            return $this->__('New Category');
        }
    }
}