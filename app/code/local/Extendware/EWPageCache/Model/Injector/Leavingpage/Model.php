<?php

class Extendware_EWPageCache_Model_Injector_Leavingpage_Model extends Extendware_EWPageCache_Model_Injector_Abstract
{
    public function getInjection(array $params = array(), array $request = array()) {
        $data = null;
        $type = isset($params['type']) ? $params['type'] : 'leavingpage/modal';
        /** @var Mage_Core_Model_Session $session */
        $session = Mage::getSingleton('core/session');
        $block = Mage::app()->getLayout()->createBlock($type, $session->getEncryptedSessionId());
        $data = $block->toHtml();
        return $data;
    }
}