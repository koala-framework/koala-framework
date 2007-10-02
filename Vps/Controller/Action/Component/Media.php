<?php
class Vps_Controller_Action_Component_Media extends Vps_Controller_Action
{
    public function originalAction()
    {
        $uploadId = $this->_getParam('uploadId');
        $config = Zend_Registry::get('config');
        $uploadDir = $config->uploads;

        $target = $uploadDir . $this->_getSourcePath($uploadId);
        $this->_showFile($target);
    }

    public function indexAction()
    {
        $uploadId = $this->_getParam('uploadId');
        $id = $this->_getParam('componentId');
        $filename = $this->_getParam('filename');
        $config = Zend_Registry::get('config');
        $uploadDir = $config->uploads;

        $checksum = md5('l4Gx8SFe' . $id);
        $downloadChecksum = md5('k4Xjgw9f' . $id);
        if ($checksum != $this->_getParam('checksum') && $downloadChecksum != $this->_getParam('checksum')) {
            throw new Vps_Controller_Action_Web_Exception('File not found.');
        }

        $target = '';
        if (strpos($filename, '.original.') || $checksum == $downloadChecksum) {
            $target = $uploadDir . $this->_getSourcePath($uploadId);
        } else {
            $extra = strpos($filename, '.thumb.') ? '.thumb' : '' ;
            $extension = strrchr($filename, '.');
            $target = $uploadDir . 'cache/' . $uploadId . '/' . $id . $extra . $extension;
            if (!is_file($target)) {
                $source = $uploadDir . $this->_getSourcePath($uploadId);
                $pageCollection = Vps_PageCollection_TreeBase::getInstance();
                $component = $pageCollection->findComponent($id);

                if ($component instanceof Vpc_FileInterface) {
                    if (!is_dir($uploadDir . 'cache/')) {
                        mkdir($uploadDir . 'cache/', 0775);
                    }
                    if (!is_dir(dirname($target))) {
                        mkdir(dirname($target), 0775);
                    }
                    try {
                        $component->createCacheFile($source, $target);
                    } catch (Exception $e) {
                        throw new Vps_Controller_Action_Web_Exception($e->getMessage()); // immer 404 auswerfen
                    }
                } else {
                    $target = $source;
                }

            }
        }
        $this->_showFile($target);
    }

    private function _getSourcePath($uploadId = 0)
    {
        $table = new Vps_Dao_File();
        $row = $table->find($uploadId)->current();
        if ($row) {
            return $row->path;
        }
        return '';
    }

    private function _showFile($target)
    {
        if (is_file($target)) {
            $extension = substr(strrchr($target, '.'), 1);
            switch ($extension) {
                case "pdf": $ctype="application/pdf"; break;
                case "zip": $ctype="application/zip"; break;
                case "doc": $ctype="application/msword"; break;
                case "xls": $ctype="application/vnd.ms-excel"; break;
                case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                case "gif": $ctype="image/gif"; break;
                case "png": $ctype="image/png"; break;
                case "jpeg": case "jpg": $ctype="image/jpg"; break;
                case "mp3": $ctype="audio/mpeg"; break;
                case "wav": $ctype="audio/x-wav"; break;
                case "mpeg": case "mpg": case "mpe": $ctype="video/mpeg"; break;
                case "mov": $ctype="video/quicktime"; break;
                case "avi": $ctype="video/x-msvideo"; break;
                default: $ctype="application/octet-stream"; break;
            }
            Zend_Controller_Action_HelperBroker::removeHelper('ViewRenderer');
            chmod($target, 0664);
            $response = $this->getResponse();
            $response->setHeader("Content-type", $ctype);
            $response->setBody(file_get_contents($target));
        } else {
            throw new Vps_Controller_Action_Web_Exception('File not found.');
        }
    }

}