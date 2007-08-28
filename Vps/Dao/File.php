<?php
class Vps_Dao_File extends Vps_Db_Table
{
    protected $_name = 'vps_uploads';
    
    private function _getUploadDir()
    {
        $config = Zend_Registry::get('config');
        $uploadDir = $config->uploads;
        
        if (!$uploadDir) {
            throw new Vps_Exception('Param "uploads" has to be set in the file application/config.ini.');
        }
        
        return $uploadDir;
    }
    
    public function uploadFile($filedata, $directory)
    {
        if ($filedata['error'] == UPLOAD_ERR_NO_FILE) {
            throw new Vps_Exception('Es wurde keine Datei hochgeladen.');
        }

        if ($filedata['error'] == UPLOAD_ERR_INI_SIZE || $filedata['error'] == UPLOAD_ERR_FORM_SIZE) {
            throw new Vps_Exception('Die Datei übersteigt die maximale Dategröße für Dateiuploads.');
        }

        if ($filedata['error'] == UPLOAD_ERR_PARTIAL) {
            throw new Vps_Exception('Die Datei wurde nicht vollständig hochgeladen.');
        }

        $uploadDir = $this->_getUploadDir();
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            throw new Vps_Exception('Dateiupload kann nicht in folgendes Verzeichnis schreiben: ' . $uploadDir);
        }

        $origName = substr($filedata['name'], 0, strrpos($filedata['name'], '.'));
        $extension = substr(strrchr($filedata['name'], '.'), 1);
        $x = 1; $name = $origName;
        while (file_exists($uploadDir . $directory . $name . '.' . $extension)) {
            $name = $origName . '_' . $x++;
        }

        $filename = $uploadDir . $directory . $name . '.' . $extension;
        if (move_uploaded_file($filedata['tmp_name'], $filename)) {
            $insert = array('path' => $directory . $name . '.' . $extension);
            return $this->insert($insert);
        }

        return null;
    }
    
    public function deleteFile($id)
    {
        $row = $this->find($id)->current();
        if ($row) {
            $filename = $this->_getUploadDir() . $row->path;
            if (is_file($filename)) {
                unlink($filename);
            }
        }
        $this->delete("id = '$id'");
    }
    
    public function deleteCacheFile($id, $componentId)
    {
        $row = $this->find($id)->current();
        if ($row) {
            $extension = strrchr($row->path, '.');
            $filename = $this->_getUploadDir() . $componentId . $extension;
            if (is_file($filename)) {
                unlink($filename);
            }
            $filename = $this->_getUploadDir() . $componentId . '.thumb' . $extension;
            if (is_file($filename)) {
                unlink($filename);
            }
        }
    }

}