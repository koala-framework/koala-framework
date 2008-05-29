<?php
class Vps_Dao_Row_File extends Vps_Db_Table_Row_Abstract
{
    public static function getUploadDir()
    {
        $config = Zend_Registry::get('config');
        $uploadDir = $config->uploads;

        if (!$uploadDir) {
            throw new Vps_Exception(trlVps('Param "uploads" has to be set in the file application/config.ini.'));
        }
        if (!is_dir($uploadDir) || !is_writable($uploadDir)) {
            throw new Vps_Exception(trlVps('Path for uploads is not writeable: {0}', $uploadDir));
        }

        return $uploadDir;
    }

    //hilfsfkt wird vor erstellen des caches aufgerufen damit die ordner korrekt
    //erstellt werden. passt nicht wirklich hier her.
    public static function prepareCacheTarget($target)
    {
        $uploadDir = Vps_Dao_Row_File::getUploadDir();
        if (!is_dir($uploadDir . '/cache')) {
            mkdir($uploadDir . '/cache', 0775);
            chmod($uploadDir . '/cache', 0775);
        }
        if (!is_dir(dirname($target))) {
            mkdir(dirname($target), 0775);
            chmod(dirname($target), 0775);
        }
    }

    public function getFileInfo()
    {
        $ret = array(
            'uploadId' => $this->id,
            'mimeType' => $this->mime_type,
            'filename' => $this->filename,
            'extension'=> $this->extension,
            'fileSize' => $this->getFileSize()
        );
        $size = @getimagesize($this->getFileSource());
        if ($size) {
            $ret['image'] = true;
            $ret['imageWidth'] = $size[0];
            $ret['imageHeight'] = $size[1];
        } else {
            $ret['image'] = false;
        }
        return $ret;
    }

    public function getFileSource()
    {
        if (!$this->id) return null;
        return self::getUploadDir() . '/' . $this->id;
    }

    public function getFileSize()
    {
        $file = $this->getFileSource();
        if ($file && is_file($file)) {
            return filesize($file);
        }
        return null;
    }

    public function deleteFile()
    {
        $filename = $this->getFileSource();
        if ($filename && is_file($filename)) {
            unlink($filename);
        }
        $this->deleteCache();
    }

    public function deleteCache()
    {
        if ($this->id) {
            $this->_recursiveRemoveDirectory(self::getUploadDir() . '/cache/' . $this->id);
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
            throw new Vps_Exception(trlVps('No File was uploaded.'));
        }

        if ($filedata['error'] == UPLOAD_ERR_INI_SIZE || $filedata['error'] == UPLOAD_ERR_FORM_SIZE) {
            throw new Vps_ClientException(trlVps('The file is larger than the maximum upload amount.'));
        }

        if ($filedata['error'] == UPLOAD_ERR_PARTIAL) {
            throw new Vps_ClientException(trlVps('The file was not uploaded completely.'));
        }

        $this->deleteFile();
        $this->filename = substr($filedata['name'], 0, strrpos($filedata['name'], '.'));
        $this->extension = substr(strrchr($filedata['name'], '.'), 1);

        if ($filedata['type'] == 'application/octet-stream') {
            //for flash uploads
            if (function_exists('finfo_open')) {
                $finfo = finfo_open(FILEINFO_MIME);
                $this->mime_type = finfo_file($finfo, $filedata['tmp_name']);
                finfo_close($finfo);
            } else if (function_exists('mime_content_type')) {
                $this->mime_type = mime_content_type($filedata['tmp_name']);
            } else {
                throw new Vps_Exception("Can't autodetect mimetype");
            }
        } else {
            $this->mime_type = $filedata['type'];
        }
        if (!$this->mime_type) {
            $this->mime_type = 'application/octet-stream';
        }
        $this->save();

        $filename = $this->getFileSource();
        if (move_uploaded_file($filedata['tmp_name'], $filename)) {
            chmod($filename, 0664);
        } else {
            $this->delete();
        }
    }

    public function copyFile($file, $filename, $extension, $mime_type)
    {
        if (!file_exists($file)) {
            throw new Vps_Exception(trlVps("File {0} does not exist", '\''.$file.'\''));
        }
        $this->deleteFile();
        $this->filename = $filename;
        $this->extension = $extension;
        $this->mime_type = $mime_type;
        $this->save();
        copy($file, $this->getFileSource());
        chmod($this->getFileSource(), 0664);
    }

    public function writeFile($contents, $filename, $extension, $mime_type)
    {
        $this->deleteFile();
        $this->filename = $filename;
        $this->extension = $extension;
        $this->mime_type = $mime_type;
        $this->save();
        file_put_contents($this->getFileSource(), $contents);
        chmod($this->getFileSource(), 0664);
    }

    public function duplicate($data = array())
    {
        $new = parent::duplicate($data);
        if (file_exists($this->getFileSource())) {
            copy($this->getFileSource(), $new->getFileSource());
            chmod($new->getFileSource(), 0664);
        }
        return $new;
    }
}
