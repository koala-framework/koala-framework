<?php
class Kwf_Media_Image
{
    const SCALE_BESTFIT = 'bestfit';
    const SCALE_CROP = 'crop';
    const SCALE_DEFORM = 'deform';
    const SCALE_ORIGINAL = 'original';

    private static function _generateIntValue($high, $low, $reversed)
    {
        return $reversed ? ord($low)*256 + ord($high) : ord($high)*256 + ord($low);
    }

    /**
     * Got information from http://www.media.mit.edu/pia/Research/deepview/exif.html
     * and http://www.impulseadventure.com/photo/exif-orientation.html
     */
    public static function getExifRotationFallback($source)
    {
        $rotation = 0;
        $handle = fopen($source, "rb"); // b for windows compatibility
        $fileHeaderSize = min(5000, filesize($source));
        $contents = fread($handle, $fileHeaderSize);
        if (self::_generateIntValue($contents[0], $contents[1], false) == 0XFFD8) { //Marks jpg file
            // could be dynamically changed if exif-data-block start at
                        // different location (documentation not clear about this)
            for ($count = 2; $count < $fileHeaderSize-1; $count++) {
                $marker = self::_generateIntValue($contents[$count], $contents[$count+1], false);
                if ($marker == 0xFFE1) {//marks start exif-data-block
                    $type = self::_generateIntValue($contents[$count+10], $contents[$count+11], false);
                    if ($type == 0x4D4D) {//Motorola, reverse byte-order
                        $reversed = true;
                    } else if ($type == 0x4949) { //Intel
                        $reversed = false;
                    }
                    //number of directory entries in IFD0
                    $propertyCount = self::_generateIntValue($contents[$count+19],
                                            $contents[$count+18], $reversed);
                    $dataPos = $count+20;
                    for ($p = 0; $p < $propertyCount; $p++) {
                        $tag = self::_generateIntValue($contents[$dataPos+($p*12)+1],
                                        $contents[$dataPos+($p*12)], $reversed);
                        if ($tag == 0x0112) { //Orientation-Tag
                            // reversed saved in this form: 00 06 | 00 00 should be 00 00 | 00 06
                            // normally saved in this form: 06 00 | 00 00
                            $highBytes = self::_generateIntValue($contents[$dataPos+($p*12)+11],
                                            $contents[$dataPos+($p*12)+10], $reversed);
                            $lowBytes = self::_generateIntValue($contents[$dataPos+($p*12)+9],
                                            $contents[$dataPos+($p*12)+8], $reversed);
                            $exifOrientation = $highBytes * pow(16, 4) + $lowBytes;
                            switch ($exifOrientation) {
                                case 1:
                                    $rotation = 0;
                                    break;
                                case 8: //Left_Bottom
                                    $rotation = -90;
                                    break;
                                case 3:
                                    $rotation = 180;
                                    break;
                                case 6: //Right_Top
                                    $rotation = 90;
                                    break;
                            }
                            break;
                        }
                    }
                    break;
                }
            }
        }
        fclose($handle);
        return $rotation;
    }

    public static function getExifRotation($source)
    {
        $rotate = 0;
        if (Kwf_Registry::get('config')->image->autoExifRotate) {
            if (class_exists('Imagick') && method_exists(new Imagick(), 'getImageOrientation')) {
                if (is_string($source)) {
                    $source = new Imagick($source);
                }
                $orientation = $source->getImageOrientation();
                switch ($orientation) {
                    case Imagick::ORIENTATION_BOTTOMRIGHT:
                        $rotate = 180;
                        break;
                    case Imagick::ORIENTATION_RIGHTTOP:
                        $rotate = 90;
                        break;
                    case Imagick::ORIENTATION_LEFTBOTTOM:
                        $rotate = -90;
                        break;
                }
            } else {
                $rotate = self::getExifRotationFallback($source);
            }
        }
        return $rotate;
    }

    /**
     * targetSize options: width, height, scale, aspectRatio (if SCALE_CROP and width or height is 0)
     */
    public static function calculateScaleDimensions($source, $targetSize)
    {
        if (is_string($source)) {
            $sourceSize = @getimagesize($source);
        } else if ($source instanceof Imagick) {
            $sourceSize = $source->getImageGeometry();
            $source = null;
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
        if ($source) {
            $rotate = self::getExifRotation($source);
            if (abs($rotate) == 90) {
                $size = array($h, $w);
            }
            if (!$rotate) {
                $rotate = null;
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

        if ($scale != self::SCALE_ORIGINAL && $scale != self::SCALE_BESTFIT) {
            if ($width == 0) {
                if (isset($targetSize['aspectRatio'])) {
                    $width = round($height * $targetSize['aspectRatio']);
                } else {
                    $width = round($height * ($size[0]/$size[1]));
                }
                if ($width <= 0) $width = 1;
            }
            if ($height == 0) {
                if (isset($targetSize['aspectRatio']) && $targetSize['aspectRatio']) {
                    $height = round($width * $targetSize['aspectRatio']);
                } else {
                    $height = round($width * ($size[1]/$size[0]));
                }
                if ($height <= 0) $height = 1;
            }
        }

        if ($scale == self::SCALE_CROP) {
            // Bild wird auf allen 4 Seiten gleichmäßig beschnitten

            if (!$width || !$height) {
                throw new Kwf_Exception("width and height must be set higher than 0 "
                    ."if Kwf_Media_Image::SCALE_CROP is used. Maybe "
                    ."Kwf_Media_Image::SCALE_BESTFIT would be better?");
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

            $widthRatio = $width ? $size[0] / $width : null;
            $heightRatio = $height ? $size[1] / $height : null;

            if ($widthRatio > $heightRatio) {
                $width = $size[0] / $widthRatio;
                $height = $size[1] / $widthRatio;
            } else if ($heightRatio > $widthRatio) {
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
        if ($source instanceof Kwf_Uploads_Row) {
            $source = $source->getFileSource();
        }
        if (is_string($source) && !is_file($source)) {
            return false;
        }

        $size = self::calculateScaleDimensions($source, $size);
        if ($size === false) return false;

        // wenn bild schon der angeforderten größe entspricht, original ausgeben
        // nötig für zB animierte gifs, da sonst die animation verloren geht
        if (($size['scale'] == self::SCALE_CROP || $size['scale'] == self::SCALE_BESTFIT || $size['scale'] == self::SCALE_DEFORM)) {
            if ($source instanceof Imagick) {
                $originalSize = array($source->getImageWidth(), $source->getImageHeight());
            } else {
                $originalSize = getimagesize($source);
            }
            if ($originalSize[0] == $size['width'] && $originalSize[1] == $size['height']) {
                $size['scale'] = self::SCALE_ORIGINAL;
            }
        }

        if ($size['scale'] == self::SCALE_CROP) {

            // Bild wird auf allen 4 Seiten gleichmäßig beschnitten
            if (class_exists('Imagick')) {
                if ($source instanceof Imagick) {
                    $im = $source;
                } else {
                    $im = self::_createImagickFromFile($source);
                }
                $im = self::_processCommonImagickSettings($im);
                if (isset($size['rotate']) && $size['rotate']) {
                    $im->rotateImage('#FFF', $size['rotate']);
                }
                $im->scaleImage($size['resizeWidth'], $size['resizeHeight']);
                $im->cropImage($size['width'], $size['height'], $size['x'], $size['y']);
                $im->setImagePage(0, 0, 0, 0);
    //             $im->unsharpMaskImage(1, 0.5, 1.0, 0.05);
                $ret = $im->getImageBlob();
                $im->destroy();
            }

        } elseif ($size['scale'] == self::SCALE_BESTFIT || $size['scale'] == self::SCALE_DEFORM) {

            if (class_exists('Imagick')) {
                if ($source instanceof Imagick) {
                    $im = $source;
                } else {
                    $im = self::_createImagickFromFile($source);
                }
                $im = self::_processCommonImagickSettings($im);
                if (isset($size['rotate']) && $size['rotate']) {
                    $im->rotateImage('#FFF', $size['rotate']);
                }
                $im->thumbnailImage($size['width'], $size['height']);
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

            if ($source instanceof Imagick) {
                $ret = $source->getImageBlob();
            } else {
                $ret = file_get_contents($source);
            }

        } else {

            return false;

        }
        return $ret;
    }

    private function _createImagickFromFile($file)
    {
        $im = new Imagick();
        $im->readImage($file);
        if (method_exists($im, 'setColorspace')) {
            $im->setType(Imagick::IMGTYPE_TRUECOLORMATTE);
            $im->setColorspace($im->getImageColorspace());
        }
        return $im;
    }

    private function _createImagickFromBlob($blob, $mime)
    {
        $im = new Imagick();
        $im->readImageBlob($blob, 'foo.'.str_replace('image/', '', $mime)); //add fake filename to help imagick with format detection
        if (method_exists($im, 'setColorspace')) {
            $im->setType(Imagick::IMGTYPE_TRUECOLORMATTE);
            $im->setColorspace($im->getImageColorspace());
        }
        return $im;
    }

    private function _processCommonImagickSettings($im)
    {
        if (method_exists($im, 'getImageProfiles') && $im->getImageColorspace() == Imagick::COLORSPACE_CMYK) {
            $profiles = $im->getImageProfiles('icc', false);
            $hasIccProfile = in_array('icc', $profiles);
            // if it doesnt have a CMYK ICC profile, we add one
            if ($hasIccProfile === false) {
                $iccCmyk = file_get_contents(Kwf_Config::getValue('libraryPath').'/icc/ISOuncoated.icc');
                $im->profileImage('icc', $iccCmyk);
                unset($iccCmyk);
            }
            // then we add an RGB profile
            $iccRgb = file_get_contents(Kwf_Config::getValue('libraryPath').'/icc/sRGB_v4_ICC_preference.icc');
            $im->profileImage('icc', $iccRgb);
            unset($iccRgb);
        }
        if (method_exists($im, 'setColorspace')) {
            $im->setColorspace(Imagick::COLORSPACE_RGB);
        } else {
            $im->setImageColorspace(Imagick::COLORSPACE_RGB);
        }

        if (method_exists($im, 'setColorspace')) {
            $im->setColorspace(Imagick::COLORSPACE_RGB);
        } else {
            $im->setImageColorspace(Imagick::COLORSPACE_RGB);
        }

        $im->stripImage();
        $im->setImageCompressionQuality(80);

        $version = $im->getVersion();
        if (isset($version['versionNumber']) && (int)$version['versionNumber'] >= 1632) {
            if ($im->getImageProperty('date:create')) $im->setImageProperty('date:create', null);
            if ($im->getImageProperty('date:modify')) $im->setImageProperty('date:modify', null);
        }
        return $im;
    }
}
