<?php
class Vps_Media_Image
{
    const SCALE_BESTFIT = 'bestfit';
    const SCALE_CROP = 'crop';
    const SCALE_DEFORM = 'deform';

    public static function scale($source, $target, $size, $style = self::SCALE_BESTFIT)
    {
        $width  = !isset($size['width'])  && isset($size[0]) ? $size[0] : 0 ;
        $height = !isset($size['height']) && isset($size[0]) ? $size[0] : 0 ;

        if ($width == 0 && $height == 0) {
            return false;
        }

        if ($style == '') {
            $style = self::SCALE_BESTFIT;
        }

        $im = new Imagick();
        $im->readImage($source);

        if ($style == self::SCALE_CROP){ // Bild wird auf allen 4 Seiten gleichmäßig beschnitten

            $size = $im->getImageGeometry();
            if ($size['width'] > $width) { // Wenn hochgeladenes Bild breiter als anzuzeigendes Bild ist
                $x = ($size['width'] - $width) / 2; // Ursprungs-X berechnen
            } else {
                $x = 0; // Bei 0 mit Beschneiden beginnen
                $width = $size['width']; // Breite auf Originalgröße begrenzen
            }
            if ($size['height'] > $height) {
                $y = ($size['height'] - $height) / 2;
            } else {
                $y = 0;
                $height = $size['height'];
            }
            $im->cropImage($width, $height, $x, $y);

        } elseif ($style == self::SCALE_BESTFIT) { // Bild wird auf größte Maximale Ausdehnung skaliert

            $size = $im->getImageGeometry();
            $widthRatio = $size['width'] / $width;
            $heightRatio = $size['height'] / $height;
            if ($widthRatio > $heightRatio){
                $width = $size['width'] / $widthRatio;
                $height = $size['height'] / $widthRatio;
            } else {
                $width = $size['width'] / $heightRatio;
                $height = $size['height'] / $heightRatio;
            }
            $im->thumbnailImage($width, $height);

        } elseif ($style == self::SCALE_DEFORM){

            $im->thumbnailImage($width, $height);

        }

        $im->writeImage($target);
        $im->destroy();
        chmod($target, 0644);
        return true;
    }
}