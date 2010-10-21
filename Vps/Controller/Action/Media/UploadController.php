<?php
class Vps_Controller_Action_Media_UploadController extends Vps_Controller_Action
{
    public function jsonUploadAction()
    {
        if (!isset($_FILES['Filedata'])) {
            throw new Vps_Exception_Client(trlVps("No Filedata received."));
        }
        $file = $_FILES['Filedata'];
        if ($file['error']) {
            if ($file['error'] == UPLOAD_ERR_NO_FILE) {
                throw new Vps_Exception_Client(trlVps("No File uploaded, please select a file."));
            } else if ($file['error'] == UPLOAD_ERR_PARTIAL) {
                throw new Vps_Exception_Client(trlVps("The uploaded file was only partially uploaded."));
            } else {
                throw new Vps_Exception("Upload error $file[error]");
            }
        }
        if (!isset($file['tmp_name']) || !is_file($file['tmp_name'])) {
            throw new Vps_Exception("No File found");
        }
        $fileRow = Vps_Model_Abstract::getInstance('Vps_Uploads_Model')
            ->createRow();

        $maxResolution = (int)$this->_getParam('maxResolution');
        if ($this->_getParam('maxResolution')) {
            $image = getimagesize($file['tmp_name']);
            if (substr($image['mime'], 0, 6) != 'image/') $maxResolution = 0;
        }
        if ($maxResolution > 0) {
            $fileData = Vps_Media_Image::scale($file['tmp_name'], array('width' => $maxResolution, 'height' => $maxResolution, 'scale' => Vps_Media_Image::SCALE_BESTFIT));
            $filename = substr($file['name'], 0, strrpos($file['name'], '.'));
            $extension = substr(strrchr($file['name'], '.'), 1);
            $fileRow->verifyUpload($file);
            $fileRow->writeFile($fileData, $filename, $extension, $file['type']);
        } else {
            $fileRow->uploadFile($file);
        }

        $this->view->value = $fileRow->getFileInfo();

    }

    public function previewAction()
    {
        if (md5($this->_getParam('uploadId').Vps_Uploads_Row::HASH_KEY) != $this->_getParam('hashKey')) {
           throw new Vps_Exception_AccessDenied();
        }
        $fileRow = Vps_Model_Abstract::getInstance('Vps_Uploads_Model')
            ->getRow($this->_getParam('uploadId'));


        if (!$fileRow) throw new Vps_Exception("Can't find upload");

        $sizes = array(
            'default' => array(40, 40),
            'frontend' => array(100, 100, Vps_Media_Image::SCALE_CROP),
            'gridRow' => array(0, 20),
            'gridRowLarge' => array(200, 200, Vps_Media_Image::SCALE_BESTFIT),
            'imageGrid' => array(100, 100, Vps_Media_Image::SCALE_BESTFIT),
        );
        if (isset($sizes[$this->_getParam('size')])) {
            $size = $this->_getParam('size');
        } else {
            $size = 'default';
        }

        static $cache = null;
        if (!$cache) $cache = new Vps_Assets_Cache(array('checkComponentSettings'=>false));
        $cacheId = $size.'_'.$fileRow->id;
        if (!$output = $cache->load($cacheId)) {
            $output = array();
            $output['contents'] = Vps_Media_Image::scale($fileRow->getFileSource(), $sizes[$size]);
            $output['mimeType'] = $fileRow->mime_type;
            $cache->save($output, $cacheId);
        }
        Vps_Media_Output::output($output);
    }


    public function downloadAction()
    {
        if (md5($this->_getParam('uploadId').Vps_Uploads_Row::HASH_KEY) != $this->_getParam('hashKey')) {
            throw new Vps_Exception_AccessDenied();
        }
        $fileRow = Vps_Model_Abstract::getInstance('Vps_Uploads_Model')
            ->getRow($this->_getParam('uploadId'));
        if (!$fileRow) throw new Vps_Exception("Can't find upload");

        $source = $fileRow->getFileSource();
        Vps_Media_Output::output(array(
            'file' => $source,
            'mimeType' => $fileRow->mime_type,
            'downloadFilename' => $fileRow->filename . '.' . $fileRow->extension
        ));
    }
}
