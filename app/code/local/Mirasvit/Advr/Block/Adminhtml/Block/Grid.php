<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.0
 * @build     370
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advr_Block_Adminhtml_Block_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
    protected $_currentCurrencyCode = null;

    protected $_afterCollectionLoadCallback = null;

    public function _prepareLayout()
    {
        $this->setTemplate('mst_advr/block/grid.phtml');
        $this->setId($this->getNameInLayout());

        $this->setChild('save_config_button',
            $this->getLayout()->createBlock('adminhtml/widget_button')
                ->setData(array(
                    'label'     => Mage::helper('adminhtml')->__('Save configuration'),
                    'onclick'   => 'grid_configuration.submit();',
                    'class'     => 'save'
                ))
        );

        return parent::_prepareLayout();
    }

    public function afterCollectionLoad($callback)
    {
        $this->_afterCollectionLoadCallback = $callback;

        return $this;
    }

    public function setPagerVisibility($visible=true)
    {
        parent::setPagerVisibility($visible);

        return $this;
    }

    protected function _prepareCollection()
    {
        parent::_prepareCollection();

        if ($this->_afterCollectionLoadCallback) {
            call_user_func($this->_afterCollectionLoadCallback);
        }

        return $this;
    }

    public function addColumn($columnId, $column)
    {
        if (is_array($column)) {
            $this->_columns[$columnId] = $this->getLayout()->createBlock('advr/adminhtml_block_grid_column')
                ->setData($column)
                ->setGrid($this);
        } else {
            throw new Exception(Mage::helper('adminhtml')->__('Wrong column format.'));
        }

        $this->_columns[$columnId]->setId($columnId);
        $this->_lastColumnId = $columnId;
        return $this;
    }

    public function getAllColumns()
    {
        $configuration = $this->loadConfiguration();
        
        $columns = parent::getColumns();

        if (is_array($configuration->getColumns())) {
            foreach ($configuration->getColumns() as $index => $options) {
                if (isset($columns[$index])) {
                    $columns[$index]->addData($options);
                }
            }
        }

        $position = 10;
        $positions = array();
        foreach ($columns as $index => $column) {
            if (!$columns[$index]->getPosition()) {
                $columns[$index]->setPosition($position);
            }
            
            $positions[$index] = $column->getPosition();
            $position += 10;
        }
        array_multisort($positions, SORT_ASC, $columns);
        
        return $columns;  
    }

    public function getColumns()
    {
        $columns = $this->getAllColumns();

        foreach ($columns as $index => $column) {
            if ($column->getHidden()) {
                unset($columns[$index]);
            }
        }

        return $columns;
    }

    public function getCurrentCurrencyCode()
    {
        if (is_null($this->_currentCurrencyCode)) {
            $this->_currentCurrencyCode = (count($this->_storeIds) > 0)
                ? Mage::app()->getStore(array_shift($this->_storeIds))->getBaseCurrencyCode()
                : Mage::app()->getStore()->getBaseCurrencyCode();
        }
        return $this->_currentCurrencyCode;
    }

    public function getRate($toCurrency = null)
    {
        if ($toCurrency == null) {
            $toCurrency = $this->getCurrentCurrencyCode();
        }

        return Mage::app()->getStore()->getBaseCurrency()->getRate($toCurrency);
    }

    public function getRowUrl($item)
    {
        if ($this->getRowUrlCallback()) {
            return call_user_func_array($this->getRowUrlCallback(), array($item));
        }

        return false;
    }

    public function getJsObjectName()
    {
        return 'advnGridJsObject';
    }

    public function addExportType($url, $label)
    {
        $this->_exportTypes[] = new Varien_Object(
            array(
                'url'   => $this->getUrl('*/*/*', array('_current' => true, '_query' => array('export' => true, 'type' => $url))),
                'label' => $label
            )
        );
        return $this;
    }

    public function saveConfiguration($configuration)
    {
        Mage::helper('advr')->setVariable($this->getId(), $configuration);

        return $this;
    }

    public function loadConfiguration()
    {
        $configuration = Mage::helper('advr')->getVariable($this->getId());

        if ($configuration instanceof Varien_Object) {
            return $configuration;
        }

        return new Varien_Object();
    }
}