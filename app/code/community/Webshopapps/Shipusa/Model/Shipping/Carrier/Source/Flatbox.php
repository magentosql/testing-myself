<?php

/*
 * @category   Webshopapps
 * @package    Webshopapps_UsaShipping
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt
 * @author     Karen Baker <sales@webshopapps.com>
*/
class Webshopapps_Shipusa_Model_Shipping_Carrier_Source_Flatbox
{
    public function toOptionArray()
    {
        $arr = $this->getCode('usps_box');
        unset($arr[4]);

        $collection = Mage::getModel('boxmenu/boxmenu')->getCollection();
        $collection->load();

        foreach ($collection->getItems() as $item) {

            $boxType = $item->getBoxType();

            if($boxType < 4 && !empty($boxType)) { //if empty it's a custom box and the update SQL hasn't run
                $arr[$item->getBoxmenuId()] = $item->getTitle();
            }
        }

        //Sort by key. Predefined options at top, customer defined at bottom
        ksort($arr);

        return $arr;
    }

    public function getCode($type, $code='')
    {
        $codes = array(

            'usps_box'=>array(
                '4' => Mage::helper('shipping')->__('Custom Box'),
                '1' => Mage::helper('shipping')->__('SM FLAT RATE BOX'),
                '2' => Mage::helper('shipping')->__('MD FLAT RATE BOX'),
                '3' => Mage::helper('shipping')->__('LG FLAT RATE BOX'),
            ),
        );

        if (!isset($codes[$type])) {
            Mage::helper('wsacommon/log')->postCritical('usashipping','USPS Invalid Flat Box Code',$code);
        }

        if (''===$code) {
            return $codes[$type];
        }

        if (!isset($codes[$type][$code])) {
            Mage::helper('wsacommon/log')->postCritical('usashipping','USPS Invalid Flat Box Code',$code);
        }

        return $codes[$type][$code];
    }
}
