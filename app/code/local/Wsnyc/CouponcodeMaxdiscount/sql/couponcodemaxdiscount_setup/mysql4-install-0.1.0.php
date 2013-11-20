<?php

$installer = $this;
 
$installer->startSetup();

$installer->getConnection()->addColumn(
    $this->getTable('salesrule/rule'),
    'maxdiscount',
    'int(11) unsigned NULL'
);

//$installer->run("ALTER TABLE ".$installer->getTable('salesrule/coupon')." ADD COLUMN maxdiscount INT(11) NULL");
 
$installer->endSetup();