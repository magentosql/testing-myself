<?php

$installer = $this;
$installer->startSetup();
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');

$this->addAttribute('sales_flat_order', 'signature_required', array(
    'label' => 'Is signature required?',
    'type' => 'int',
    'input' => 'text',
    'visible' => true,
    'required' => false,
    'position' => 1,
    'visible_on_front'  => false,
    'default' => 0,
));

$installer->addAttribute("quote", "signature_required", array("type"=>"int"));

$installer->endSetup();