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
 * @build     345
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_Advr_Model_Resource_Collection_Abstract extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    protected $_filterData = null;

    public function addSumColumn($table, $column, $as = null)
    {
        if ($as == null) {
            $as = "sum_$column";
        }
        
        $this->getSelect()->columns(array($as => "IFNULL(SUM($table.$column), 0)"));

        return $this;
    }

    public function addColumn($table, $column)
    {
        $this->getSelect()->columns(array($column => "$table.$column"));

        return $this;
    }

    public function addAvgColumn($table, $column, $as = null)
    {
        if ($as == null) {
            $as = "avg_$column";
        }
        
        $this->getSelect()->columns(array($as => "IFNULL(AVG($table.$column), 0)"));

        return $this;
    }

    public function getFilterData()
    {
        return $this->_filterData;
    }

    public function getTotals()
    {
        $totals = array();

        $select = clone $this->getSelect();
        $rows = $this->getConnection()->fetchAll($select);
        // echo $select;
        foreach ($rows as $row) {
            foreach ($row as $k => $v) {
                if (!isset($totals[$k])) {
                    $totals[$k] = null;
                }

                $totals[$k] += $v;
                $totals[$k] = round($totals[$k], 2);
            }
        }

        return new Varien_Object($totals);
    }

    public function addFieldToFilter($field, $condition = null)
    {
        $columnExpression = $this->_columnExpression($field);

        if ($columnExpression == $field || $field == 'category') {
            if (isset($condition['eq'])) {
                $condition['like'] = '%,'.$condition['eq'].',%';
                unset($condition['eq']);
            } 
            parent::addFieldToFilter($columnExpression, $condition);
        } else {
            $this->getSelect()->having($this->_translateCondition($columnExpression, $condition));
        }

        return $this;
    }

    public function setOrder($field, $direction = 'DESC')
    {
        $columnExpression = $this->_columnExpression($field);

        $this->getSelect()->order("$columnExpression $direction");

        return $this;
    }

    protected function _columnExpression($field)
    {
        $columns = $this->getSelect()->getPart(Zend_Db_Select::COLUMNS);
        foreach ($columns as $column) {
            if ($column[2] == $field) {
                if (is_object($column[1])) {
                    $expr = $column[1]->__toString();
                } else {
                    $expr = $column[1];
                }

                if (strpos($expr, 'COUNT(') !== false
                    || strpos($expr, 'AVG(') !== false
                    || strpos($expr, 'SUM(') !== false
                    || strpos($expr, 'CONCAT(') !== false) {

                    return $expr;
                }
            }
        }

        return $field;
    }

    protected function _getRangeExpression($range)
    {
        switch ($range) {
            case '1h':
                $expr = $this->getConnection()->getConcatSql(array(
                    $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-%d %H:00:00'),
                    $this->getConnection()->quote('00')
                ));
                break;

            case '1d':
                $expr = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-%d 00:00:00');
                break;

            case '1w':
                $expr1 = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y');
                $expr2 = new Zend_Db_Expr('WEEKOFYEAR({{attribute}}) - 1');
                $expr3 = new Zend_Db_Expr("'Monday'");
                $contact = $this->getConnection()->getConcatSql(array($expr1, $expr2, $expr3), ' ');
                $expr = $this->getConnection()->getConcatSql(array("STR_TO_DATE($contact, '%X %V %W')", "'00:00:00'"), ' ');
                break;

            default:
            case '1m':
                $expr = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-%m-01 00:00:00');
                break;

            case '1q':
                $year = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y');
                $quarter = new Zend_Db_Expr('CEIL((MONTH({{attribute}}) - 1) / 4) * 3 + 1');
                $expr = $this->getConnection()->getConcatSql(array($year, $quarter, "'01 00:00:00'"), '-');

                break;

            case '1y':
                $expr = $this->getConnection()->getDateFormatSql('{{attribute}}', '%Y-01-01 00:00:00');
                break;
        }

        return $expr;
    }

    protected function _getRangeExpressionForAttribute($range, $attribute)
    {
        $expression = $this->_getRangeExpression($range);
        return str_replace('{{attribute}}', $this->getConnection()->quoteIdentifier($attribute), $expression);
    }

    public function getTZDate($column)
    {
        $offset = Mage::getSingleton('core/date')->getGmtOffset();

        $periods = $this->_getTZOffsetTransitions(
            Mage::app()->getLocale()->storeDate(null)->toString(Zend_Date::TIMEZONE_NAME),
            time() - 3 * 365 * 24 * 60 * 60,
            null
        );

        if (!count($periods)) {
            return $column;
        }

        $query = "";
        $periodsCount = count($periods);

        $i = 0;
        foreach ($periods as $offset => $timestamps) {
            $subParts = array();
            foreach ($timestamps as $ts) {
                $subParts[] = "($column between {$ts['from']} and {$ts['to']})";
            }

            $then = $this->getConnection()->getDateAddSql($column, $offset, Varien_Db_Adapter_Interface::INTERVAL_SECOND);

            $query .= (++$i == $periodsCount) ? $then : "CASE WHEN " . join(" OR ", $subParts) . " THEN $then ELSE ";
        }

        return new Zend_Db_Expr($query.str_repeat('END ', count($periods) - 1));
    }

    protected function _getTZOffsetTransitions($timezone, $from = null, $to = null)
    {
        $tzTransitions = array();
        try {
            if ($from == null) {
                $from = new Zend_Date($from, Varien_Date::DATETIME_INTERNAL_FORMAT);
                $from = $from->getTimestamp();
            }

            $to = new Zend_Date($to, Varien_Date::DATETIME_INTERNAL_FORMAT);
            $nextPeriod = $this->getConnection()->formatDate($to->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
            $to = $to->getTimestamp();

            $dtz = new DateTimeZone($timezone);
            $transitions = $dtz->getTransitions();
            $dateTimeObject = new Zend_Date('c');
            for ($i = count($transitions) - 1; $i >= 0; $i--) {
                $tr = $transitions[$i];
                if (!$this->_isValidTransition($tr, $to)) {
                    continue;
                }

                $dateTimeObject->set($tr['time']);
                $tr['time'] = $this->getConnection()
                    ->formatDate($dateTimeObject->toString(Varien_Date::DATETIME_INTERNAL_FORMAT));
                $tzTransitions[$tr['offset']][] = array('from' => $tr['time'], 'to' => $nextPeriod);

                if (!empty($from) && $tr['ts'] < $from) {
                    break;
                }
                $nextPeriod = $tr['time'];
            }
        } catch (Exception $e) {
            $this->_logException($e);
        }

        return $tzTransitions;
    }

    protected function _isValidTransition($transition, $to)
    {
        $result         = true;
        $timeStamp      = $transition['ts'];
        $transitionYear = date('Y', $timeStamp);

        if ($transitionYear > 10000 || $transitionYear < -10000) {
            $result = false;
        } else if ($timeStamp > $to) {
            $result = false;
        }

        return $result;
    }

    protected function _translateCondition($field, $condition)
    {
        $field = $this->_getMappedField($field);
        return $this->_getConditionSql($field, $condition);
    }
}