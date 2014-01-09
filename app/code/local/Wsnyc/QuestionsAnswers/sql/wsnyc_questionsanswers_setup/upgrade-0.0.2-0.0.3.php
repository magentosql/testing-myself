<?php
$installer = $this;
$installer->startSetup();

$this->_conn->addColumn($this->getTable('wsnyc_questionsanswers/category'), 'identifier', 'varchar(255) not null default ""');

$installer->endSetup();