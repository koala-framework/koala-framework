<?php
class Vps_Auto_Field_File extends Vps_Auto_Field_SimpleAbstract
{
    private $_directory;
    private $_extensions;
    private $_media;

    public function __construct($directory, $extensions)
    {
        parent::__construct('vps_upload_id');
        $this->setInputType('file');
        $this->_directory = $directory;
        $this->_extensions = $extensions;
    }

    public function load($row)
    {
        $this->_media = $row->vps_upload_id ? '/media/' . $row->vps_upload_id : '' ;
        return array();
    }
    
    public function getMetaData()
    {
        $data = parent::getMetaData();
        $data['media'] = $this->_media;
        return $data;
    }

    public function save(Zend_Db_Table_Row_Abstract $row, $postData)
    {
        $file = isset($_FILES['vps_upload_id']) ? $_FILES['vps_upload_id'] : array();
        $fileTable = new Vps_Dao_File();

        if ($row->vps_upload_id == 0 && (!isset($file['error']) || $file['error'] == UPLOAD_ERR_NO_FILE)) {
            throw new Vps_ClientException('Please select a file');
        }
        
        if (isset($postData['vps_upload_id_delete']) && $postData['vps_upload_id_delete'] == '1') {
            $fileTable->deleteFile($row->vps_upload_id);
            $row->vps_upload_id = null;
        }

        if (isset($file['tmp_name']) && is_file($file['tmp_name'])) {
            $extension = substr(strrchr($file['name'], '.'), 1);
            if (!in_array($extension, $this->_extensions)) {
                throw new Vps_ClientException('File-extension not allowed. Allowed: ' . implode(', ', $this->_extensions));
            }

            try {
                $id = $fileTable->uploadFile($file, $this->_directory, $row->vps_upload_id);
                if ($id) {
                    $row->vps_upload_id = $id;
                }
            } catch (Vps_Exception $e) {
                throw new Vps_ClientException($e->getMessage());
            }
        }
        
        $fileTable->deleteCache($row->vps_upload_id);

        parent::load($row);
    }
}
