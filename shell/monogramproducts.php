<?php
/**
 * Error reporting
 */
error_reporting(E_ALL | E_STRICT);
 ini_set('display_errors', 1);

/**
 * Compilation includes configuration file
 */
define('MAGENTO_ROOT', dirname(__FILE__) . '/..');

$compilerConfig = MAGENTO_ROOT . '/includes/config.php';
if (file_exists($compilerConfig)) {
    include $compilerConfig;
}

$mageFilename = MAGENTO_ROOT . '/app/Mage.php';

require_once $mageFilename;

Varien_Profiler::enable();

#if (isset($_SERVER['MAGE_IS_DEVELOPER_MODE'])) {
    Mage::setIsDeveloperMode(true);
#}

ini_set('display_errors', -1);

umask(0);

/* Store or website code */
$mageRunCode = isset($_SERVER['MAGE_RUN_CODE']) ? $_SERVER['MAGE_RUN_CODE'] : '';

/* Run store or run website */
$mageRunType = isset($_SERVER['MAGE_RUN_TYPE']) ? $_SERVER['MAGE_RUN_TYPE'] : 'store';

Mage::app($mageRunCode, $mageRunType);

$attrCode = 'monogram';
$collection = Mage::getModel('catalog/product')->getCollection()->addAttributeToSelect('*')->addAttributeToFilter($attrCode, 1);
Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);
foreach($collection as $product) {
    $duplicate = $product->duplicate();
    $duplicate->setName($duplicate->getName() . ' with monogram');
    $duplicate->setSku($product->getSku() . '-M');
    $duplicate->setStatus(1);
    $duplicate->getResource()->save($duplicate);
    $product->setData($attrCode, 0)->getResource()->saveAttribute($product, $attrCode);
}