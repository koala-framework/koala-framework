<?php
class Vps_Dao_Row_File extends Vps_Db_Table_Row_Abstract
{
    const SHOW = 1;
    const DOWNLOAD = 2;

    private function _getUploadDir()
    {
        $config = Zend_Registry::get('config');
        $uploadDir = $config->uploads;

        if (!$uploadDir) {
            throw new Vps_Exception('Param "uploads" has to be set in the file application/config.ini.');
        }

        return $uploadDir;
    }

    public function getFileSource()
    {
        if (!$this->id) return null;
        return $this->_getUploadDir() . '/' . $this->id;
    }
    public function getFileSize()
    {
        $file = $this->getFileSource();
        if (is_file($file)) {
            return round((filesize($file) /1024), 2);
        }
        return null;
    }
    public function generateUrl($class, $id, $filename, $type = self::SHOW, $addRandom = false)
    {
        if ($type == self::SHOW) {
            $checksum = md5(Vps_Media_Password::CACHE . $id);
        } else {
            $checksum = md5(Vps_Media_Password::ORIGINAL . $id);
        }
        $extension = $this->extension;
        $random = $addRandom ? '?' . uniqid() : '';
        return "/media/{$this->id}/$class/$id/$checksum/$filename.$extension$random";
    }

    public function getOriginalUrl()
    {
        if (is_file($this->getFileSource())) {
            return "/media/{$this->id}.{$this->extension}";
        }
        return null;
    }

    public function deleteFile()
    {
        $filename = $this->getFileSource();
        if (is_file($filename)) {
            unlink($filename);
        }
        $this->deleteCache();
    }

    public function deleteCache()
    {
        if ($this->id) {
            $this->_recursiveRemoveDirectory($this->_getUploadDir() . '/cache/' . $this->id);
        }
    }

    private function _recursiveRemoveDirectory($dir)
    {
        if (!file_exists($dir) || !is_dir($dir)) return;
        $iterator = new RecursiveDirectoryIterator($dir);
        foreach (new RecursiveIteratorIterator($iterator, RecursiveIteratorIterator::CHILD_FIRST) as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($dir);
    }

    //wird von Zend_Db_Table_Row_Abstract vorm löschen aufgerufen
    protected function _delete()
    {
        $this->deleteFile();
    }

    public function uploadFile($filedata)
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

        $filename = substr($filedata['name'], 0, strrpos($filedata['name'], '.'));
        $extension = substr(strrchr($filedata['name'], '.'), 1);
        
        // Falls überschrieben wird, alte Datei löschen
        $this->deleteFile();
        $this->filename = $filename;
        $this->extension = $extension;
        $this->save();
        
        $filename = $uploadDir . '/' . $this->id;
        if (move_uploaded_file($filedata['tmp_name'], $filename)) {
            chmod($filename, 0664);
        } else {
            $this->delete();
        }
    }
}
