<?php

ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);

if (!defined('SS_BASE_PATH')) {
	define('SS_BASE_PATH', __DIR__ . '/..');
}

/*
 * Dynamic product price based on current customer's session
 *
 * Given a list of product ids, returns the actual final price of each
 *  product. Final price is based on the request store/website context,
 *  and current user session customer group.
 *
 * Important: If the magento store does not have it's global/store session
 *  cookie path and domain setup as '/', then the entry point for this script
 *  needs to be at the same spot as the store/website's index.php. If the
 *  magento installation is split into several websites with their own
 *  directory and index.php, then this is most likely the case.
 *
 *  To do this, just put a new script in each websites directory, where
 *  it's 'index.php' file is. Define 'SS_BASE_PATH' to point to the base
 *  of the magento installation, and 'require' this file.
 *
 *  Example from base directory:
 *     define('SS_BASE_PATH', __DIR__ . '/');
 *     require_once('./searchspring/searchspring-live-pricing.php');
 *
 *  Example from specific website/store directory:
 *     define('SS_BASE_PATH', __DIR__ . '/..');
 *     require_once('../searchspring/searchspring-live-pricing.php');
 *
 */

require_once(SS_BASE_PATH . '/app/Mage.php');
umask(0);

// Pull Scope Context
$scopeCode = '';
$scopeType = 'store';

// Pull Scope Context from params
if (isset($_GET['scope_code'])) $scopeCode = $_GET['scope_code'];
if (isset($_GET['scope_type'])) $scopeType = $_GET['scope_type'];

// Load up Magento
$app = Mage::app($scopeCode,$scopeType);

// Load up frontend session (usually done by magento run sequence)
$session = Mage::getSingleton('core/session', array('name' => 'frontend'));

// Load up frontend event area (usually done by magento run sequence)
$app->loadAreaPart(Mage_Core_Model_App_Area::AREA_FRONTEND, Mage_Core_Model_App_Area::PART_EVENTS);

// Header for json return type
$app->getResponse()->setHeader('Content-type', 'application/json',true);

// Get products to load
$products = explode(',', $app->getRequest()->getParam('ids'));

// Get Prices
$pricing = array();

foreach($products as $productId) {

	$_product = Mage::getModel('catalog/product')->load($productId);

	$price = $_product->getFinalPrice() * Mage::helper('multiplewebsites')->getCustomerGroupPriceModifier();
	$regularPrice = $_product->getPrice() * Mage::helper('multiplewebsites')->getCustomerGroupPriceModifier();
	
	$pricing[$productId] = array('price' => '$'.number_format($price, 2, '.', ''), 'regprice' => '$'.number_format($regularPrice, 2, '.', ''));

}

// Send Results
$app->getResponse()->sendHeaders();
print json_encode($pricing);

