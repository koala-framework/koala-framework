<?php
class Vps_Uploads_Model extends Vps_Model_Db_Proxy
{
    protected $_table = 'vps_uploads';
    protected $_rowClass = 'Vps_Uploads_Row';
    private $_uploadDir;

    public function setUploadDir($dir)
    {
        $this->_uploadDir = $dir;
        return $this;
    }

    public function getUploadDir()
    {
        if (!isset($this->_uploadDir)) {
            $this->_uploadDir = Vps_Config::getValue('uploads');

            if (!$this->_uploadDir) {
                throw new Vps_Exception(('Param "uploads" has to be set in the file application/config.ini.'));
            }
            if (!is_dir($this->_uploadDir) || !is_writable($this->_uploadDir)) {
                throw new Vps_Exception("Path for uploads is not writeable: {$this->_uploadDir}");
            }
        }
        return $this->_uploadDir;
    }
}
