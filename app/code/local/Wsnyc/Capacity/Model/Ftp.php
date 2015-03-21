<?php

/**
 * Class for making FTP connection and file transferring
 */
class Wsnyc_Capacity_Model_Ftp extends Varien_Object {
    
    /**
     * Paths to configurable data
     */
    const XML_PATH_FTP_LOGIN = 'shipping/capacity/ftp_username';
    const XML_PATH_FTP_PASSWORD = 'shipping/capacity/ftp_password';
    const XML_PATH_FTP_SERVER = 'shipping/capacity/server';
    
    /**
     * FTP connection resource
     * 
     * @var resource
     */
    protected $_ftpConnection = false;


    protected function _construct() {
        if(!$this->getStoreId()) {
            $this->setStoreId('0');
        }
    }
    /**
     * FTP login getter
     * 
     * @return string
     */
    public function getFtpLogin() {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig(self::XML_PATH_FTP_LOGIN, $this->getStoreId()));
    }
    
    /**
     * FTP password getter
     * 
     * @return string
     */
    public function getFtpPassword() {
        return Mage::helper('core')->decrypt(Mage::getStoreConfig(self::XML_PATH_FTP_PASSWORD, $this->getStoreId()));
    }
    
    /**
     * FTP host getter
     * 
     * @return string
     */
    public function getFtpServer() {
        return Mage::getStoreConfig(self::XML_PATH_FTP_SERVER, $this->getStoreId());
    }
    
    /**
     * Connect to FTP server
     * 
     * @param string $server
     * @param boolean $reconnect If set to TRUE it will close current connection first.
     * @return \Wsnyc_Capacity_Model_Ftp
     * @throws Exception
     */
    public function connect($server, $reconnect = false) {
        if (false == $this->_ftpConnection || $reconnect) {
            $this->close();
            $this->_ftpConnection = ftp_connect($server, 21, 30);
            if (false === $this->_ftpConnection) {
                Mage::log('Unable to connect to the FTP server. Connection timeout', null, 'capacity.log');
                throw new Exception('Unable to connect to the FTP server. Connection timeout');
            }
            $this->login($this->getFtpLogin(), $this->getFtpPassword());            
        }
        
        return $this;
    }
    
    /**
     * Login to FTP server
     * 
     * @param string $user
     * @param string $password
     * @return \Wsnyc_Capacity_Model_Ftp
     * @throws Exception
     */
    public function login($user, $password) {
        if (!ftp_login($this->_ftpConnection, $user, $password)) {
            Mage::log('Unable to connect to the FTP server. Credentials are invalid', null, 'capacity.log');
            throw new Exception('Unable to connect to the FTP server. Credentials are invalid');
        }
        
        return $this;
    }
    
    public function setFtpPasv($flag = true) {
        ftp_pasv($this->_ftpConnection, $flag);
    }
    
    /**
     * Close connection with the server
     * 
     * @return \Wsnyc_Capacity_Model_Ftp
     */
    public function close() {
        if (false != $this->_ftpConnection) {
            ftp_close($this->_ftpConnection);
        }
        
        return $this;
    }
    
    public function upload($filePath) {
        $this->setFtpPasv();
        $fileName = 'IN/' . basename($filePath);
        Mage::log('Filename: ' . $filename, null, 'capacity.log');
        if (!ftp_put($this->_ftpConnection, $fileName, $filePath, FTP_BINARY)) {
            Mage::log('Error while uploading the file', null, 'capacity.log');
            throw new Exception('Error while uploading the file');
        }
        
        return true;
    }
    
    public function getDirContent($dir) {
        $this->setFtpPasv();
        return ftp_nlist($this->_ftpConnection, $dir);
    }
    
    public function getFile($fullPath) {
        $filename = 'ftp://' . $this->getFtpLogin() . ':' . $this->getFtpPassword() . '@' . $this->getFtpServer() . $fullPath;
        $handle = fopen($filename, "r");
        return $handle;
    }
    
    /**
     * Close connection when destorying the instance
     */
    public function __destruct() {
        $this->close();
    }
}