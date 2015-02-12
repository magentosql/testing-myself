<?php

$installer = $this;

$installer->startSetup();

$setup = Mage::getModel('customer/entity_setup', 'core_setup');

$entityTypeId = $setup->getEntityTypeId('customer');
$attributeSetId = $setup->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $setup->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$setup->addAttribute("customer", "company_name", array(
    "type" => "varchar",
    "backend" => "",
    "label" => "Company Name",
    "input" => "text",
    "source" => "",
    "visible" => true,
    "required" => false,
    "default" => "",
    "frontend" => "",
    "unique" => false
));

$attribute = Mage::getSingleton("eav/config")->getAttribute("customer", "company_name");

$setup->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'company_name', '400');

$usedInForms = array('adminhtml_customer', 'customer_account_edit', 'adminhtml_checkout');

$attribute->setData("used_in_forms", $usedInForms)
        ->setData("is_used_for_customer_segment", true)
        ->setData("is_system", 0)
        ->setData("is_user_defined", 1)
        ->setData("is_visible", 1)
        ->setData("sort_order", 100)
        ->save();

$installer->endSetup();
