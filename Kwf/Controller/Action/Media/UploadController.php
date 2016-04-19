<?php
class Kwf_Controller_Action_Media_UploadController extends Kwf_Controller_Action
{
    public function jsonUploadAction()
    {
        Kwf_Util_MemoryLimit::set(1024);
        $uploadModel = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model');

        $uploadedFile = array(
            'filename' => '',
            'extension' => ''
        );

        if (isset($_FILES['Filedata'])) {
            $file = $_FILES['Filedata'];
            if ($file['error']) {
                if ($file['error'] == UPLOAD_ERR_NO_FILE) {
                    throw new Kwf_Exception_Client(trlKwf("No File uploaded, please select a file."));
                } else if ($file['error'] == UPLOAD_ERR_PARTIAL) {
                    throw new Kwf_Exception_Client(trlKwf("The uploaded file was only partially uploaded."));
                } else if ($file['error'] == UPLOAD_ERR_INI_SIZE) {
                    throw new Kwf_Exception_Client(trlKwf("The uploaded file is too large."));
                } else {
                    throw new Kwf_Exception("Upload error $file[error]");
                }
            }
            if (!isset($file['tmp_name']) || !is_file($file['tmp_name'])) {
                throw new Kwf_Exception("No File found");
            }
            $imageSize = getimagesize($file['tmp_name']);
            if (isset($imageSize[0]) && $imageSize[0] > 10000) {
                throw new Kwf_Exception_Client(trlKwf("The uploaded image has too large pixel dimensions. Please upload an image with less than 10 000 pixels width."));
            }
            if (isset($imageSize[1]) && $imageSize[1] > 10000) {
                throw new Kwf_Exception_Client(trlKwf("The uploaded image has too large pixel dimensions. Please upload an image with less than 10 000 pixels height. "));
            }

            $maxResolution = (int)$this->_getParam('maxResolution');
            if ($this->_getParam('maxResolution')) {
                if (substr($imageSize['mime'], 0, 6) != 'image/') $maxResolution = 0;
            }
            $filename = substr($file['name'], 0, strrpos($file['name'], '.'));
            $extension = substr(strrchr($file['name'], '.'), 1);
            $uploadedFile['filename'] = $filename;
            $uploadedFile['extension'] = $extension;
            if ($maxResolution > 0) {
                $fileData = Kwf_Media_Image::scale($file['tmp_name'], array('width' => $maxResolution, 'height' => $maxResolution, 'cover' => false));
                Kwf_Uploads_Model::verifyUpload($file);
                $fileRow = $uploadModel->writeFile($fileData, $filename, $extension, $file['type']);
            } else {
                $fileRow = $uploadModel->uploadFile($file);
            }
        } else if (isset($_SERVER['HTTP_X_UPLOAD_NAME'])) {
            $fileData = file_get_contents("php://input");
            if (isset($_SERVER['CONTENT_LENGTH'])) {
                $contentLength = $_SERVER['CONTENT_LENGTH'];
            } else {
                $contentLength = $_SERVER['HTTP_CONTENT_LENGTH'];
            }
            if ($contentLength != $_SERVER['HTTP_X_UPLOAD_SIZE']) {
                throw new Kwf_Exception("Content-Length doesn't match X-Upload-Size");
            }
            if ($contentLength != strlen($fileData)) {
                throw new Kwf_Exception("Content-Length doesn't match uploaded data");
            }
            $name = rawurldecode($_SERVER['HTTP_X_UPLOAD_NAME']);
            $filename = substr($name, 0, strrpos($name, '.'));
            $extension = substr(strrchr($name, '.'), 1);
            $uploadedFile['filename'] = $filename;
            $uploadedFile['extension'] = $extension;
            $mimeType = null;
            if (isset($_SERVER['HTTP_X_UPLOAD_TYPE'])) {
                $mimeType = $_SERVER['HTTP_X_UPLOAD_TYPE'];
            }

            $tempFile = tempnam('temp', 'upload');
            file_put_contents($tempFile, $fileData);
            $imageSize = getimagesize($tempFile);
            if (isset($imageSize[0]) && $imageSize[0] > 10000) {
                throw new Kwf_Exception_Client(trlKwf("The uploaded image has too large pixel dimensions. Please upload an image with less than 10 000 pixels width."));
            }
            if (isset($imageSize[1]) && $imageSize[1] > 10000) {
                throw new Kwf_Exception_Client(trlKwf("The uploaded image has too large pixel dimensions. Please upload an image with less than 10 000 pixels height. "));
            }

            if (isset($_SERVER['HTTP_X_UPLOAD_MAXRESOLUTION']) && $_SERVER['HTTP_X_UPLOAD_MAXRESOLUTION'] > 0) {
                $maxResolution = $_SERVER['HTTP_X_UPLOAD_MAXRESOLUTION'];
                $fileData = Kwf_Media_Image::scale($tempFile, array('width' => $maxResolution, 'height' => $maxResolution, 'cover' => false));
                $fileRow = $uploadModel->writeFile($fileData, $filename, $extension, $mimeType);
            } else {
                $fileRow = $uploadModel->writeFile($fileData, $filename, $extension, $mimeType);
            }
            unlink($tempFile);
        } else {
            throw new Kwf_Exception_Client(trlKwf("No Filedata received."));
        }

        $this->view->value = $fileRow->getFileInfo();
        $this->view->value['uploaded_filename'] = $uploadedFile['filename'];
        $this->view->value['uploaded_extension'] = $uploadedFile['extension'];


    }

    public function previewAction()
    {
        if (!$this->_getParam('uploadId')) {
            throw new Kwf_Exception_NotFound();
        }
        $fileRow = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model')
            ->getRow($this->_getParam('uploadId'));
        if (!$fileRow) throw new Kwf_Exception_NotFound();

        if ($fileRow->getHashKey() != $this->_getParam('hashKey')) {
           throw new Kwf_Exception_AccessDenied();
        }

        $sizes = array(
            'default' => array(40, 40),
            'frontend' => array(100, 100, 'cover' => true,),
            'gridRow' => array(0, 20),
            'gridRowLarge' => array(200, 200, 'cover' => false,),
            'imageGrid' => array(140, 140, 'cover' => false),
            'imageGridLarge' => array(400, 400, 'cover' => false),
        );
        if (isset($sizes[$this->_getParam('size')])) {
            $size = $this->_getParam('size');
        } else {
            $size = 'default';
        }

        $cache = Kwf_Assets_Cache::getInstance();
        $cacheId = $size.'_'.$fileRow->id;
        if (!$output = $cache->load($cacheId)) {
            $output = array();
            $output['contents'] = Kwf_Media_Image::scale($fileRow->getFileSource(), $sizes[$size], $fileRow->id);
            $output['mimeType'] = $fileRow->mime_type;
            $cache->save($output, $cacheId);
        }
        Kwf_Media_Output::output($output);
    }


    public function downloadAction()
    {
        $fileRow = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model')
            ->getRow($this->_getParam('uploadId'));
        if (!$fileRow) throw new Kwf_Exception("Can't find upload");

        if ($fileRow->getHashKey() != $this->_getParam('hashKey')) {
            throw new Kwf_Exception_AccessDenied();
        }

        $source = $fileRow->getFileSource();
        Kwf_Media_Output::output(array(
            'file' => $source,
            'mimeType' => $fileRow->mime_type,
            'downloadFilename' => $fileRow->filename . '.' . $fileRow->extension
        ));
    }

    public function downloadHandyAction()
    {
        $fileRow = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model')
            ->getRow($this->_getParam('uploadId'));
        if (!$fileRow) throw new Kwf_Exception("Can't find upload");

        if ($fileRow->getHashKey() != $this->_getParam('hashKey')) {
            throw new Kwf_Exception_AccessDenied();
        }

        $scaleFactor = Kwf_Media_Image::getHandyScaleFactor($fileRow->getFileSource());
        $outputParams = array(
            'mimeType' => $fileRow->mime_type,
            'downloadFilename' => $fileRow->filename . '.' . $fileRow->extension
        );
        $targetSize = array(600, 600, 'cover' => false);
        $image = Kwf_Media_Image::scale($fileRow->getFileSource(), $targetSize, $fileRow->id);
        $outputParams['contents'] = $image;
        Kwf_Media_Output::output($outputParams);
    }
}
