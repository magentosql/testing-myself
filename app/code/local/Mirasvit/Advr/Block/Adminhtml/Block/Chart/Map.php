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


class Mirasvit_Advr_Block_Adminhtml_Block_Chart_Map
    extends Mirasvit_Advr_Block_Adminhtml_Block_Chart_Abstract
{
    public function _prepareLayout()
    {
        $this->setTemplate('mst_advr/block/chart/map.phtml');
        
        return parent::_prepareLayout();
    }

    public function getSeries()
    {
        $series = array(
            array('Country', 'Grand Total')
        );

        foreach ($this->getCollection() as $itm) {
            foreach ($this->getColumns() as $index => $column) {
                $code = $this->getCodeField();
                $value = $this->getValueField();

                if ($itm->getData($code) && $itm->getData($value) > 0) {
                    $row = array(
                        $itm->getData($code),
                        floatval($itm->getData($value))
                    );
                    $series[] = $row;
                }
            }
        }

        return $series;
    }
}