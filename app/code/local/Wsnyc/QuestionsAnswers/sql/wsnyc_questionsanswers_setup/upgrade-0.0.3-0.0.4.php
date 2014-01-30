<?php
$installer = $this;
$installer->startSetup();

$this->_conn->addColumn($this->getTable('wsnyc_questionsanswers/question'), 'featured', 'int unsigned not null default 0');

$installer->endSetup();