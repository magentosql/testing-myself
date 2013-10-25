<?php
/**
 * Created by PhpStorm.
 * User: msyrek
 * Date: 25.10.2013
 * Time: 11:59
 */
/* @var $installer Mage_Core_Model_Resource_Setup */
$installer = $this;

$installer->startSetup();

Mage::app()->setCurrentStore(Mage_Core_Model_App::ADMIN_STORE_ID);

$cmsPageData = array(
    'title' => 'Holiday shipping information',
    'root_template' => 'one_column',
    'meta_keywords' => '',
    'meta_description' => '',
    'identifier' => 'holiday-shipping-information',
    'content_heading' => '',
    'stores' => array(0),
    'content' => "Aenean vestibulum, est at bibendum ornare, risus eros adipiscing purus, tempor rutrum quam elit sit amet ipsum. Curabitur egestas urna et fermentum tristique. Pellentesque non quam nunc. Maecenas magna magna, euismod vitae justo quis, sagittis tempor nulla. Pellentesque vehicula sodales sapien consectetur imperdiet. Curabitur vulputate pulvinar orci, in iaculis odio volutpat id. Aliquam vitae sollicitudin est. Maecenas sem arcu, vehicula non sapien at, auctor lacinia urna. Etiam adipiscing mollis tortor vitae semper. Pellentesque vitae sem vulputate, tempor quam nec, suscipit mauris."
);

Mage::getModel('cms/page')->setData($cmsPageData)->save();

$installer->endSetup();
