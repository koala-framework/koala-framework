<?php
class Kwf_Util_Aws_Uploads_S3Row extends Kwf_Uploads_Row
{
    private function _getCacheFile()
    {
        return 'cache/uploads/'.$this->id;
    }

    private function _deleteCache()
    {
        $f = $this->_getCacheFile();
        if (file_exists($f)) {
            unlink($f);
        }
    }

    protected function _deleteFile()
    {
        throw new Kwf_Exception_NotYetImplemented();
        $this->_deleteCache();
    }

    protected function _putFileContents($contents)
    {
        $s3 = new Kwf_Util_Aws_S3();
        $r = $s3->create_object(
            Kwf_Config::getValue('aws.uploadsBucket'),
            $this->id,
            array(
                'body' => $contents,
                'length' => strlen($contents),
                'contentType' => $this->mime_type,
            )
        );
        if (!$r->isOk()) {
            throw new Kwf_Exception($r->body);
        }
        $this->_deleteCache();
    }

    public function getFileSource()
    {
        if (!$this->id) {
            return null;
        }
        $cacheFile = $this->_getCacheFile();
        if (!file_exists($cacheFile)) {
            $s3 = new Kwf_Util_Aws_S3();
            $r = $s3->get_object(
                Kwf_Config::getValue('aws.uploadsBucket'),
                $this->id,
                array(
                    'fileDownload' => $cacheFile,
                )
            );
            if (!$r->isOk()) {
                $body = file_get_contents($cacheFile);
                unlink($cacheFile);
                throw new Kwf_Exception($body);
            }
        }
        return $cacheFile;
    }
}

