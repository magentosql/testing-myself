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


class Mirasvit_Advd_Adminhtml_DashboardController extends Mage_Adminhtml_Controller_Action
{
    protected function _validateFormKey()
    {
        return true;
    }

    protected function _initAction()
    {
        $this->loadLayout()->_setActiveMenu('dashboard');

        return $this;
    }

    public function testAction()
    {
        echo '<pre>';

        $intervals = Mage::helper('advr/date')->getIntervals(true, true, true);
        foreach ($intervals as $code => $label) {
            echo $label.'<br>';

            $interval = Mage::helper('advr/date')->getInterval($code);
            echo '    '.$interval->getFrom()->get(Zend_Date::DATETIME_MEDIUM).' - '.$interval->getTo()->get(Zend_Date::DATETIME_MEDIUM).'<br>';

            $interval = Mage::helper('advr/date')->getPreviousInterval($code);
            echo '    1 '.$interval->getFrom()->get(Zend_Date::DATETIME_MEDIUM).' - '.$interval->getTo()->get(Zend_Date::DATETIME_MEDIUM).'<br>';

            $interval = Mage::helper('advr/date')->getPreviousInterval($code, 365);
            echo '    365 '.$interval->getFrom()->get(Zend_Date::DATETIME_MEDIUM).' - '.$interval->getTo()->get(Zend_Date::DATETIME_MEDIUM).'<br>';

            echo '<br>';
        }
    }

    public function globalAction()
    {
        $this->_initAction();
        $this->_title($this->__('Advanced Dashboard'));

        $this->_initDashboard();

        $this->_addContent($this->getLayout()->createBlock('advd/adminhtml_dashboard', 'dashboard'));

        $this->renderLayout();
    }

    public function userAction()
    {
        $this->_title($this->__('User Dashboard'));

        $this->getRequest()
            ->setParam('dashboard', Mage::getSingleton('admin/session')->getUser()->getId());
            
        $this->_initAction();
        $this->_initDashboard();

        $this->_addContent($this->getLayout()->createBlock('advd/adminhtml_dashboard', 'dashboard'));

        $this->renderLayout();
    }

    public function actionAction()
    {
        $ts = microtime(true);

        $result = array();
        $cmd = $this->getRequest()->getParam('cmd');
        $dashboard = $this->_initDashboard();

        try {
            switch ($cmd) {
                case 'list':
                    $result['dashboard'] = $dashboard->getDashboard();
                    break;

                case 'load':
                    $id = $this->getRequest()->getParam('id');
                    $result = $dashboard->loadWidget($id);
                    break;

                case 'save':
                    $grid = $this->getRequest()->getParam('grid');

                    $dashboardGrid = array();
                    foreach ($grid as $widget) {
                        $dashboardGrid[$widget['id']] = $widget;
                    }
                    $dashboard->setDashboard($dashboardGrid)
                        ->save();
                    break;

                case 'settings':
                    $id = $this->getRequest()->getParam('id');
                    $widget = $this->getRequest()->getParam('widget');
                    if ($this->getRequest()->getParam('settings')) {
                        $params = $this->getRequest()->getParam('settings');
                        $dashboard->updateWidget($id, $params);
                    }
                    
                    $result = array('content' => $dashboard->loadWidgetSettings($id, $widget));

                    break;
            }
        } catch(Exception $e) {
            $result['error'] = $e->getMessage();
        }

        $result['time'] = round(microtime(true) - $ts, 4);

        echo Mage::helper('core')->jsonEncode($result);
        exit;
    }

    protected function _initDashboard()
    {
        $id = (int) $this->getRequest()->getParam('dashboard');

        $dashboard = Mage::getModel('advd/dashboard')
            ->load($id);

        Mage::register('current_dashboard', $dashboard);

        return $dashboard;
    }
}