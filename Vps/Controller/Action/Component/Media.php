<?php
class Vps_Controller_Action_Component_Media extends Vps_Controller_Action
{
    public function indexAction()
    {
        $id = $this->_getParam('componentId');
        $config = Zend_Registry::get('config');
        $uploadDir = $config->uploads;

        $checksum = md5('l4Gx8SFe' . $id);
        if ($checksum != $this->_getParam('checksum')) {
            $response = new Zend_Http_Response(404, array('POST' => 'HTTP/1.0 404 Not Found'));
            echo $response->getStatus() . ": " . $response->getMessage();
        }
        
        if (strpos($this->_getParam('filename'), '.original.')) {
            $pageCollection = Vps_PageCollection_TreeBase::getInstance();
            $component = $pageCollection->findComponent($id);
            $table = new Vps_Dao_File();
            $row = $table->find($component->getSetting('vps_upload_id'))->current();
            if ($row) {
                $filename = $uploadDir . $row->path;
            }
        } else {
            $isThumb = strpos($this->_getParam('filename'), '.thumb.');
            $id = $this->_getParam('componentId');
            if ($isThumb) { $id .= '.thumb'; }
            $extension = strrchr($this->_getParam('filename'), '.');
            $filename = $uploadDir . $id . $extension;
            if (!is_file($filename)) {
                $pageCollection = Vps_PageCollection_TreeBase::getInstance();
                $component = $pageCollection->findComponent($this->_getParam('componentId'));
    
                $table = new Vps_Dao_File();
                $row = $table->find($component->getSetting('vps_upload_id'))->current();
                if ($row) {
                    if ($isThumb) {
                        $component->setSetting('width', 100);
                        $component->setSetting('height', 100);
                    }
                    $source = $uploadDir . $component->getSetting('uploadDir') . $row->path;
                    $this->copyImage($source, $filename, $component);
                }
            }
        }
        
        $extension = substr(strrchr($this->_getParam('filename'), '.'), 1);
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
        header("Content-type: $ctype");
        readfile($filename);
        die();
    }

    private function copyImage($source, $target, $component)
    {
        $width = $component->getSetting('width');
        $height = $component->getSetting('height');
        $style = $component->getSetting('style');
        if ($width <= 0) { $width = 100; }
        if ($height <= 0) { $height = 100; }
        if ($style == '') { $style = $component->getSetting('default_style'); }

        $im = new Imagick();
        $im->readImage($source);
        if ($style == 'crop'){ // Bild wird auf allen 4 Seiten gleichmäßig beschnitten
            
            $scale = $im->getImageGeometry();
            if ($scale['width'] > $width) { // Wenn hochgeladenes Bild breiter als anzuzeigendes Bild ist
                $x = ($scale['width'] - $width) / 2; // Ursprungs-X berechnen
            } else {
                $x = 0; // Bei 0 mit Beschneiden beginnen
                $width = $scale['width']; // Breite auf Originalgröße begrenzen
            }
            if ($scale['height'] > $height) {
                $y = ($scale['height'] - $height) / 2;
            } else {
                $y = 0;
                $height = $scale['height'];
            }
            $im->cropImage($width, $height, $x, $y);
          
        } elseif ($style == 'scale') { // Bild wird auf größte Maximale Ausdehnung skaliert
            
            $scale = $im->getImageGeometry();
            $widthRatio = $scale['width'] / $width;
            $heightRatio = $scale['height'] / $height;
            if ($widthRatio > $heightRatio){
                $width = $scale['width'] / $widthRatio;
                $height = $scale['height'] / $widthRatio;
            } else {
                $width = $scale['width'] / $heightRatio;
                $height = $scale['height'] / $heightRatio;
            }
            $im->thumbnailImage($width, $height);
          
        } elseif ($style == 'scale_bg'){
          
            $color = $component->getSetting('color');
            if ($color == '') { $color = $component->getSetting('default_color'); }
            
            $Imagick = new Imagick();
            $ImagickPixel = new ImagickPixel();

            $ImagickPixel->setColor( $color );

            $Imagick->newImage($width, $height, $ImagickPixel);
            $Imagick->setImageFormat($im->getImageFormat());

            $scale = $im ->getImageGeometry();
            $widthRatio = $scale['width'] / $width;
            $heightRatio = $scale['height'] / $height;
            if ($widthRatio > $heightRatio){
                $height1 = $height;
                $width = $scale['width'] / $widthRatio;
                $height = $scale['height'] / $widthRatio;
                $x = 0;
                $y = ($height1 - $height) / 2;
            } else {
                $width1 = $width;
                $width = $scale['width'] / $heightRatio;
                $height = $scale['height'] / $heightRatio;
                $x = ($width1 - $width) / 2;
                $y = 0;
            }
            $im->thumbnailImage($width, $height);
            $Imagick->compositeImage($im, $im->getImageCompose(), $x, $y);
            $im = $Imagick;
        
        } elseif ($style == 'deform'){
            
            $im->thumbnailImage($width, $height);
          
        }
        
        $im->writeImage($target);
        $im->destroy();
    }
}