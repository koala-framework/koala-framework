<?php
class Vps_Media_Image
{
    const SCALE_BESTFIT = 'bestfit';
    const SCALE_CROP = 'crop';
    const SCALE_DEFORM = 'deform';
    const SCALE_ORIGINAL = 'original';

    public static function calculateScaleDimensions($source, $targetSize)
    {
        if (is_string($source)) {
            $sourceSize = @getimagesize($source);
        } else {
            $sourceSize = $source;
            $source = null;
        }

        if (!$sourceSize) return false;
        $w = null;
        if (isset($sourceSize['width'])) $w = $sourceSize['width'];
        if (isset($sourceSize[0])) $w = $sourceSize[0];
        $h = null;
        if (isset($sourceSize['height'])) $h = $sourceSize['height'];
        if (isset($sourceSize[1])) $h = $sourceSize[1];
        $size = array($w, $h);
        $rotate = null;
        if ($source && function_exists('exif_read_data') &&
            isset($sourceSize['mime']) && $sourceSize['mime'] == 'image/jpg'
        ) {
            $exif = exif_read_data($source);
            if (isset($exif['Orientation'])) {
                switch ($exif['Orientation']) {
                    case 6:
                        $size = array($h, $w);
                        $rotate = 90;
                    case 8:
                        $size = array($h, $w);
                        $rotate = -90;
                }
            }
        }
        if (!$size[0] || !$size[1]) return false;

        if (isset($targetSize['width'])) $width = $targetSize['width'];
        else if (isset($targetSize[0])) $width = $targetSize[0];
        else $width = 0;

        if (isset($targetSize['height'])) $height = $targetSize['height'];
        else if (isset($targetSize[1])) $height = $targetSize[1];
        else $height = 0;

        if (isset($targetSize['scale'])) $scale = $targetSize['scale'];
        else if (isset($targetSize[2])) $scale = $targetSize[2];
        else $scale = self::SCALE_BESTFIT;
        if (!$scale) $scale = self::SCALE_BESTFIT;

        if ($width == 0 && $height == 0 && $scale != self::SCALE_ORIGINAL) {
            return false;
        }

        if ($scale != self::SCALE_ORIGINAL) {
            if ($width == 0) {
                $width = round($height * ($size[0]/$size[1]));
                if ($width <= 0) $width = 1;
            }
            if ($height == 0) {
                $height = round($width * ($size[1]/$size[0]));
                if ($height <= 0) $height = 1;
            }
        }

        if ($scale == self::SCALE_CROP) {
            // Bild wird auf allen 4 Seiten gleichmäßig beschnitten

            if (!$width || !$height) {
                throw new Vps_Exception("width and height must be set higher than 0 "
                    ."if Vps_Media_Image::SCALE_CROP is used. Maybe "
                    ."Vps_Media_Image::SCALE_BESTFIT would be better?");
            }

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
                         'resizeHeight' => $resizeHeight,
                         'scale'        => $scale,
                         'rotate'       => $rotate
            );

        } elseif ($scale == self::SCALE_BESTFIT) {

            // Bild wird auf größte Maximale Ausdehnung skaliert
            // Bild wird NICHT vergrößert! (kann also auch kleiner ausgegeben werden als angefordert)

            // $width / $height => target size
            // $size => original size

            // 3 if abfragen um zu verhindern, dass das bild vergrößert wird
            if ($size[0] <= $width && $size[1] <= $height) {
                $width = $size[0];
                $height = $size[1];
            } else {
                if ($size[0] < $width) {
                    $width = $size[0];
                }
                if ($size[1] < $height) {
                    $height = $size[1];
                }
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

        } elseif ($scale == self::SCALE_DEFORM) {

            //width und height sind schon korrekt gesetzt

        } elseif ($scale == self::SCALE_ORIGINAL) {

            $width = $size[0];
            $height = $size[1];

        } else {
            return false;
        }
        $width = round($width);
        $height = round($height);
        if ($width <= 0) $width = 1;
        if ($height <= 0) $height = 1;
        return array(
            'width' => $width,
            'height' => $height,
            'scale' => $scale,
            'rotate' => $rotate
        );
    }

    public static function scale($source, $size)
    {
        if ($source instanceof Vps_Uploads_Row) {
            $source = $source->getFileSource();
        }
        if (!is_file($source)) {
            return false;
        }

        $size = self::calculateScaleDimensions($source, $size);
        if ($size === false) return false;

        // wenn bild schon der angeforderten größe entspricht, original ausgeben
        // nötig für zB animierte gifs, da sonst die animation verloren geht
        if (($size['scale'] == self::SCALE_CROP || $size['scale'] == self::SCALE_BESTFIT || $size['scale'] == self::SCALE_DEFORM)) {
            $originalSize = getimagesize($source);
            if ($originalSize[0] == $size['width'] && $originalSize[1] == $size['height']) {
                $size['scale'] = self::SCALE_ORIGINAL;
            }
        }

        if ($size['scale'] == self::SCALE_CROP) {

            // Bild wird auf allen 4 Seiten gleichmäßig beschnitten
            if (class_exists('Imagick')) {
                $im = new Imagick();
                $im->readImage($source);
                if (isset($size['rotate']) && $size['rotate']) {
                    $im->rotateImage('#FFF', $size['rotate']);
                }
                $im->scaleImage($size['resizeWidth'], $size['resizeHeight']);
                $im->cropImage($size['width'], $size['height'], $size['x'], $size['y']);
                $im->setImagePage(0, 0, 0, 0);
    //             $im->unsharpMaskImage(1, 0.5, 1.0, 0.05);
                $im = self::_processCommonImagickSettings($im);
                $ret = $im->getImageBlob();
                $im->destroy();
            }

        } elseif ($size['scale'] == self::SCALE_BESTFIT || $size['scale'] == self::SCALE_DEFORM) {

            if (class_exists('Imagick')) {
                $im = new Imagick();
                $im->readImage($source);
                if (isset($size['rotate']) && $size['rotate']) {
                    $im->rotateImage('#FFF', $size['rotate']);
                }
                $im->thumbnailImage($size['width'], $size['height']);
                $im = self::_processCommonImagickSettings($im);
                $ret = $im->getImageBlob();
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
                if (isset($size['rotate']) && $size['rotate']) {
                    $source = imagerotate($source, $size['rotate'], 0);
                }
                $destination = imagecreatetruecolor($size['width'], $size['height']);
                imagecopyresampled($destination, $source, 0, 0, 0, 0,
                                    $size['width'], $size['height'],
                                    $srcSize[0], $srcSize[1]);
                ob_start();
                if ($srcSize[2] == 1) {
                    $source = imagegif($destination);
                } elseif ($srcSize[2] == 2) {
                    $source = imagejpeg($destination);
                } elseif ($srcSize[2] == 3) {
                    $source = imagepng($destination);
                }
                $ret = ob_get_contents();
                ob_end_clean();
            }

        } elseif ($size['scale'] == self::SCALE_ORIGINAL) {

            $ret = file_get_contents($source);

        } else {

            return false;

        }
        return $ret;
    }

    private function _processCommonImagickSettings($im)
    {
        $im->setImageColorspace(Imagick::COLORSPACE_RGB);
        $im->setCompressionQuality(90);
        $version = $im->getVersion();
        if (isset($version['versionNumber']) && (int)$version['versionNumber'] >= 1632) {
            $im->setImageProperty('date:create', null);
            $im->setImageProperty('date:modify', null);
        }
        return $im;
    }
}