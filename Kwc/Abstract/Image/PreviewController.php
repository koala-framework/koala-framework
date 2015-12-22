<?php
class Kwc_Abstract_Image_PreviewController extends Kwf_Controller_Action
{
    protected function _isAllowedComponent()
    {
        $user = Kwf_Registry::get('userModel')->getAuthedUser();
        if ($user) {
            return true;
        }
        return false;
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
        $cache = Kwf_Assets_Cache::getInstance();
        $cacheId = 'previewLarge_'.$fileRow->id;
        if (!$output = $cache->load($cacheId)) {
            $output = array();
            $output['contents'] = Kwf_Media_Image::scale($fileRow->getFileSource(), $dimensions, $fileRow->id);
            $output['mimeType'] = $fileRow->mime_type;
            $cache->save($output, $cacheId);
        }

        $cropX = $this->_getParam('cropX');
        $cropY = $this->_getParam('cropY');
        $cropWidth = $this->_getParam('cropWidth');
        $cropHeight = $this->_getParam('cropHeight');

        $imageOriginal = new Imagick($fileRow->getFileSource());
        if ($this->_getParam('cropX') == null || $this->_getParam('cropY') == null
            || $this->_getParam('cropWidth') == null || $this->_getParam('cropHeight') == null
       ) { //calculate default selection
            $dimension = $this->_getParam('dimension');
            if (!$dimension) Kwf_Media_Output::output($output);
            $dimensions = Kwc_Abstract::getSetting($this->_getParam('class'), 'dimensions');
            $dimension = $dimensions[$dimension];

            if (!isset($dimension['width'])) $dimension['width'] = 0;
            if (!isset($dimension['height'])) $dimension['height'] = 0;
            if (!isset($dimension['cover'])) $dimension['cover'] = false;

            if ($dimension['width'] == Kwc_Abstract_Image_Component::USER_SELECT)
                $dimension['width'] = $this->_getParam('width');
            if ($dimension['height'] == Kwc_Abstract_Image_Component::USER_SELECT)
                $dimension['height'] = $this->_getParam('height');
            if (!$dimension['cover']) Kwf_Media_Output::output($output);
            if ($dimension['width'] == Kwc_Abstract_Image_Component::CONTENT_WIDTH)
                Kwf_Media_Output::output($output);
            if ($dimension['height'] == 0 || $dimension['width'] == 0)
                Kwf_Media_Output::output($output);

            $cropX = 0;
            $cropY = 0;
            $cropHeight = $imageOriginal->getImageHeight();
            $cropWidth = $imageOriginal->getImageWidth();
            if ($imageOriginal->getImageHeight() / $dimension['height']
                > $imageOriginal->getImageWidth() / $dimension['width']
            ) {// orientate on width
                $cropHeight = $dimension['height'] * $imageOriginal->getImageWidth() / $dimension['width'];
                $cropY = ($imageOriginal->getImageHeight() - $cropHeight) /2;
            } else {// orientate on height
                $cropWidth = $dimension['width'] * $imageOriginal->getImageHeight() / $dimension['height'];
                $cropX = ($imageOriginal->getImageWidth() - $cropWidth) /2;
            }
        }

        // Calculate values relative to preview image
        $image = new Imagick();
        $image->readImageBlob($output['contents']);
        $previewFactor = 1;
        if ($image->getImageWidth() == $previewWidth) {
            $previewFactor = $image->getImageWidth() / $imageOriginal->getImageWidth();
        } else if ($image->getImageHeight() == $previewHeight) {
            $previewFactor = $image->getImageHeight() / $imageOriginal->getImageHeight();
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
        for($x = 0; $x < $image->getImageWidth(); $x++) {
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
}
