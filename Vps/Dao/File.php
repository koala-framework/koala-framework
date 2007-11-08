<?php
class Vps_Dao_File extends Vps_Db_Table
{
    protected $_name = 'vps_uploads';
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

    public function getFileSource($uploadId)
    {
        return $this->_getUploadDir() . '/' . $uploadId;
    }

    public function getFileSize($uploadId)
    {
        $row = $this->find($uploadId)->current();
        if ($row) {
            $file = $this->getFileSource($uploadId);
            if (is_file($file)) {
                return round((filesize($file) /1024), 2);
            }
        }
        return null;
    }

    public function generateUrl($uploadId, $id, $filename, $type = self::SHOW, $addRandom = false)
    {
        $row = $this->find($uploadId)->current();
        if ($row) {
            if ($type == self::SHOW) {
                $checksum = md5(Vps_Media_Password::CACHE . $id);
            } else {
                $checksum = md5(Vps_Media_Password::ORIGINAL . $id);
            }
            $extension = $row->extension;
            $random = $addRandom ? '?' . uniqid() : '';
            return "/media/$uploadId/$id/$checksum/$filename.$extension$random";
        } else {
            return null;
        }
    }

    public function getOriginalUrl($uploadId)
    {
        $row = $this->find($uploadId)->current();
        if ($row && is_file($this->getFileSource($uploadId))) {
            $extension = $row->extension;
            return "/media/$uploadId.$extension";
        }
        return null;
    }

    /**
     * Wenn id==null, wird neuer Datensatz angelegt, sonst bestehender geändert.
     */
    public function uploadFile($filedata, $id = null)
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
        $row = $id ? $this->find($id)->current() : null;
        if ($row) {
            $this->deleteFile($id);
        } else {
            $row = $this->createRow();
        }
        $row->filename = $filename;
        $row->extension = $extension;
        $id = $row->save();
        
        $filename = $uploadDir . '/' . $id;
        if (move_uploaded_file($filedata['tmp_name'], $filename)) {
            chmod($filename, 0664);
            return $id;
        } else {
            $row->delete();
            return null;
        }
    }

    public function delete($id)
    {
        if (is_array($id)) { // Datensatz tatsächlich löschen
            parent::delete($id);
        } else {
            $row = $this->find($id)->current();
            if ($row) {
                $this->deleteFile($id);
                $x = $row->delete(); // ruft delete mit Array auf (siehe oben)
            }
        }
    }

    public function deleteFile($id)
    {
        $filename = $this->getFileSource($id);
        if (is_file($filename)) {
            unlink($filename);
        }
        $this->deleteCache($id);
    }

    public function deleteCache($id)
    {
        $this->_recursiveRemoveDirectory($this->_getUploadDir() . '/cache/' . $id);
    }

    private function _recursiveRemoveDirectory($dir)
    {
        if (is_dir($dir)) {
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
        }
        return true;
    }

    private function _createDirectory($dir)
    {
        if (!is_dir($dir)) {
            mkdir($dir, 0775);
            chmod($dir, 0775);
        }
    }

}