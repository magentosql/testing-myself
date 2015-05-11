<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at http://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   Advanced Reports
 * @version   1.0.0
 * @build     370
 * @copyright Copyright (C) 2015 Mirasvit (http://mirasvit.com/)
 */


class Mirasvit_MstCore_Helper_Code extends Mage_Core_Helper_Data { const EE_EDITION = 'EE'; const CE_EDITION = 'CE'; protected static $_edition = false; protected $k; protected $s; protected $l; protected $v; protected $b; protected $d; public function getStatus($sp84013b = null) { $spe4a940 = $this->sp669a2e(); if (strpos($spe4a940, '127.') !== false || strpos($spe4a940, '192.') !== false) { return true; } if ($sp84013b) { $spb9a7ec = $this->sp6b5c3c($sp84013b); $spf7eeac = $this->sp01cb11($spb9a7ec); if ($spf7eeac) { if (get_class($spf7eeac) !== 'Mirasvit_MstCore_Helper_Code') { return $spf7eeac->getStatus(null); } else { return true; } } else { return 'Wrong extension package!'; } } else { return $this->sp2514dc(); } return true; } public function getOurExtensions() { $sp2dc4d9 = array(); foreach (Mage::getConfig()->getNode('modules')->children() as $spc07127 => $spb9a7ec) { if ($spb9a7ec->active != 'true') { continue; } if (strpos($spc07127, 'Mirasvit_') === 0) { if ($spc07127 == 'Mirasvit_MstCore' || $spc07127 == 'Mirasvit_MCore') { continue; } $sp30342c = explode('_', $spc07127); if ($spf7eeac = $this->sp01cb11($sp30342c[1])) { if (method_exists($spf7eeac, '_sku') && method_exists($spf7eeac, '_version') && method_exists($spf7eeac, '_build') && method_exists($spf7eeac, '_key')) { $sp2dc4d9[] = array('s' => $spf7eeac->_sku(), 'v' => $spf7eeac->_version(), 'r' => $spf7eeac->_build(), 'k' => $spf7eeac->_key()); } } } } return $sp2dc4d9; } private function sp01cb11($sp001330) { $spe0c564 = Mage::getBaseDir() . '/app/code/local/Mirasvit/' . $sp001330 . '/Helper/Code.php'; if (file_exists($spe0c564)) { $spf7eeac = Mage::helper(strtolower($sp001330) . '/code'); return $spf7eeac; } return false; } private function sp6b5c3c($sp84013b) { if (is_object($sp84013b)) { $sp84013b = get_class($sp84013b); } $sp84013b = explode('_', $sp84013b); if (isset($sp84013b[1])) { return $sp84013b[1]; } return false; } private function sp2514dc() { $sp3805bc = false; $spb3b9f0 = $this->sp2c38b0(); $sp06d1da = $this->spa446ae(); if (!$sp06d1da) { $this->sp82c9d7(); $sp06d1da = $this->spa446ae(); } if ($sp06d1da && isset($sp06d1da['status'])) { if ($sp06d1da['status'] === 'active') { if (abs(time() - $sp06d1da['time']) > 24 * 60 * 60) { $this->sp82c9d7(); } $sp3805bc = true; } else { $this->sp82c9d7(); $sp3805bc = $sp06d1da['message']; } } return $sp3805bc; } private function spa446ae() { $sp4f9e7e = 'mstcore_' . $this->sp36278c(); $sp7a13b5 = Mage::getModel('core/flag'); $sp7a13b5->load($sp4f9e7e, 'flag_code'); if ($sp7a13b5->getFlagData()) { $sp06d1da = @unserialize(@base64_decode($sp7a13b5->getFlagData())); if (is_array($sp06d1da)) { return $sp06d1da; } } return false; } private function sp8fc676($sp06d1da) { $sp4f9e7e = 'mstcore_' . $this->sp36278c(); $sp7a13b5 = Mage::getModel('core/flag'); $sp7a13b5->load($sp4f9e7e, 'flag_code'); $sp06d1da = base64_encode(serialize($sp06d1da)); $sp7a13b5->setFlagCode($sp4f9e7e)->setFlagData($sp06d1da); $sp7a13b5->getResource()->save($sp7a13b5); return $this; } private function sp82c9d7() { $spaa2a12 = array(); $spaa2a12['v'] = 3; $spaa2a12['d'] = $this->sp2c38b0(); $spaa2a12['ip'] = $this->sp669a2e(); $spaa2a12['mv'] = Mage::getVersion(); $spaa2a12['me'] = $this->spa0c5f2(); $spaa2a12['l'] = $this->sp36278c(); $spaa2a12['k'] = $this->_key(); $spaa2a12['uid'] = $this->spb8931a(); $spa58445 = @unserialize($this->spc4796c('http://mirasvit.com/lc/check/', $spaa2a12)); if (isset($spa58445['status'])) { $spa58445['time'] = time(); $this->sp8fc676($spa58445); } return $this; } private function spc4796c($sped2ef6, $spaa2a12) { $sp06c26a = new Varien_Http_Adapter_Curl(); $sp06c26a->write(Zend_Http_Client::POST, $sped2ef6, '1.1', array(), http_build_query($spaa2a12, '', '&')); $sp06d1da = $sp06c26a->read(); $sp06d1da = preg_split('/^\\r?$/m', $sp06d1da, 2); $sp06d1da = trim($sp06d1da[1]); return $sp06d1da; } private function sp669a2e() { return Mage::helper('core/http')->getServerAddr(false); } private function sp2c38b0() { return Mage::helper('core/url')->getCurrentUrl(); } private function spa0c5f2() { if (!self::$_edition) { $sp10ac20 = BP . DS . 'app' . DS . 'etc' . DS . 'modules' . DS . 'Enterprise' . '_' . 'Enterprise' . '.xml'; $spce5fe6 = BP . DS . 'app' . DS . 'code' . DS . 'core' . DS . 'Enterprise' . DS . 'Enterprise' . DS . 'etc' . DS . 'config.xml'; $sp37c0dc = !file_exists($sp10ac20) || !file_exists($spce5fe6); if ($sp37c0dc) { self::$_edition = self::CE_EDITION; } else { self::$_edition = self::EE_EDITION; } } return self::$_edition; } public function _key() { return $this->k; } public function _sku() { return $this->s; } private function sp36278c() { return $this->l; } public function _version() { return $this->v; } public function _build() { return $this->b; } private function spa5bff3() { return $this->d; } private function spb8931a() { $sp1df96a = Mage::getConfig()->getResourceConnectionConfig('core_read'); return md5($sp1df96a->dbname . $sp1df96a->dbhost); } public function onControllerActionPredispatch($spf7f1b7) { } public function onModelSaveBefore($spf7f1b7) { } public function onCoreBlockAbtractToHtmlAfter($spf7f1b7) { $spb87d9c = $spf7f1b7->getBlock(); if (is_object($spb87d9c) && substr(get_class($spb87d9c), 0, 9) == 'Mirasvit_') { $sp3805bc = $this->getStatus(get_class($spb87d9c)); if ($sp3805bc !== true) { $spf7f1b7->getTransport()->setHtml("<ul class='messages'><li class='error-msg'><ul><li>{$sp3805bc}</li></ul></li></ul>"); } } } }