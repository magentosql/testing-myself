<?php

class Wsnyc_SeoSubfooter_Block_Adminhtml_Blurb_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('blurbsGrid');
        // This is the primary key of the database
        $this->setDefaultSort('blurb_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
        $this->setUseAjax(true);
    }

    protected function _prepareCollection() {
        $collection = Mage::getModel('seosubfooter/blurb')->getCollection();
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('blurb_id');
        $this->getMassactionBlock()->setFormFieldName('blurb_id');

        //add delete mass action option
        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('tax')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete', array('' => '')),
            'confirm' => Mage::helper('tax')->__('Are you sure?')
        ));

        //add status update mass action
        $statuses = Mage::getSingleton('seosubfooter/source_status')->toOptionHash();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('seosubfooter')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('seosubfooter')->__('Status'),
                    'values' => $statuses
                )
            )
        ));

        Mage::dispatchEvent('zefir_dealers_grid_prepare_massaction', array('grid' => $this));
        return $this;
    }

    protected function _prepareColumns() {

        $this->addColumn('blurb_id', array(
            'header' => Mage::helper('seosubfooter')->__('Blurb ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'blurb_id',
        ));

        $this->addColumn('title', array(
            'header' => Mage::helper('seosubfooter')->__('Title'),
            'align' => 'left',
            'width' => '350px',
            'index' => 'title',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('seosubfooter')->__('Status'),
            'align' => 'left',
            'width' => '100px',
            'index' => 'status',
            'type' => 'options',
            'options' => Mage::getSingleton('seosubfooter/source_status')->toOptionHash()
        ));

        $this->addColumn('action', array(
            'header' => $this->helper('seosubfooter')->__('Action'),
            'width' => 15,
            'sortable' => false,
            'filter' => false,
            'getter' => 'getBlurbId',
            'type' => 'action',
            'actions' => array(
                array(
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id',
                    'caption' => $this->helper('seosubfooter')->__('Edit'),
                ),
            )
        ));

        Mage::dispatchEvent('seosubfooter_grid_prepare_columns', array('grid' => $this));
        return parent::_prepareColumns();
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }

    public function getGridUrl() {
        return $this->getUrl('*/*/grid', array('_current' => true));
    }

}
