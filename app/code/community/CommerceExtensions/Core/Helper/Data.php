<?php

class CommerceExtensions_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
  public function getHandles()
  {
    return Mage::app()->getLayout()->getUpdate()->getHandles();
  }
  
  public function getFullActionName()
  {
	return Mage::app()->getFrontController()->getAction()->getFullActionName();
  }	
  	
  public function getFileExtension($filepath)
  {
	$filename = basename($filepath);
	$parts = explode('.',$filename);
	$extension = end($parts);
	return $extension;
  }	
}