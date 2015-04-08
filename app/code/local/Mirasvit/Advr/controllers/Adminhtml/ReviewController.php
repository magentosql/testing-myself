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


class Mirasvit_Advr_Adminhtml_ReviewController extends Mirasvit_Advr_Controller_Report
{
    protected function _initAction()
    {
        $this->loadLayout()
            ->_setActiveMenu('advr');

        $this->_title($this->__('Advanced Reports'))
            ->_title($this->__('Reviews'));

        return parent::_initAction();
    }

    public function reviewsAction()
    {
        $this->_initAction();

        $this->_title($this->__('Reviews'));

        $this->renderLayout();
    }
}