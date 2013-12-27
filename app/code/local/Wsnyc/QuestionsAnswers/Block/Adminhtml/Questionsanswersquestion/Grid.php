<?php
class Wsnyc_QuestionsAnswers_Block_Adminhtml_Questionsanswersquestion_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('category_id');
        $this->setId('wsnyc_questionsanswers_adminhtml_questionsanswersquestion_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'wsnyc_questionsanswers/question_collection';
    }

    protected function _prepareCollection()
    {
        // Get and set our collection for the grid
        $collection = Mage::getResourceModel($this->_getCollectionClass());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns()
    {
        // Add the columns that should appear in the grid
        $this->addColumn('question_id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'question_id'
            )
        );

        $this->addColumn('asked_email',
            array(
                'header'=> $this->__('Email'),
                'index' => 'asked_email'
            )
        );

        $this->addColumn('asked_name',
            array(
                'header'=> $this->__('Name'),
                'index' => 'asked_name'
            )
        );

        $this->addColumn('question_text',
            array(
                'header'=> $this->__('Question'),
                'index' => 'question_text'
            )
        );

        $this->addColumn('published',
            array(
                'header'=> $this->__('Published'),
                'index' => 'published'
            )
        );

        $this->addColumn('from_backend',
            array(
                'header'=> $this->__('Added by admin'),
                'index' => 'from_backend'
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('question_id' => $row->getQuestionId()));
    }
}