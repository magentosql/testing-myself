<?php
$installer = $this;
$installer->startSetup();

$this->_conn->addColumn($this->getTable('wsnyc_questionsanswers/question'), 'from_backend', 'int unsigned not null default 0');
$this->_conn->addColumn($this->getTable('wsnyc_questionsanswers/question'), 'published', 'int unsigned not null default 0');

$installer->endSetup();