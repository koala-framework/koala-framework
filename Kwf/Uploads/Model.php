<?php
class Kwf_Uploads_Model extends Kwf_Model_Db_Proxy
{
    protected $_table = 'kwf_uploads';
    protected $_rowClass = 'Kwf_Uploads_Row';
    protected $_filters = array(
        'id' => 'Kwf_Filter_Row_GenerateUuid'
    );
    private $_uploadDir;

    protected function _init()
    {
        parent::_init();

        if (Kwf_Config::getValue('aws.uploadsBucket')) {
            $this->_rowClass = 'Kwf_Util_Aws_Uploads_S3Row';
        }
    }

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
            if (!is_dir($this->_uploadDir)) {
                throw new Kwf_Exception("Path for uploads do not exist: {$this->_uploadDir}");
            } else if (!is_writable($this->_uploadDir)) {
                throw new Kwf_Exception("Path for uploads is not writeable: {$this->_uploadDir}");
            }
        }
        return $this->_uploadDir;
    }

    public function writeFile($contents, $filename, $extension, $mimeType = null)
    {
        $s = $this->select()
            ->whereEquals('md5_hash', md5($contents))
            ->limit(1);
        $existingRow = $this->getRow($s);
        if ($existingRow) {
            $existingFileSource = $existingRow->getFileSource();
            if (file_exists($existingFileSource) && filesize($existingFileSource) == strlen($contents)) {
                return $existingRow;
            }
        }

        $row = $this->createRow();
        $row->writeFile($contents, $filename, $extension, $mimeType);
        return $row;
    }

    public function copyFile($file, $filename, $extension, $mimeType = null)
    {
        if (!file_exists($file)) {
            throw new Kwf_Exception("File '$file' does not exist");
        }
        $s = $this->select()
            ->whereEquals('md5_hash', md5_file($file))
            ->limit(1);
        $existingRow = $this->getRow($s);
        if ($existingRow) {
            $existingFileSource = $existingRow->getFileSource();
            if (file_exists($existingFileSource) && filesize($existingFileSource) == filesize($file)) {
                return $existingRow;
            }
        }

        $row = $this->createRow();
        $row->copyFile($file, $filename, $extension, $mimeType);
        return $row;
    }

    public function uploadFile($filedata)
    {
        self::verifyUpload($filedata);

        $filename = substr($filedata['name'], 0, strrpos($filedata['name'], '.'));
        $extension = substr(strrchr($filedata['name'], '.'), 1);
        return $this->copyFile($filedata['tmp_name'], $filename, $extension, $filedata['type']);
    }

    public static function verifyUpload($filedata)
    {
        if ($filedata['error'] == UPLOAD_ERR_NO_FILE || !$filedata['tmp_name'] || !file_exists($filedata['tmp_name'])) {
            throw new Kwf_Exception('No File was uploaded.');
        }

        if ($filedata['error'] == UPLOAD_ERR_INI_SIZE || $filedata['error'] == UPLOAD_ERR_FORM_SIZE) {
            throw new Kwf_ClientException(trlKwf('The file is larger than the maximum upload amount.'));
        }

        if ($filedata['error'] == UPLOAD_ERR_PARTIAL) {
            throw new Kwf_ClientException(trlKwf('The file was not uploaded completely.'));
        }

        if ($filedata['error'] != UPLOAD_ERR_OK) {
            throw new Kwf_Exception('An Error when processing file upload happend: '.$filedata['error']);
        }
    }
}
