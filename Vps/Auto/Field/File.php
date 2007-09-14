<?php
class Vps_Auto_Field_File extends Vps_Auto_Field_SimpleAbstract
{
    private $_directory;
    private $_extensions;

    public function __construct($directory, $extensions)
    {
        parent::__construct('vps_upload_id');
        $this->setInputType('file');
        $this->_directory = $directory;
        $this->_extensions = $extensions;
    }

    public function load($row)
    {
        return array();
    }

    public function getMetaData()
    {
        $ret = parent::getMetaData();
        $ret['type'] = 'TextField';
        return $ret;
    }

    public function save(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        $name = $this->getName();
        $file = isset($_FILES[$name]) ? $_FILES[$name] : array();
        $fileTable = new Vps_Dao_File();

        if ($row->$name == 0 && (!isset($file['error']) || $file['error'] == UPLOAD_ERR_NO_FILE)) {
            throw new Vps_ClientException('Please select a file');
        }

        if (isset($file['tmp_name']) && is_file($file['tmp_name'])) {
            $extension = substr(strrchr($file['name'], '.'), 1);
            if (!in_array($extension, $this->_extensions)) {
                throw new Vps_ClientException('File-extension not allowed. Allowed: ' . implode(', ', $this->_extensions));
            }

            try {
                $id = $fileTable->uploadFile($file, $this->_directory, $row->$name);
                if ($id) {
                    $row->$name = $id;
                }
            } catch (Vps_Exception $e) {
                throw new Vps_ClientException($e->getMessage());
            }
        }
        
        $fileTable->deleteCache($row->$name);

        parent::load($row);
    }
}
