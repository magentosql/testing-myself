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


class Mirasvit_Advr_Block_Adminhtml_Block_Chart_Column
    extends Mirasvit_Advr_Block_Adminhtml_Block_Chart_Abstract
{
    public function _prepareLayout()
    {
        $this->setTemplate('mst_advr/block/chart/column.phtml');
        
        return parent::_prepareLayout();
    }

    public function getXAxis()
    {
        $axis = array();
        $columns = $this->getColumns();
        if (isset($columns[$this->getXAxisField()])) {
            $column = $columns[$this->getXAxisField()];

            foreach ($this->getCollection() as $row) {
                $axis[] = $column->getRowField($row);
            }
        }

        return $axis;
    }

    public function getDataTable()
    {
        $array = array();

        $columns = $this->getColumns();

        $row = array();

        foreach ($columns as $index => $column) {
            if (!in_array($column->getType(), array('number', 'currency'))) {
                continue;
            }

            if ($column->getChart() === 'none') {
                continue;
            }

            $row[] = $column->getHeader();

        }
        
        $row[] = $this->getXAxisType();

        $array[] = array_reverse($row);

        foreach ($this->getCollection() as $itm) {
            $row = array();


            foreach ($columns as $index => $column) {
                if (!in_array($column->getType(), array('number', 'currency'))) {
                    continue;
                }

                if ($column->getChart() === 'none') {
                    continue;
                }

                $value  = floatval($itm->getData($index));

                $row[] = $value;
            }

            $xColumn = $columns[$this->getXAxisField()];
            $row[] = str_replace('<br>', "\n", $xColumn->getRowField($itm));

            $array[] = array_reverse($row);
        }

        return $array;
    }

    public function getDefaultSeries()
    {
        $series = array();

        $idx = 1;
        $total = 0;

        foreach ($this->getColumns() as $index => $column) {
            if (!in_array($column->getType(), array('number', 'currency'))) {
                continue;
            }

            if ($column->getChart() === 'none') {
                continue;
            }

            if ($column->getChart()) {
                $series[] = $idx;
            }

            $idx++;
        }

        # revert
        foreach ($series as $k => $v) {
            $series[$k] = $idx - $v;
        }

        return $series;
    }

}