<?php

/**
 * Class for making FTP connection and file transferring
 */
class Wsnyc_Capacity_Model_Ftp {
    
    /**
     * Paths to configurable data
     */
    const XML_PATH_FTP_LOGIN = 'shipping/capacity/ftp_username';
    const XML_PATH_FTP_PASSWORD = 'shipping/capacity/ftp_password';
    const XML_PATH_FTP_SERVER = 'shipping/capacity/server';
    
    /**
     * FTP login getter
     * 
     * @return string
     */
    public function getFtpLogin() {
        return Mage::getStoreConfig(self::XML_PATH_FTP_LOGIN);
    }
    
    /**
     * FTP password getter
     * 
     * @return string
     */
    public function getFtpPassword() {
        return Mage::getStoreConfig(self::XML_PATH_FTP_PASSWORD);
    }
    
    /**
     * FTP host getter
     * 
     * @return string
     */
    public function getFtpServer() {
        return Mage::getStoreConfig(self::XML_PATH_FTP_SERVER);
    }
}