<?php
class Kwf_Uploads_Row extends Kwf_Model_Proxy_Row
{
    protected function _deleteFile()
    {
        $filename = $this->getFileSource();
        if ($filename && is_file($filename)) {
            unlink($filename);
        }
    }

    protected function _putFileContents($contents)
    {
        $filename  = $this->getFileSource();
        $handle = fopen($filename, "w");
        $pointer = 0;
        $length = 1024;
        $bytesWritten = 0;
        while ($contentPart = substr($contents, $pointer, $length)) {
            $bytesWritten += fwrite($handle, $contentPart);
            $pointer = $pointer+$length;
        }
        fclose($handle);
        if ($bytesWritten != strlen($contents)) {
            throw new Kwf_Exception("Writing file failed");
        }
    }

    public function writeFile($contents, $filename, $extension, $mimeType = null)
    {
        $this->filename = $filename;
        $this->extension = $extension;
        $mimeType = self::detectMimeType($mimeType, $contents);
        $this->mime_type = $mimeType;
        $this->save();
        $this->_putFileContents($contents);
        return $this;
    }

    public function copyFile($file, $filename, $extension, $mimeType = null)
    {
        if (!file_exists($file)) {
            throw new Kwf_Exception("File '$file' does not exist");
        }
        $this->writeFile(file_get_contents($file), $filename, $extension, $mimeType);
        return $this;
    }

    public function uploadFile($filedata)
    {
        $this->verifyUpload($filedata);

        $filename = substr($filedata['name'], 0, strrpos($filedata['name'], '.'));
        $extension = substr(strrchr($filedata['name'], '.'), 1);
        $this->copyFile($filedata['tmp_name'], $filename, $extension, $filedata['type']);
        return $this;
    }

    public function verifyUpload($filedata)
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

    public static function detectMimeType($mimeType, $contents)
    {
        $ret = $mimeType;
        if (!$mimeType || $mimeType == 'application/octet-stream') {
            if (function_exists('finfo_open')) {
                //für andere server muss dieser pfad vielleicht einstellbar gemacht werden
                $path = false;
                if (is_file('/usr/share/file/magic')) {
                    $path = '/usr/share/file/magic';
                } else if (is_file('/usr/share/misc/magic')) {
                    $path = '/usr/share/misc/magic';
                } else {
                    $path = null;
                }
                $finfo = new finfo(FILEINFO_MIME, $path);
                $ret = $finfo->buffer($contents);
                $ret = str_replace('; charset=binary', '', $ret);
                if($ret == 'application/zip') {
                    $path = dirname(__FILE__).'/magic';
                    $finfo = new finfo(FILEINFO_MIME, $path);
                    $ret = $finfo->buffer($contents);
                    $ret = str_replace('; charset=binary', '', $ret);
                }
            } else {
                throw new Kwf_Exception("Can't autodetect mimetype, install FileInfo extension");
            }
        }

        if (!$ret) {
            $ret = 'application/octet-stream';
        }
        return $ret;
    }

    public function getFileSource()
    {
        if (!$this->id) {
            return null;
        }
        return $this->getModel()->getUploadDir() . '/' . $this->id;
    }
    public function getFileSize()
    {
        $file = $this->getFileSource();
        if ($file && is_file($file)) {
            return filesize($file);
        }
        return null;
    }

    public function getHashKey()
    {
        return Kwf_Util_Hash::hash($this->id);
    }

    //wird von upload-feld verwendet
    public function getFileInfo()
    {
        $ret = array(
            'uploadId' => $this->id,
            'mimeType' => $this->mime_type,
            'filename' => $this->filename,
            'extension'=> $this->extension,
            'fileSize' => $this->getFileSize(),
            'hashKey'  => $this->getHashKey()
        );
        if (!$this->id && is_file($this->filename)) {
            $ret['mimeType'] = $this->_getMimeType($this->filename);
            $ret['extension'] = substr(strrchr($this->filename, '.'), 1);
        }
        $size = @getimagesize($this->getFileSource());
        if ($size) {
            $ret['image'] = true;
            if (abs(Kwf_Media_Image::getExifRotation($this->getFileSource())) == 90) {
                $size = array($size[1], $size[0]);
            }
            $ret['imageWidth'] = $size[0];
            $ret['imageHeight'] = $size[1];
            $ret['imageHandyScaleFactor'] = Kwf_Media_Image::getHandyScaleFactor($this->getFileSource());
        } else {
            $ret['image'] = false;
        }
        return $ret;
    }

    protected function _beforeDelete()
    {
        parent::_beforeDelete();
        $this->_deleteFile();
    }

    public function getImageDimensions()
    {
        $size = @getimagesize($this->getFileSource());
        if ($size) {
            $ret = array();
            $ret['width'] = $size[0];
            $ret['height'] = $size[1];
        } else {
            $ret = false;
        }
        return $ret;
    }

    /*
    TODO
    public function duplicate(array $data = array())
    {
        $new = parent::duplicate($data);
        if (file_exists($this->getFileSource())) {
            copy($this->getFileSource(), $new->getFileSource());
        }
        return $new;
    }
    */
}
