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


class Mirasvit_Advd_Block_Adminhtml_Notification_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();

        $this->_objectId   = 'notification_id';
        $this->_blockGroup = 'advd';
        $this->_controller = 'adminhtml_notification';

        $this->_removeButton('delete')
            ->_removeButton('back');
    }

    public function getHeaderText()
    {
        return Mage::helper('advr')->__("Email Notifications for user '%s'", $this->htmlEscape(Mage::registry('current_model')->getTitle()));
    }
}