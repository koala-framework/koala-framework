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
    
    /**
     * Wenn id==null, wird neuer Datensatz angelegt, sonst bestehender geändert.
     */
    public function uploadFile($filedata, $directory, $id = null)
    {
        $row = $id ? $this->find($id)->current() : null;
        
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
        
        // Falls überschrieben wird, alte Datei löschen
        if ($row) {
            $this->deleteFile($id);
        }
        
        // Falls Datei existiert, _1... anhängen
        $origName = substr($filedata['name'], 0, strrpos($filedata['name'], '.'));
        $extension = substr(strrchr($filedata['name'], '.'), 1);
        $x = 1; $name = $origName;
        while (is_file($uploadDir . $directory . $name . '.' . $extension)) {
            $rename = true;
            // Falls bestehende Datei gleich groß ist, bestehende Datei löschen
            if (filesize($uploadDir . $directory . $name . '.' . $extension) == filesize($filedata['tmp_name'])) {
                if (unlink($uploadDir . $directory . $name . '.' . $extension)) {
                    $rename = false;
                }
            }
            if ($rename) {
                $name = $origName . '_' . $x++;
            }
        }

        // Datei hochlade
        $filename = $uploadDir . $directory . $name . '.' . $extension;
        if (move_uploaded_file($filedata['tmp_name'], $filename)) {
            chmod($filename, 0664);
            // Hochgeladene Datei als Pfad in DB eintragen
            $path = $directory . $name . '.' . $extension;
            if ($row) {
                $row->path = $path;
                $row->save();
            } else {
                return $this->insert(array('path' => $path));
            }
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
            $this->deleteCache($id);
        }
    }
    
    public function deleteCache($id)
    {
        $this->_recursiveRemoveDirectory($this->_getUploadDir() . 'cache/' . $id);
    }
    
    private function _recursiveRemoveDirectory( $dir )
    {
        $d = dir($dir);
        while (FALSE !== ($entry = $d->read())) {
            if ( $entry == '.' || $entry == '..' ) { continue; }
            $entry = $dir . '/' . $entry;
            if (is_dir($entry)) {
                if (!$this->_recursiveRemoveDirectory($entry)) {
                    return false;
                }
                continue;
            }
            if (!@unlink($entry)) {
                $d->close();
                return false;
            }
        }
       
        $d->close();
        rmdir($dir);
        return true;
    }

}