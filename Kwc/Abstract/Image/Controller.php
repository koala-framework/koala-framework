<?php
class Kwc_Abstract_Image_Controller extends Kwf_Controller_Action_Auto_Kwc_Form
{
    public function previewWithCropAction()
    {
        $fileRow = Kwf_Model_Abstract::getInstance('Kwf_Uploads_Model')
            ->getRow($this->_getParam('uploadId'));
        if (!$fileRow) throw new Kwf_Exception("Can't find upload");

        if ($fileRow->getHashKey() != $this->_getParam('hashKey')) {
           throw new Kwf_Exception_AccessDenied();
        }

        $dimensions = array(100, 100, 'cover' => false);
        $size = 'previewLarge';

        static $cache = null;
        if (!$cache) $cache = new Kwf_Assets_Cache(array('checkComponentSettings'=>false));
        $cacheId = $size.'_'.$fileRow->id;
        if (!$output = $cache->load($cacheId)) {
            $output = array();
            $output['contents'] = Kwf_Media_Image::scale($fileRow->getFileSource(), $dimensions);
            $output['mimeType'] = $fileRow->mime_type;
            $cache->save($output, $cacheId);
        }

//         $isLightImage = $this->_isLightImage($output);

//         $cropX = $this->_getParam('cropX');
//         $cropY = $this->_getParam('cropY');
//         $cropWidth = $this->_getParam('cropWidth');
//         $cropHeight = $this->_getParam('cropHeight');

//         $draw = new ImagickDraw();
//         $draw->setFillColor('black');
//         $draw->rectangle(0, 0, $image->getImageWidth(), $cropX);
//         $draw->rectangle(0, $cropY, $cropX, $cropHeight);
//         $draw->rectangle($cropX+$cropWidth, $cropY, $image->getImageWidth() - ($cropX + $cropWidth), $cropHeight);
//         $draw->rectangle(0, $cropY + $cropHeight, $image->getImageWidth(), $image->getImageHeight()-($cropY + $cropHeight));

//         $image = new Imagick();
//         $image->readImage($output['contents']);
//         $image->drawImage($draw);

//         $output['contents'] = $image;

        Kwf_Media_Output::output($output);
    }

//     private function _isLightImage($imageData)
//     {
//         $image = new Imagick();
//         $image->readImage($imageData['contents']);
//         $max = $image->getQuantumRange();
//         $max = $max["quantumRangeLong"];
//         $image->setImageType(Imagick::IMGTYPE_GRAYSCALEMATTE);
//         $float = "0.".$percent;
//         $float = floatval($float);
//         $image->thresholdImage($float * $max, 255);
//         $image->setBackgroundColor('white');
//         $black = 0;
//         $white = 0;
//         for($x = 0; $x < $image->getImageWidth(); $x++) {
//             for ($y = 0; $y < $image->getImageHeight(); $y++) {
//                 $pixel = $image->getImagePixelColor($x, $y);
//                 if ($pixel->getColorValue(Imagick::COLOR_BLACK)) {
//                     $black++;
//                 } else {
//                     $white++;
//                 }
//             }
//         }
//         return $white > $black;
//     }
}
