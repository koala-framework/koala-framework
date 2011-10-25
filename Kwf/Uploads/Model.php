<?php
class Kwf_Uploads_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwf_uploads';
    protected $_rowClass = 'Kwf_Uploads_Row';
    private $_uploadDir;

    public function setUploadDir($dir)
    {
        $this->_uploadDir = $dir;
        return $this;
    }

    public function getUploadDir()
    {
        if (!isset($this->_uploadDir)) {
            $this->_uploadDir = Kwf_Config::getValue('uploads');

            if (!$this->_uploadDir) {
                throw new Kwf_Exception(('Param "uploads" has to be set in the file config.ini.'));
            }
            if (!is_dir($this->_uploadDir) || !is_writable($this->_uploadDir)) {
                throw new Kwf_Exception("Path for uploads is not writeable: {$this->_uploadDir}");
            }
        }
        return $this->_uploadDir;
    }
}
