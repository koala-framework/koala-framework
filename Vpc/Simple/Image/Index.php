<?php
class Vpc_Simple_Image_Index extends Vpc_Abstract implements Vpc_FileInterface
{
    protected $_tablename = 'Vpc_Simple_Image_IndexModel';
    const NAME = 'Standard.Image';
    protected $_settings = array(
        'typesAllowed' 	    => 'jpg, gif, png',
        'directory'   	    => 'SimpleImage/',
        'size'              => array(), // Leeres Array -> freie Wahl, array(width, height), array(array(width, height), ...)
        'default_style'		=> 'crop',
        'allow'		        => array('crop', 'scale', 'bestfit') //keywords: crop, scale, bestfit
    );
    const SIZE_NORMAL = '';
    const SIZE_THUMB = '.thumb';
    const SIZE_ORIGINAL = '.original';
    
    
    public function getTemplateVars()
    {
        $return['url'] = $this->getImageUrl();
        $return['template'] = 'Simple/Image.html';
        return $return;
    }
    
    public function getImageUrl($size = self::SIZE_NORMAL)
    {
        $rowset = $this->_getTable()->find($this->getDbId(), $this->getComponentKey());
        if ($rowset->count() == 1) {
            $row = $rowset->current();
            $filename = $row->name != '' ? $row->name : 'unnamed';
            $filename .= $size;
            $id = $this->getId();
            $checksum = md5('l4Gx8SFe' . $id);
            $rowset2 = $this->_getTable('Vps_Dao_File')->find($row->vps_upload_id);
            if ($rowset2->count() == 1) {
                $extension = substr(strrchr($rowset2->current()->path, '.'), 1);
                $uploadId = $row->vps_upload_id;
                return "/media/$uploadId/$id/$checksum/$filename.$extension";
            } else {
                return null;
            }
        } else {
            return null;
        }
    }
    
    public function getExtensions()
    {
        $extensions = array();
        foreach (explode(',', $this->getSetting('typesAllowed')) as $extension) {
            $extensions[] = trim(strtolower($extension));
        }
        return $extensions;
    }
    
    public function createCacheFile($source, $target)
    {
        if (strpos($target, '.thumb.')) {
            $this->setSetting('width', 100);
            $this->setSetting('height', 100);
        }

        $width = $this->getSetting('width');
        $height = $this->getSetting('height');
        $style = $this->getSetting('style');
        if ($width <= 0) { $width = 100; }
        if ($height <= 0) { $height = 100; }
        if ($style == '') { $style = $this->getSetting('default_style'); }

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
          
        } elseif ($style == 'bestfit') { // Bild wird auf größte Maximale Ausdehnung skaliert
            
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
          
        } elseif ($style == 'deform'){
            
            $im->thumbnailImage($width, $height);
          
        }
        
        $im->writeImage($target);
        $im->destroy();
    }
}