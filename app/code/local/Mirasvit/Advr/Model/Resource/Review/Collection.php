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


class Mirasvit_Advr_Model_Resource_Review_Collection extends Mirasvit_Advr_Model_Resource_Collection_Abstract
{
    protected function _construct()
    {
        $this->_init('review/review');

        $this->setMainTable($this->getTable('review/review'));
    }

    protected function _initSelect()
    {
        parent::_initSelect();

        $this->getSelect()->reset(Zend_Db_Select::COLUMNS);

        $this->getSelect()->columns(array('quantity' => 'COUNT(main_table.entity_id)'));

        $this->getSelect()
            ->joinLeft(
                array('review_detail' => $this->getTable('review/review_detail')),
                'main_table.entity_id = review_detail.review_id',
                array()
            )
            ->where('main_table.status_id = ?', Mage_Review_Model_Review::STATUS_APPROVED);
            ;

        return $this;
    }

    public function groupByPeriod()
    {
        $expression = $this->_getRangeExpressionForAttribute(
            $this->getFilterData()->getRange(),
            $this->getTZDate('main_table.created_at')
        );

        $this->getSelect()
            ->group($expression)
            ->columns(array('period' => $expression))
            ;

        return $this;
    }

    public function joinProduct($fields = array())
    {
        foreach ($fields as $field) {
            $this->getSelect()->columns(array($field => $field), 'main_table');
        }

        return $this;
    }

    public function setFilterData($data)
    {
        $this->_filterData = $data;

        if ($this->_filterData->getFrom()) {
            $this->getSelect()
                ->where($this->getTZDate('main_table.created_at')." >= '".$this->_filterData->getFrom()."'");
        }

        if ($this->_filterData->getTo()) {
            $this->getSelect()
                ->where($this->getTZDate('main_table.created_at')." < '".$this->_filterData->getTo()."'");
        }

        if (count($this->_filterData->getStoreIds())) {
            $this->getSelect()
                ->where('review_detail.store_id IN('.implode(',', $this->_filterData->getStoreIds()).')');
        }

        return $this;
    }

    public function getSelectCountSql()
    {
        $countSelect = clone $this->getSelect();
        $countSelect->reset(Zend_Db_Select::ORDER);
        $countSelect->reset(Zend_Db_Select::LIMIT_COUNT);
        $countSelect->reset(Zend_Db_Select::LIMIT_OFFSET);
        $countSelect->reset(Zend_Db_Select::COLUMNS);        
        $countSelect->columns();
        
        $select = 'SELECT COUNT(*) FROM ('.$countSelect->__toString().') as cnt';
        
        return $select;
    }
}