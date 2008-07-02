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
        $height = !isset($size['height']) && isset($size[1]) ? $size[1] : 0 ;

        if ($width == 0 && $height == 0) {
            return false;
        }

        if (!is_file($source)) {
            return false;
        }

        if ($width == 0) {
            $size = getimagesize($source);
            $width = round($height * ($size[0]/$size[1]));
            return array('width'=>$width, 'height'=>$height);
        } else if ($height == 0) {
            $size = getimagesize($source);
            $height = round($width * ($size[1]/$size[0]));
            return array('width'=>$width, 'height'=>$height);
        } else if ($scale == self::SCALE_CROP) {
            // Bild wird auf allen 4 Seiten gleichmäßig beschnitten

            if (!$width || !$height) {
                throw new Vps_Exception("width and height must be set higher than 0 "
                    ."if Vps_Media_Image::SCALE_CROP is used. Maybe "
                    ."Vps_Media_Image::SCALE_BESTFIT would be better?");
            }

            $size = getimagesize($source);

            if (($width / $height) >= ($size[0] / $size[1])) {
                $resizeWidth  = $width;
                $resizeHeight = 0;
                $cropFromWidth  = $resizeWidth;
                $cropFromHeight = $size[1] * ($width / $size[0]);
            } else {
                $resizeWidth  = 0;
                $resizeHeight = $height;
                $cropFromWidth  = $size[0] * ($height / $size[1]);
                $cropFromHeight = $resizeHeight;
            }

            if ($cropFromWidth > $width) { // Wenn hochgeladenes Bild breiter als anzuzeigendes Bild ist
                $x = ($cropFromWidth - $width) / 2; // Ursprungs-X berechnen
            } else {
                $x = 0; // Bei 0 mit Beschneiden beginnen
                $width = $cropFromWidth; // Breite auf Originalgröße begrenzen
            }
            if ($cropFromHeight > $height) {
                $y = ($cropFromHeight - $height) / 2;
            } else {
                $y = 0;
                $height = $cropFromHeight;
            }
            return array('width'        => round($width),
                         'height'       => round($height),
                         'x'            => round($x),
                         'y'            => round($y),
                         'resizeWidth'  => $resizeWidth,
                         'resizeHeight' => $resizeHeight
            );

        } elseif ($scale == self::SCALE_BESTFIT) {
            // Bild wird auf größte Maximale Ausdehnung skaliert
            // Bild wird NICHT vergrößert! (kann also auch kleiner ausgegeben werden als angefordert)

            $size = getimagesize($source);
            if (!$size) return array();

            // 2 if abfragen um zu verhindern, dass das bild vergrößert wird
            if ($size[0] < $width) {
                $height = $height / ($width / $size[0]);
                $width = $size[0];
            }
            if ($size[1] < $height) {
                $width = $width / ($height / $size[1]);
                $height = $size[1];
            }

            $widthRatio = $size[0] / $width;
            $heightRatio = $size[1] / $height;

            if ($widthRatio > $heightRatio) {
                $width = $size[0] / $widthRatio;
                $height = $size[1] / $widthRatio;
            } else {
                $width = $size[0] / $heightRatio;
                $height = $size[1] / $heightRatio;
            }
            return array('width'=>round($width), 'height'=>round($height));

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
        if (!$scale) { $scale = self::SCALE_BESTFIT; }
        $size = self::calculateScaleDimensions($source, $size, $scale);

        if ($size === false) return false;

        if ($scale == self::SCALE_CROP) {
            // Bild wird auf allen 4 Seiten gleichmäßig beschnitten
            if (class_exists('Imagick')) {
                $im = new Imagick();
                $im->readImage($source);
                $im->scaleImage($size['resizeWidth'], $size['resizeHeight']);
                $im->cropImage($size['width'], $size['height'], $size['x'], $size['y']);
                $im->setImagePage(0, 0, 0, 0);
    //             $im->unsharpMaskImage(1, 0.5, 1.0, 0.05);
                $im->writeImage($target);
                $im->destroy();
            }

        } elseif ($scale == self::SCALE_BESTFIT || $scale == self::SCALE_DEFORM) {
            if (class_exists('Imagick')) {
                $im = new Imagick();
                $im->readImage($source);
                $im->thumbnailImage($size['width'], $size['height']);
                $im->writeImage($target);
                $im->destroy();
            } else {
                $srcSize = getimagesize($source);
                if ($srcSize[2] == 1) {
                    $source = imagecreatefromgif($source);
                } elseif ($srcSize[2] == 2) {
                    $source = imagecreatefromjpeg($source);
                } elseif ($srcSize[2] == 3) {
                    $source = imagecreatefrompng($source);
                }
                $destination = imagecreatetruecolor($size['width'], $size['height']);
                imagecopyresampled($destination, $source, 0, 0, 0, 0,
                                    $size['width'], $size['height'],
                                    $srcSize[0], $srcSize[1]);
                if ($srcSize[2] == 1) {
                    $source = imagegif($destination, $target);
                } elseif ($srcSize[2] == 2) {
                    $source = imagejpeg($destination, $target);
                } elseif ($srcSize[2] == 3) {
                    $source = imagepng($destination, $target);
                }
            }

        } elseif ($scale == self::SCALE_ORIGINAL) {

            copy($source, $target);

        } else {

            return false;

        }

        chmod($target, 0644);
        return true;
    }
}
