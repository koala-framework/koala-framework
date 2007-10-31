<?php
class Vps_Media_Image
{
    const SCALE_BESTFIT = 'bestfit';
    const SCALE_CROP = 'crop';
    const SCALE_DEFORM = 'deform';
    const SCALE_ORIGINAL = 'original';

    public static function calculateScaleDimensions($source, $size, $scale = self::SCALE_BESTFIT)
    {
        $width  = !isset($size['width'])  && isset($size[0]) ? $size[0] : 0 ;
        $height = !isset($size['height']) && isset($size[0]) ? $size[0] : 0 ;

        if ($width == 0 && $height == 0) {
            return false;
        }

        if ($scale == self::SCALE_CROP){ // Bild wird auf allen 4 Seiten gleichmäßig beschnitten

            $size = getimagesize($source);
            if ($size[0] > $width) { // Wenn hochgeladenes Bild breiter als anzuzeigendes Bild ist
                $x = ($size[0] - $width) / 2; // Ursprungs-X berechnen
            } else {
                $x = 0; // Bei 0 mit Beschneiden beginnen
                $width = $size[0]; // Breite auf Originalgröße begrenzen
            }
            if ($size[1] > $height) {
                $y = ($size[1] - $height) / 2;
            } else {
                $y = 0;
                $height = $size[1];
            }
            return array('width'=>$width, 'height'=>$height, 'x'=>$x, 'y'=>$y);

        } elseif ($scale == self::SCALE_BESTFIT) { // Bild wird auf größte Maximale Ausdehnung skaliert

            $size = getimagesize($source);
            $widthRatio = $size[0] / $width;
            $heightRatio = $size[1] / $height;
            if ($widthRatio > $heightRatio){
                $width = $size[0] / $widthRatio;
                $height = $size[1] / $widthRatio;
            } else {
                $width = $size[0] / $heightRatio;
                $height = $size[1] / $heightRatio;
            }
            return array('width'=>$width, 'height'=>$height);

        } elseif ($scale == self::SCALE_DEFORM) {

            return array('width'=>$width, 'height'=>$height);

        } elseif ($scale == self::SCALE_ORIGINAL) {

            $size = getimagesize($source);
            return array('width'=>$size[0], 'height'=>$size[1]);

        } else {

            return false;

        }
    }

    public static function scale($source, $target, $size, $scale = self::SCALE_BESTFIT)
    {
        $size = self::calculateScaleDimensions($source, $size, $scale);
        if ($size === false) return false;

        $writeImage = true;

        $im = new Imagick();
        $im->readImage($source);

        if ($scale == self::SCALE_CROP){ // Bild wird auf allen 4 Seiten gleichmäßig beschnitten

            $im->cropImage($size['width'], $size['height'], $size['x'], $size['y']);

        } elseif ($scale == self::SCALE_BESTFIT) { // Bild wird auf größte Maximale Ausdehnung skaliert

            $im->thumbnailImage($size['width'], $size['height']);

        } elseif ($scale == self::SCALE_DEFORM){

            $im->thumbnailImage($size['width'], $size['height']);

        } elseif ($scale == self::SCALE_ORIGINAL){

            copy($source, $target);
            $writeImage = false;

        } else {

            return false;

        }

        if ($writeImage) {
            $im->writeImage($target);
        }
        $im->destroy();
        chmod($target, 0644);
        return true;
    }
}