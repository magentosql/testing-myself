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


class Mirasvit_Advr_Block_Adminhtml_Block_Grid_Column extends Mage_Adminhtml_Block_Widget_Grid_Column
{
    public function getRowFieldExport(Varien_Object $row)
    {
        $renderedValue = $row->getData($this->getIndex());

        # if need format column value
        $exportCallback = $this->getExportCallback();
        if (is_array($exportCallback)) {
            $renderedValue = call_user_func($exportCallback, $renderedValue, $row, $this, false);
        }

        $renderedValue = strip_tags($renderedValue);

        return $renderedValue;
    }
}