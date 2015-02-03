<?php

$installer = $this;
$installer->startSetup();

$setup = new Mage_Eav_Model_Entity_Setup('catalog_setup');
$setup->removeAttribute(Mage_Catalog_Model_Product::ENTITY, 'seosubfooter_blurb');
$setup->removeAttribute(Mage_Catalog_Model_Category::ENTITY, 'seosubfooter_blurb');


/**
 * @var Varien_Db_Adapter_Pdo_Mysql $connection *
 */
$connection = $installer->getConnection();
$connection->dropColumn($installer->getTable('cms/page'), 'seosubfooter_blurb');
if (Mage::getConfig()->getModuleConfig('Wsnyc_QuestionsAnswers')->asArray() != '') {
    $connection->dropColumn($installer->getTable('wsnyc_questionsanswers/category'), 'seosubfooter_blurb');
}
$connection->dropTable($installer->getTable('seosubfooter/blurb'));
$installer->endSetup();
