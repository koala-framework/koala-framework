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
        $uploadsDir = $this->getModel()->getUploadDir() . '/' . substr($this->id, 0, 2);
        if (!is_dir($uploadsDir)) mkdir($uploadsDir);
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
        $this->md5_hash = md5($contents);
        $this->save();
        $this->_putFileContents($contents);
        $this->_saveImageDimensions();
        Kwf_Util_Upload::onFileWrite($this->getFileSource());
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
        $uploadsModelClass = Kwf_Config::getValue('uploadsModelClass');
        call_user_func(array($uploadsModelClass, 'verifyUpload'), $filedata);

        $filename = substr($filedata['name'], 0, strrpos($filedata['name'], '.'));
        $extension = substr(strrchr($filedata['name'], '.'), 1);
        $this->copyFile($filedata['tmp_name'], $filename, $extension, $filedata['type']);
        return $this;
    }

    /**
     * @deprecated use Kwf_Uploads_Model::verifyUpload instead
     */
    public function verifyUpload($filedata)
    {
        $uploadsModelClass = Kwf_Config::getValue('uploadsModelClass');
        return call_user_func(array($uploadsModelClass, 'verifyUpload'), $filedata);
    }

    public static function detectMimeType($mimeType, $contents)
    {
        if (!$mimeType) {
            $uri = 'data://application/octet-stream;base64,' . base64_encode($contents);
            $meta = getimagesize($uri);
            if (isset($meta['mime'])) {
                $mimeType = $meta['mime'];
            } else {
                $mimeType = 'application/octet-stream';
            }
        }
        return $mimeType;
    }

    public function getFileSource()
    {
        if (!$this->id) {
            return null;
        }
        $ret = $this->getModel()->getUploadDir() . '/' . substr($this->id, 0, 2) . '/' . $this->id;
        if (isset($this->id_old) && $this->id_old && !is_file($ret) && is_file($this->getModel()->getUploadDir() . '/' . $this->id_old)) {
            $ret = $this->getModel()->getUploadDir() . '/' . $this->id_old;
        }
        if (Kwf_Config::getValue('uploadsFetchLazyFrom') && !is_file($ret)) {
            $url = Kwf_Config::getValue('uploadsFetchLazyFrom').'/kwf/media/upload/download?uploadId='.$this->id.'&hashKey='.$this->getHashKey();
            $uploadsDir = $this->getModel()->getUploadDir() . '/' . substr($this->id, 0, 2);
            if (!is_dir($uploadsDir)) mkdir($uploadsDir);
            file_put_contents($ret, file_get_contents($url));
            Kwf_Util_Upload::onFileWrite($ret);
        }
        return $ret;
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
        $size = $this->getImageDimensions();
        if ($size) {
            $ret['image'] = true;
            $ret['imageHandyScaleFactor'] = Kwf_Media_Image::getHandyScaleFactor($size);
            if (abs($size['rotation']) == 90) {
                $size = array('width'=>$size['height'], 'height'=>$size['width']);
            }
            $ret['imageWidth'] = $size['width'];
            $ret['imageHeight'] = $size['height'];
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

    private function _saveImageDimensions()
    {
        if (is_null($this->is_image)) {
            $size = null;
            if ($this->getFileSource() && is_file($this->getFileSource())) {
                $size = @getimagesize($this->getFileSource());
                $rotation = Kwf_Media_Image::getExifRotation($this->getFileSource());
            }
            if ($size) {
                $this->is_image = 1;
                $this->image_width = $size[0];
                $this->image_height = $size[1];
                $this->image_rotation = $rotation;
            } else {
                $this->is_image = 0;
            }
            $this->save();
        }
    }

    public function getImageDimensions()
    {
        $this->_saveImageDimensions();
        if ($this->is_image) {
            return array(
                'width' => $this->image_width,
                'height' => $this->image_height,
                'rotation' => $this->image_rotation,
            );
        }
        return null;
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
