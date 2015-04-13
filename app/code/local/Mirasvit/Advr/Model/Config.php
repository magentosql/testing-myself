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


class Mirasvit_Advr_Model_Config
{
    public function dateFormat()
    {
        return Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_MEDIUM);
    }

    public function calendarDateFormat()
    {
        return Varien_Date::convertZendToStrFtime($this->dateFormat());
    }

    public function isLinkUnderReport()
    {
        return Mage::getStoreConfig('advr/view/link_under_report');
    }

    public function isReplaceDashboardLink()
    {
        return Mage::getStoreConfig('advr/view/replace_dashboard_link');
    }

    public function getProcessOrderStatuses()
    {
        $statuses = explode(',', Mage::getStoreConfig('advr/report/process_orders'));
        $statuses = array_filter($statuses);

        if (!count($statuses)) {
            $statuses[] = 'complete';
        }

        return $statuses;
    }
}