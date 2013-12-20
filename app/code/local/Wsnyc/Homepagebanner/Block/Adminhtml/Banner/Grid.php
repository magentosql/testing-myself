<?php

class Wsnyc_Homepagebanner_Block_Adminhtml_Banner_Grid
	extends Mage_Adminhtml_Block_Widget_Grid
{
    public function __construct() {
        parent::__construct();
        $this->setId('wsnyc_homepagebannerGrid');
        $this->setDefaultSort('wsnyc_homepagebanner_id');
        $this->setDefaultDir('ASC');
        $this->setUseAjax(true);
        $this->setSaveParametersInSession(true);
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('wsnyc_homepagebanner/banner')->getCollection();

        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {
        $this->addColumn('banner_id', array(
            'header' => Mage::helper('wsnyc_homepagebanner')->__('ID'),
            'align' => 'right',
            'width' => '50px',
            'index' => 'banner_id',
        ));

        $this->addColumn('banner_title', array(
            'header' => Mage::helper('wsnyc_homepagebanner')->__('Title'),
            'align' => 'left',
            'index' => 'title',
        ));

        $this->addColumn('status', array(
            'header' => Mage::helper('wsnyc_homepagebanner')->__('Status'),
            'align' => 'left',
            'width' => '80px',
            'index' => 'status',
            'type' => 'options',
            'options' => array(
                1 => 'Enabled',
                2 => 'Disabled'
            ),
        ));

        $this->addColumn('action', array(
            'header' => Mage::helper('wsnyc_homepagebanner')->__('Action'),
            'width' => '100px',
            'type' => 'action',
            'getter' => 'getId',
            'actions' => array(
                array(
                    'caption' => Mage::helper('wsnyc_homepagebanner')->__('Edit'),
                    'url' => array('base' => '*/*/edit'),
                    'field' => 'id'
                )
            ),
            'filter' => false,
            'sortable' => false,
            'index' => 'stores',
            'is_system' => true,
        ));

        return parent::_prepareColumns();
    }

    protected function _prepareMassaction() {
        $this->setMassactionIdField('banner_id');
        $this->getMassactionBlock()->setFormFieldName('wsnyc_homepagebanner');

        $this->getMassactionBlock()->addItem('delete', array(
            'label' => Mage::helper('wsnyc_homepagebanner')->__('Delete'),
            'url' => $this->getUrl('*/*/massDelete'),
            'confirm' => Mage::helper('wsnyc_homepagebanner')->__('Are you sure?')
        ));

        $statuses = Mage::getSingleton('wsnyc_homepagebanner/status')->getOptionArray();

        array_unshift($statuses, array('label' => '', 'value' => ''));
        $this->getMassactionBlock()->addItem('status', array(
            'label' => Mage::helper('wsnyc_homepagebanner')->__('Change status'),
            'url' => $this->getUrl('*/*/massStatus', array('_current' => true)),
            'additional' => array(
                'visibility' => array(
                    'name' => 'status',
                    'type' => 'select',
                    'class' => 'required-entry',
                    'label' => Mage::helper('wsnyc_homepagebanner')->__('Status'),
                    'values' => $statuses
                )
            )
        ));
        return $this;
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/edit', array('id' => $row->getId()));
    }
	
}