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


class Mirasvit_Advd_Model_Notification extends Mage_Core_Model_Abstract
{
    protected function _construct()
    {
        $this->_init('advd/notification');
    }

    public function getTitle()
    {
        return $this->getCurrentUser()->getUsername();
    }

    public function loadForCurrentUser()
    {
        return $this->getCollection()
            ->addFieldToFilter('user_id', $this->getCurrentUser()->getId())
            ->getFirstItem();
    }

    public function getCurrentUser()
    {
        return Mage::getSingleton('admin/session')->getUser();
    }

    public function getAllWidgets()
    {
        $result = array();
        $globalDashboard = Mage::getModel('advd/dashboard')->load(0);
        foreach ($globalDashboard->getDashboard() as $id => $widget) {
            $result[] = array(
                'value' => $id,
                'label' => $widget['title']
            );
        }

        $userDashboard = Mage::getModel('advd/dashboard')->load($this->getCurrentUser()->getId());
        foreach ($userDashboard->getDashboard() as $id => $widget) {
            $result[] = array(
                'value' => $id,
                'label' => $widget['title']
            );
        }

        return $result;
    }

    public function send()
    {
        Mage::getDesign()->setArea(Mage_Core_Model_App_Area::AREA_ADMINHTML);

        $globalDashboard = Mage::getModel('advd/dashboard')->load(0);
        $userDashboard = Mage::getModel('advd/dashboard')->load($this->getUserId());

        $content = '';
        foreach ($this->getEmailWidgets() as $widgetId) {
            $widget = false;
            
            if ($globalDashboard->isWidgetExists($widgetId)) {
                $widget = $globalDashboard->loadWidget($widgetId);
            } elseif ($userDashboard->isWidgetExists($widgetId)) {
                $widget = $userDashboard->loadWidget($widgetId);
            }

            if ($widget) {
                $content .= sprintf(
                    "<div class='block'><h3>%s</h3>%s</div>",
                    $widget['title'], $widget['content']
                );
            }
        }

        $translate = Mage::getSingleton('core/translate');
        $translate->setTranslateInline(false);
        
        $email = Mage::getModel('core/email_template');
        $email->setTemplateFilter(Mage::helper('advd/email_filter'));

        $recipientEmails = explode(',', $this->getRecipientEmail());

        foreach ($recipientEmails as $recipientEmail) {
            $email->sendTransactional(
                'advd_notification_template',
                'support',
                $recipientEmail,
                $recipientEmail,
                array(
                    'email'   => $this,
                    'subject' => $this->getEmailSubject().' ['.Mage::getModel('core/date')->date('M d, Y H:i').']',
                    'content' => $content,
                )
            );
        }

        $translate->setTranslateInline(true);

        $this->setSentAt(Mage::getSingleton('core/date')->gmtDate())
            ->save();

        return $this;
    }

    public function canSend($timestamp = null)
    {
        $offset = Mage::getSingleton('core/date')->timestamp() - Mage::getSingleton('core/date')->gmtTimestamp();
        
        $now  = new Zend_Date($timestamp, null, Mage::app()->getLocale()->getLocaleCode()); # gmt
        $last = new Zend_Date(strtotime($this->getSentAt()), null, Mage::app()->getLocale()->getLocaleCode()); # gmt

        if (in_array($now->get(Zend_Date::WEEKDAY_DIGIT), $this->getScheduleDay())) {
            foreach ($this->getScheduleTime() as $minutes) {
                $scheduled = clone $now; # gmt
                $scheduled->setTime('00:00:00')
                    ->addMinute($minutes)
                    ->subSecond($offset);

                $diff1 = $now->getTimestamp() - $scheduled->getTimestamp();
                $diff2 = $now->getTimestamp() - $last->getTimestamp();

                if ($diff1 > 0 && $diff1 < 25 * 60
                    && $diff2 > 25 * 60) {
                    
                    return true;
                }
            }
        }

        return false;
    }
}