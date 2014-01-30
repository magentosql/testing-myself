<?php
class Wsnyc_QuestionsAnswers_Block_Adminhtml_Questionsanswerscategory_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct()
    {
        parent::__construct();

        // Set some defaults for our grid
        $this->setDefaultSort('category_id');
        $this->setId('wsnyc_questionsanswers_adminhtml_questionsanswerscategory_grid');
        $this->setDefaultDir('asc');
        $this->setSaveParametersInSession(true);
    }

    protected function _getCollectionClass()
    {
        // This is the model we are using for the grid
        return 'wsnyc_questionsanswers/category_collection';
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
        $this->addColumn('category_id',
            array(
                'header'=> $this->__('ID'),
                'align' =>'right',
                'width' => '50px',
                'index' => 'category_id'
            )
        );

        $this->addColumn('name',
            array(
                'header'=> $this->__('Name'),
                'index' => 'name'
            )
        );

        $this->addColumn('image',
            array(
                'header'=> $this->__('Index Image'),
                'index' => 'image'
            )
        );

        $this->addColumn('wide_image',
            array(
                'header'=> $this->__('Wide Image'),
                'index' => 'wide_image'
            )
        );

        $this->addColumn('parent_name',
            array(
                'header'=> $this->__('Parent'),
                'index' => 'parent_name'
            )
        );

        return parent::_prepareColumns();
    }

    public function getRowUrl($row)
    {
        // This is where our row data will link to
        return $this->getUrl('*/*/edit', array('category_id' => $row->getCategoryId()));
    }
}