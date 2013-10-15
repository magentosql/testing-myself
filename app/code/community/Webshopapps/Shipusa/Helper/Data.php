<?php
/* Dimensional Shipping
 * @category   Webshopapps
 * @package    Webshopapps_UsaShipping
 * @copyright   Copyright (c) 2013 Zowta Ltd (http://www.WebShopApps.com)
 *              Copyright, 2013, Zowta, LLC - US license
 * @license    http://www.webshopapps.com/license/license.txt - Commercial license
 */

/****
 * Helper Methods
 **/
class Webshopapps_Shipusa_Helper_Data extends Mage_Core_Helper_Abstract
{

	protected static $_debug;
	protected static $_handlingProductInstalled;
	protected static $_handlingProdModel = NULL;
	protected static $_shipAllSeparate;
	protected static $_wholeWeightRounding;
    protected static $_isExactPackingAlgorithm;
    protected static $_isVolumePackingAlgorithm;

	public static function isDebug() {
		if (self::$_debug==NULL) {
			self::$_debug = Mage::helper('wsalogger')->isDebug('Webshopapps_Shipusa');
		}
		return self::$_debug;
	}

	public static function shipAllSeparate() {

		if (self::$_shipAllSeparate==NULL) {
			self::$_shipAllSeparate = Mage::getStoreConfig('shipping/shipusa/ship_separate');
		}
		return self::$_shipAllSeparate;

	}

	public static function isExactPackingAlgorithm() {

		if (self::$_isExactPackingAlgorithm==NULL) {
			self::$_isExactPackingAlgorithm =
                (Mage::getStoreConfig('shipping/shipusa/packing_algorithm')=='exact_packing') ? true : false;
		}
		return self::$_isExactPackingAlgorithm;

	}

    public static function isVolumePackingAlgorithm() {

        if (self::$_isVolumePackingAlgorithm==NULL) {
            self::$_isVolumePackingAlgorithm =
                (Mage::getStoreConfig('shipping/shipusa/packing_algorithm')=='volume_packing') ? true : false;
        }
        return self::$_isVolumePackingAlgorithm;

    }

	public static function isWholeWeightRounding() {
		if (self::$_wholeWeightRounding==NULL) {
			self::$_wholeWeightRounding = Mage::getStoreConfig('shipping/shipusa/whole_weight');
		}
		return self::$_wholeWeightRounding;
	}


	public static function isHandlingProdInstalled() {
		if (self::$_handlingProductInstalled==NULL ) {
			self::$_handlingProductInstalled = Mage::helper('wsacommon')
				->isModuleEnabled('Webshopapps_Handlingproduct','shipping/handlingproduct/active') ? true : false;
			if (self::$_handlingProductInstalled) {
				self::$_handlingProdModel=Mage::getModel('handlingproduct/handlingproduct');
			}

		}
		return self::$_handlingProductInstalled;
	}

	public static function getHandlingProductModel() {
		return self::$_handlingProdModel;
	}

	public function getWeightCeil($weight) {
        if(floor($weight) == $weight) {
            return floor($weight);
        }

		if ($this->isWholeWeightRounding()) {
			return ceil(round($weight,2));
		}	else {
			return round($weight,2);
		}
	}

	/**
	 * Simple function to round a value to two significant figures
	 * @param int $value The value to be rounded
	 */
	public function toTwoDecimals($value=-1) {
		return round($value,2);		// changed from ceil as worried about above causing an issue
	}

  	public function percentageOverflow($maxQtyPerBox,$qty,$percentageFull=0) {
    	return $this->getPercentageFull($maxQtyPerBox,$qty,$percentageFull) > 100 ? true : false;
    }

    public function formatXML($xmlString) {

        try {
            $simpleXml = new SimpleXMLElement($xmlString);
            $dom = new DOMDocument('1.0');
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($simpleXml->asXML());
            return $dom->saveXML();
        } catch (Exception $e) {
            return $xmlString;
        }

    }

    public function getPercentageFull($maxQtyPerBox,$qty,$percentageFull=0) {
    	 if (is_numeric($maxQtyPerBox) && $maxQtyPerBox>=1) {
    		$indItemPercentage = 100/$maxQtyPerBox;
    		$newPercentageFull = ($indItemPercentage*$qty) + $percentageFull;
    		return $newPercentageFull;
    	 } else {
    	 	return $percentageFull;
    	 }
    }

	public function getPercentageQtyLeft($maxQtyPerBox,$qty,$percentageFull=0) {
    	$indItemPercentage = 100/$maxQtyPerBox;
    	$percentageLeft = 100- $percentageFull;

    	$allowedQty = ceil($percentageLeft/$indItemPercentage);

    	return $allowedQty;
    }

    public function getBoxId($length, $width, $height) {
    	$dimensions = array($length, $width, $height);
    	sort($dimensions);
    	return $dimensions[0].'_'.$dimensions[1].'_'.$dimensions[2];
    }

}
