<?php
class Kwf_Controller_Action_Media_UploadController extends Kwf_Controller_Action
{
    protected function _isAllowedResource()
    {
        if ($this->getRequest()->getActionName() == 'download') {
            return true;
        }
        return parent::_isAllowedResource();
    }

    protected function _validateCsrf()
    {
    }

    private function _isUploadAllowed($mimeType, $filename, $filesize)
    {
        $acl = Kwf_Acl::getInstance();

        foreach ($acl->getAllResources() as $resource) {
            if ($resource instanceof Kwf_Acl_Resource_MediaUpload && $acl->isAllowed(Kwf_Registry::get('userModel')->getAuthedUserRole(), $resource)) {
                $allowed = true;
                if ($resource->getMimeTypePattern() && !preg_match('#'.$resource->getMimeTypePattern().'#', $mimeType)) {
                    $allowed = false;
                }
                if ($resource->getFilenamePattern() && !preg_match('#'.$resource->getFilenamePattern().'#', $filename)) {
                    $allowed = false;
                }
                if ($resource->getMaxFilesize() && $filesize > $resource->getMaxFilesize()) {
                    $allowed = false;
                }
                if ($allowed) {
                    return true;
                }
            }
        }
        return false;
    }

    private function _isDownloadAllowed($mimeType, $filename, $filesize)
    {
        $acl = Kwf_Acl::getInstance();

        foreach ($acl->getAllResources() as $resource) {
            if ($resource instanceof Kwf_Acl_Resource_MediaDownload && $acl->isAllowed(Kwf_Registry::get('userModel')->getAuthedUserRole(), $resource)) {
                $allowed = true;
                if ($resource->getMimeTypePattern() && !preg_match('#'.$resource->getMimeTypePattern().'#', $mimeType)) {
                    $allowed = false;
                }
                if ($resource->getFilenamePattern() && !preg_match('#'.$resource->getFilenamePattern().'#', $filename)) {
                    $allowed = false;
                }
                if ($resource->getMaxFilesize() && $filesize > $resource->getMaxFilesize()) {
                    $allowed = false;
                }
                if ($allowed) {
                    return true;
                }
            }
        }
        return false;
    }

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
            if (!$this->_isUploadAllowed($file['type'], $file['name'], $file['size'])) {
                throw new Kwf_Exception_Client(trlKwf("Invalid file"));
            }
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
            if (!$this->_isUploadAllowed($mimeType, $name, strlen($fileData))) {
                throw new Kwf_Exception_Client(trlKwf("Invalid file"));
            }

            $tempFile = tempnam('temp', 'upload');
            file_put_contents($tempFile, $fileData);
            $imageSize = @getimagesize($tempFile);
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

        $cache = Kwf_Media_OutputCache::getInstance();
        $cacheId = $size.'_'.$fileRow->id;
        if (!$output = $cache->load($cacheId)) {
            $output = array();
            $output['contents'] = Kwf_Media_Image::scale($fileRow, $sizes[$size]);
            $output['mimeType'] = $fileRow->mime_type;
            $cache->save($output, $cacheId);
        }
        Kwf_Media_Output::output($output);
    }

    public function previewWithCropAction()
    {
        $previewWidth = 390;
        $previewHeight = 184;
        $fileRow = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model')
            ->getRow($this->_getParam('uploadId'));
        if (!$fileRow) throw new Kwf_Exception("Can't find upload");

        if ($fileRow->getHashKey() != $this->_getParam('hashKey')) {
            throw new Kwf_Exception_AccessDenied();
        }


        //Scale dimensions
        $dimensions = array($previewWidth, $previewHeight, 'cover' => false);
        $cache = Kwf_Media_OutputCache::getInstance();
        $cacheId = 'previewLarge_'.$fileRow->id;
        if (!$output = $cache->load($cacheId)) {
            $output = array();
            $output['contents'] = Kwf_Media_Image::scale($fileRow, $dimensions);
            $output['mimeType'] = $fileRow->mime_type;
            $cache->save($output, $cacheId);
        }

        $cropX = $this->_getParam('cropX');
        $cropY = $this->_getParam('cropY');
        $cropWidth = $this->_getParam('cropWidth');
        $cropHeight = $this->_getParam('cropHeight');

        $sourceSize = $fileRow->getImageDimensions();
        if ($this->_getParam('cropX') == null || $this->_getParam('cropY') == null
            || $this->_getParam('cropWidth') == null || $this->_getParam('cropHeight') == null
        ) { //calculate default selection
            $dimension = $this->_getParam('dimension');
            if (!$dimension) Kwf_Media_Output::output($output);
            $dimension = array(
                'width' => $this->_getParam('dimension_width') ? $this->_getParam('dimension_width') : 0,
                'height' => $this->_getParam('dimension_height') ? $this->_getParam('dimension_height') : 0,
                'cover' => $this->_getParam('dimension_cover') ? $this->_getParam('dimension_cover') : false
            );

            if ($dimension['width'] == Kwf_Form_Field_Image_UploadField::USER_SELECT)
                $dimension['width'] = $this->_getParam('width');
            if ($dimension['height'] == Kwf_Form_Field_Image_UploadField::USER_SELECT)
                $dimension['height'] = $this->_getParam('height');
            if (!$dimension['cover']) Kwf_Media_Output::output($output);
            if ($dimension['width'] == Kwf_Form_Field_Image_UploadField::CONTENT_WIDTH)
                Kwf_Media_Output::output($output);
            if ($dimension['height'] == 0 || $dimension['width'] == 0)
                Kwf_Media_Output::output($output);

            $cropX = 0;
            $cropY = 0;
            $cropHeight = $sourceSize['height'];
            $cropWidth = $sourceSize['width'];
            if ($sourceSize['height'] / $dimension['height']
                > $sourceSize['width'] / $dimension['width']
            ) {// orientate on width
                $cropHeight = $dimension['height'] * $sourceSize['width'] / $dimension['width'];
                $cropY = ($sourceSize['height'] - $cropHeight) /2;
            } else {// orientate on height
                $cropWidth = $dimension['width'] * $sourceSize['height'] / $dimension['height'];
                $cropX = ($sourceSize['width'] - $cropWidth) /2;
            }
        }

        // Calculate values relative to preview image
        $image = new Imagick();
        $image->readImageBlob($output['contents']);
        $previewFactor = 1;
        if ($image->getImageWidth() == $previewWidth) {
            $previewFactor = $image->getImageWidth() / $sourceSize['width'];
        } else if ($image->getImageHeight() == $previewHeight) {
            $previewFactor = $image->getImageHeight() / $sourceSize['height'];
        }
        $cropX = floor($cropX * $previewFactor);
        $cropY = floor($cropY * $previewFactor);
        $cropWidth = floor($cropWidth * $previewFactor);
        $cropHeight = floor($cropHeight * $previewFactor);

        $draw = new ImagickDraw();
        if ($this->_isLightImage($output)) {
            $draw->setFillColor('black');
        } else {
            $draw->setFillColor('white');
        }
        $draw->setFillOpacity(0.3);

        // if cropX == 0 or cropY == 0 no rectangle should be drawn, because it
        // can't be 0 wide on topmost and leftmost position so it will result in
        // a 1px line which is wrong
        //Top region
        if ($cropY > 0) {
            $draw->rectangle(0, 0, $image->getImageWidth(), $cropY);
        }
        //Left region
        if ($cropX > 0) {
            if ($cropY > 0) {
                $draw->rectangle(0, $cropY+1, $cropX, $cropY + $cropHeight-1);
            } else {
                $draw->rectangle(0, $cropY, $cropX, $cropY + $cropHeight-1);
            }
        }
        //Right region
        if ($cropY > 0) {
            $draw->rectangle($cropX+$cropWidth, $cropY+1, $image->getImageWidth(), $cropY + $cropHeight-1);
        } else {
            $draw->rectangle($cropX+$cropWidth, $cropY, $image->getImageWidth(), $cropY + $cropHeight-1);
        }
        //Bottom region
        $draw->rectangle(0, $cropY + $cropHeight, $image->getImageWidth(), $image->getImageHeight());

        $image->drawImage($draw);

        $output['contents'] = $image->getImageBlob();

        Kwf_Media_Output::output($output);
    }

    private function _isLightImage($imageData)
    {
        $image = new Imagick();
        $image->readImageBlob($imageData['contents']);
        $max = $image->getQuantumRange();
        $max = $max["quantumRangeLong"];
        $image->setImageType(Imagick::IMGTYPE_GRAYSCALEMATTE);
        $float = 0.5;
        $image->thresholdImage($float * $max, 255);
        $image->setBackgroundColor('white');
        $black = 0;
        $white = 0;
        for ($x = 0; $x < $image->getImageWidth(); $x++) {
            for ($y = 0; $y < $image->getImageHeight(); $y++) {
                $pixel = $image->getImagePixelColor($x, $y);
                $value = $pixel->getColor();
                if ($value['r'] == 0 && $value['g'] == 0 && $value['b'] == 0) {
                    $black++;
                } else {
                    $white++;
                }
            }
        }
        return $white > $black;
    }

    public function downloadAction()
    {
        $fileRow = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model')
            ->getRow($this->_getParam('uploadId'));
        if (!$fileRow) throw new Kwf_Exception("Can't find upload");

        if (!$this->_isDownloadAllowed($fileRow->mime_type, $fileRow->filename . '.' . $fileRow->extension, $fileRow->getFileSize())) {
            throw new Kwf_Exception_Client(trlKwf("Download not allowed."));
        }

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

        $outputParams = array(
            'mimeType' => $fileRow->mime_type,
            'downloadFilename' => $fileRow->filename . '.' . $fileRow->extension
        );
        $targetSize = array(600, 600, 'cover' => false);
        $image = Kwf_Media_Image::scale($fileRow, $targetSize);
        $outputParams['contents'] = $image;
        Kwf_Media_Output::output($outputParams);
    }
}
