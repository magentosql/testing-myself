<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    ALTER TABLE {$this->getTable('wsnyc_homepagebanner')}
    CHANGE COLUMN `content` `banner_content` TEXT NULL DEFAULT NULL
    ");

$installer->endSetup();