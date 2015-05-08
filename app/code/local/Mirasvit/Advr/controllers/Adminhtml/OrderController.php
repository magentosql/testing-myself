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


class Mirasvit_Advr_Adminhtml_OrderController extends Mirasvit_Advr_Controller_Report
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('advr');

        $this->_title($this->__('Advanced Reports'))
            ->_title($this->__('Sales'));

        return parent::_initAction();
    }

    public function ordersAction()
    {
        $this->_initAction();

        $this->_title($this->__('Sales'));

        $this->renderLayout();
    }

    public function plainAction()
    {
        $this->_initAction();

        $this->_title($this->__('Sales'));

        $this->renderLayout();
    }

    public function hourAction()
    {
        $this->_initAction();

        $this->_title($this->__('By Hour'));

        $this->renderLayout();
    }

    public function dayAction()
    {
        $this->_initAction();

        $this->_title($this->__('By Day of Week'));

        $this->renderLayout();
    }

    public function countryAction()
    {
        $this->_initAction();

        $this->_title($this->__('By Country'));

        $this->renderLayout();
    }

    public function stateAction()
    {
        $this->_initAction();

        $this->_title($this->__('By State'));

        $this->renderLayout();
    }

    public function cityAction()
    {
        $this->_initAction();

        $this->_title($this->__('By City'));

        $this->renderLayout();
    }

    public function zipAction()
    {
        $this->_initAction();

        $this->_title($this->__('By ZIP Code'));

        $this->renderLayout();
    }

    public function paymentTypeAction()
    {
        $this->_initAction();

        $this->_title($this->__('By Payment Type'));

        $this->renderLayout();
    }

    public function customerGroupAction()
    {
        $this->_initAction();

        $this->_title($this->__('Sales By Customer Group'));

        $this->renderLayout();
    }

    public function couponAction()
    {
        $this->_initAction();

        $this->_title($this->__('Sales By Coupon'));

        $this->renderLayout();
    }

    public function newVsReturningAction()
    {
        $this->_initAction();

        $this->_title($this->__('New vs Returning Customers'));

        $this->renderLayout();
    }

    public function customerAction()
    {
        $this->_initAction();

        $this->_title($this->__('Sales by Customer'));

        $this->renderLayout();
    }

    public function productAction()
    {
        $this->_initAction();

        $this->_title($this->__('Product Sales'));

        $this->renderLayout();
    }

    public function categoryAction()
    {
        $this->_initAction();

        $this->_title($this->__('Sales by Category'));

        $this->renderLayout();
    }

    public function shippingtimeAction()
    {
        $this->_initAction();

        $this->_title($this->__('Average Shipping Time'));

        $this->renderLayout();
    }
}