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


class Mirasvit_Advr_Block_Adminhtml_Block_Container extends Mage_Adminhtml_Block_Template
{
    protected $_toolbar       = null;
    protected $_grid          = null;
    protected $_chart         = null;
    protected $_storeSwitcher = null;

    public function _prepareLayout()
    {
        $this->_initStoreSwitcher()
            ->_initToolbar()
            ->_initGrid()
            ->_initChart();
 
        $this->setTemplate('mst_advr/block/container.phtml');

        return parent::_prepareLayout();
    }

    public function getGrid()
    {
        return $this->_grid;
    }

    public function getToolbar()
    {
        return $this->_toolbar;
    }

    public function getChart()
    {
        return $this->_chart;
    }

    public function getStoreSwitcher()
    {
        return $this->_storeSwitcher;
    }

    protected function _initStoreSwitcher()
    {
        $this->_storeSwitcher = Mage::app()->getLayout()->createBlock('adminhtml/store_switcher')
            ->setTemplate('mst_advr/block/store_switcher.phtml')
            ->setStoreVarName('store_ids');

        return $this;
    }

    protected function _initToolbar()
    {
        $this->_toolbar = Mage::app()->getLayout()->createBlock('advr/adminhtml_block_toolbar');

        $this->_toolbar
            ->setFilterData($this->getFilterData())
            ->setVisibility(true)
            ->setRangesVisibility(true)
            ->setCompareVisibility(true)
            ->setIntervalsVisibility(true);

        return $this;
    }

    protected function _initGrid()
    {
        $this->_grid = Mage::app()->getLayout()->createBlock('advr/adminhtml_block_grid', get_class($this));
        
        $this->_grid->setContainer($this)
            ->setFilterData($this->getFilterData())
            ->afterCollectionLoad(array($this, 'afterGridCollectionLoad'));

        $this->_grid->setCollection($this->getCollection());

        foreach ($this->getColumns() as $index => $column) {
            $column['header'] = Mage::helper('advr')->__($column['header']);

            if (!isset($column['type'])) {
                $column['type'] = 'text';
            }

            if ($column['type'] == 'currency') {
                $column['currency_code'] = $this->_grid->getCurrentCurrencyCode();
                $column['rate']          = $this->_grid->getRate();
            } elseif ($column['type'] == 'percent') {
                $column['column_css_class'] = 'nobr percent';
            }

            if (!isset($column['index'])) {
                $column['index'] = $index;
            }

            if (!isset($column['chart'])) {
                $column['chart'] = false;
            }

            $gridColumn = $this->_grid->addColumn($index, $column);

            if (isset($column['grouped'])) {
                $this->_grid->isColumnGrouped($index, 1);
            }
        }

        $totals = $this->getTotals();
        if ($totals) {
            $this->_grid->setTotals($totals);
            $this->_grid->setCountTotals(1);
        }

        return $this;
    }

    public function afterGridCollectionLoad()
    {
        if ($this->getCompareFilterData()) {
            $collection = $this->getGrid()->getCollection();
            $compare = $this->getCollection($this->getCompareFilterData());
            $collection->merge($compare);
        }

        $totals = $this->getTotals();

        if ($totals && $totals != $this->_grid->getTotals()) {
            $this->_grid->setFilterTotals($totals);
            $this->_grid->setFilterCountTotals(1);
        }

        return $this;
    }

    protected function _initChart()
    {
        switch ($this->getChartType()) {
            case 'map':
                $blockType = 'advr/adminhtml_block_chart_map';
                break;

            case 'pie':
                $blockType = 'advr/adminhtml_block_chart_pie';
                break;

            case 'column':
            default:
                $blockType = 'advr/adminhtml_block_chart_column';
                break;
        }

        $this->_chart = Mage::app()->getLayout()->createBlock($blockType);

        $this->_chart
            ->setCollection($this->getCollection($this->getFilterData()))
            ->setColumns($this->_grid->getColumns());

        return $this;
    }

    public function getGridHtml()
    {
        if ($this->_grid) {
            return $this->_grid->toHtml();
        }
    }

    public function getToolbarHtml()
    {
        if ($this->_toolbar) {
            return $this->_toolbar->toHtml();
        }
    }

    public function getStoreSwitcherHtml()
    {
        if ($this->_storeSwitcher) {
            return $this->_storeSwitcher->toHtml();
        }
    }

    public function getChartHtml()
    {
        if ($this->_chart) {
            return $this->_chart->toHtml();
        }
    }

    public function getCollection($filterData = null)
    {
        if (!$filterData) {
            $filterData = $this->getFilterData();
        }

        $hash = md5(serialize($filterData->getData()));

        if (!$this->hasData($hash)) {
            $collection = $this->_prepareCollection($filterData);
            $this->setData($hash, $collection);
        }

        return $this->getData($hash);
    }

    public function getFilterData()
    {
        if (!$this->hasData('filter_data')) {
            $data = Mage::helper('adminhtml')->prepareFilterString($this->getRequest()->getParam('filter'));
            $data = $this->_filterDates($data, array('from', 'to', 'compare_from', 'compare_to'));

            $currentMonth = Mage::helper('advr/date')->getInterval(Mirasvit_Advr_Helper_Date::THIS_MONTH);

            if (!isset($data['from'])) {
                $data['from'] = $currentMonth->getFrom()->get(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }

            if (!isset($data['to'])) {
                $data['to'] = $currentMonth->getTo()->get(Varien_Date::DATETIME_INTERNAL_FORMAT);
            }

            if (strpos($data['from'], ':') === false) {
                $data['from'] .= ' 00:00:00';
            }
            if (isset($data['compare_from']) && strpos($data['compare_from'], ':') === false) {
                $data['compare_from'] .= ' 00:00:00';
            }

            if (strpos($data['to'], ':') === false) {
                $data['to'] .= ' 23:59:59';
            }
            if (isset($data['compare_to']) && strpos($data['compare_to'], ':') === false) {
                $data['compare_to'] .= ' 23:59:59';
            }

            if (!isset($data['range'])) {
                $data['range'] = '1d';
            }

            if (!isset($data['group_by'])) {
                $data['group_by'] = 'status';
            }

            $fromLocal = new Zend_Date(strtotime($data['from']) - Mage::getSingleton('core/date')->getGmtOffset());
            $data['from_local'] = $fromLocal->get(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $toLocal = new Zend_Date(strtotime($data['to']) - Mage::getSingleton('core/date')->getGmtOffset());
            $data['to_local'] = $toLocal->get(Varien_Date::DATETIME_INTERNAL_FORMAT);

            $data['store_ids'] = array_filter(explode(',', $this->getRequest()->getParam('store_ids')));

            $data = array_filter($data);

            $this->setData('filter_data', new Varien_Object($data));
        }

        return $this->getData('filter_data');
    }

    public function getCompareFilterData()
    {
        if (!$this->getFilterData()->getCompare()) {
            return false;
        }

        $params = $this->getFilterData();
        $params->setFrom($this->getFilterData()->getCompareFrom());
        $params->setTo($this->getFilterData()->getCompareTo());

        return $params;
    }

    protected function _filterDates($array, $dateFields)
    {
        if (empty($dateFields)) {
            return $array;
        }

        $filterInput = new Zend_Filter_LocalizedToNormalized(array(
            'locale'      => Mage::app()->getLocale()->getLocaleCode(),
            'date_format' => Mage::getSingleton('advr/config')->dateFormat()
        ));

        $filterInternal = new Zend_Filter_NormalizedToLocalized(array(
            'locale'      => Mage::app()->getLocale()->getLocaleCode(),
            'date_format' => Varien_Date::DATE_INTERNAL_FORMAT
        ));

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($dateField)) {
                $array[$dateField] = $filterInput->filter($array[$dateField]);
                $array[$dateField] = $filterInternal->filter($array[$dateField]);
            }
        }

        return $array;
    }
}