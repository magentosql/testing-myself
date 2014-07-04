<?php

$this->addAttribute('catalog_product', 'more_description', array(
    'input' => 'textarea',
    'type' => 'text',
    'label' => 'More Info',
    'backend' => '',
    'visible' => true,
    'required' => false,
    'wysiwyg_enabled' => true,
    'visible_on_front' => true,
    'is_html_allowed_on_front' => true,
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'user_defined' => false
));
