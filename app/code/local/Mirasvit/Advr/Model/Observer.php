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


class Mirasvit_Advr_Model_Observer extends Varien_Object
{
    public function onControllerActionPredispatch($observer)
    {
        try {

            $menu = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('menu');
            $acl  = Mage::getSingleton('admin/config')->getAdminhtmlConfig()->getNode('acl');

            if (Mage::getSingleton('advr/config')->isLinkUnderReport()) {
                $itemAdvr = null;
                $itemReport = null;
                foreach ($menu->children() as $key => $children) {
                    if ($key == 'advr') {
                        $itemAdvr = $children;
                    }

                    if ($key == 'report') {
                        $itemReport = $children;
                    }
                }

                if ($itemAdvr && $itemReport) {
                    $itemReport->children()->appendChild($itemAdvr);
                }

                $menu->setNode('advr', null);


                $itemAdvr = null;
                $itemReport = null;

                foreach ($acl->resources->admin->children->children() as $key => $children) {
                    if ($key == 'advr') {
                        $itemAdvr = $children;
                    }

                    if ($key == 'report') {
                        $itemReport = $children;
                    }
                }
                if ($itemAdvr && $itemReport) {
                    $itemReport->children()->appendChild($itemAdvr);
                }
                $acl->resources->admin->children->setNode('advr', null);
            }
        } catch (Exception $e) {}
    }
}