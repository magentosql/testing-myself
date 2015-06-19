<?php

@include_once dirname(__FILE__) .'/lib/Mobile/Detect.php';

if (class_exists('Mobile_Detect', false) === true and array_key_exists('HTTP_USER_AGENT', $_SERVER) === true) {
	$device = 'desktop';
	$detect = new Mobile_Detect;
	$ua = $_SERVER['HTTP_USER_AGENT'];
	if ($detect->isMobile($ua) && !$detect->isTablet($ua)) {
		$device = 'mobile';
	} elseif ($detect->isTablet($ua)) {
		$device = 'tablet';
	}
	
	if ($device == 'mobile') {
		$ieVersion = $detect->version('IE');
		$androidVersion = $detect->version('Android', Mobile_Detect::VERSION_TYPE_FLOAT);
		$isSafari = (bool)$detect->version('Safari');
		if (($ieVersion && $ieVersion >= 10 || $androidVersion && $androidVersion < 4.4 && $isSafari)) {
			$device = 'desktop';
		}
	}
	
	$_SERVER['HTTP_USER_AGENT'] .= ' ew' . $device;
}
