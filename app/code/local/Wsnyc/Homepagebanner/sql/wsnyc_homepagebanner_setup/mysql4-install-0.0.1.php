<?php

$installer = $this;
$installer->startSetup();

$installer->run("
    DROP TABLE IF EXISTS {$this->getTable('wsnyc_homepagebanner')};
    CREATE TABLE {$this->getTable('wsnyc_homepagebanner')} (
    `banner_id` int(11) unsigned NOT NULL auto_increment,
    `title` varchar(255) NOT NULL default '',
    `filename` varchar(255) NOT NULL default '',
    `content` text NULL,
    `status` smallint(6) NOT NULL default '0',
    `webname` varchar(255) NULL,
    `weblink` varchar(255) NULL,
    `position` smallint (6) NULL,
    `created_time` datetime NUll,
    `update_time` datetime NULL,
    `stores` text default '',
    PRIMARY KEY (`banner_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

    ");
    
$installer->endSetup();