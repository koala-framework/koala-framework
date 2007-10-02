<?php
class Vps_Media_Image
{
    public static function scale($source, $target, $style='bestfit')
    {
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