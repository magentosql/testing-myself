<?php

class Wsnyc_Challenge_Model_Observer {

    public function switchChallengePages() {

        if ($this->_checkSwitchTime()) {
            $oldPage = Mage::getModel('cms/page')->load('30-day-clean-home-challenge', 'identifier');
            $oldPage->setIdentifier('30-day-clean-home-challenge-disabled')
                ->setIsActive(false)
                ->save();

            $newPage = Mage::getModel('cms/page')->load('30-day-clean-home-challenge-end', 'identifier');
            $newPage->setIsActive(true)
                ->setIdentifier('30-day-clean-home-challenge')
                ->save();

            $this->refreshCache();
        }
    }

    public function refreshCache() {
        try {
            Mage::dispatchEvent('adminhtml_cache_flush_all');
            Mage::app()->getCacheInstance()->flush();
        }
        catch (Exception $e) {}
    }

    protected function _checkSwitchTime() {
        return time() >= Mage::getStoreConfig('design/head/switchtime');
    }

}
