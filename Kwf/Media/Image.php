<?php
class Kwf_Media_Image
{
    const SCALE_BESTFIT = 'bestfit';
    const SCALE_CROP = 'crop';
    const SCALE_DEFORM = 'deform';
    const SCALE_ORIGINAL = 'original';

    public static function getHandyScaleFactor($originalPath)
    {
        $targetSize = array(600, 600, Kwf_Media_Image::SCALE_BESTFIT);
        $original = @getimagesize($originalPath);
        $original['width'] = $original[0];
        $original['height'] = $original[1];
        $target = Kwf_Media_Image::calculateScaleDimensions($originalPath, $targetSize);

        if ($original['width'] <= $target['width'] && $original['height'] <= $target['height']) {
            return 1;
        } else {
            return $original['width'] / $target['width'];
        }
    }

    /**
     * targetSize options: width, height, bestfit, aspectRatio (if SCALE_CROP and width or height is 0)
     */
    public static function calculateScaleDimensions($source, $targetSize)
    {
        // Get size of image (handle different param-possibilities)
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

        $originalSize = array($w, $h);


        // Check if image has to be rotated
        $rotate = null;
        if (Kwf_Registry::get('config')->image->autoExifRotate
            && $source
            && function_exists('exif_read_data')
            && isset($sourceSize['mime'])
            && ($sourceSize['mime'] == 'image/jpg'
                || $sourceSize['mime'] == 'image/jpeg')
        ) {
            try {
                $exif = exif_read_data($source);
                if (isset($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 6:
                            $originalSize = array($h, $w);
                            $rotate = 90;
                        case 8:
                            $originalSize = array($h, $w);
                            $rotate = -90;
                    }
                }
            } catch (ErrorException $e) {
                $rotate = null;
            }
        }

        if (!$originalSize[0] || !$originalSize[1]) return false;


        // get output-width
        if (isset($targetSize['width'])) $outputWidth = $targetSize['width'];
        else if (isset($targetSize[0])) $outputWidth = $targetSize[0];
        else $outputWidth = 0;

        // get output-height
        if (isset($targetSize['height'])) $outputHeight = $targetSize['height'];
        else if (isset($targetSize[1])) $outputHeight = $targetSize[1];
        else $outputHeight = 0;

        // get crop-data
        if (isset($targetSize['crop'])) $crop = $targetSize['crop'];
        else $crop = null;

        // get bestfit
        if (isset($targetSize['bestfit'])) $bestfit = $targetSize['bestfit'];
        else $bestfit = false;

        if ($outputWidth == 0 && $outputHeight == 0) {
            // Handle keep original
            return array(
                'width' => $originalSize[0],
                'height' => $originalSize[1],
                'bestfit' => true,
                'rotate' => null
           );
        }



        // Calculate missing dimension
        $calculateWidth = $originalSize[0];
        $calculateHeight = $originalSize[1];
        if ($crop) {
            $calculateWidth = $crop['width'];
            $calculateHeight = $crop['height'];
        }


        if (!$bestfit) { // image will always have defined size
            if ($outputWidth == 0) {
                if (isset($targetSize['aspectRatio'])) {
                    $outputWidth = round($outputHeight * $targetSize['aspectRatio']);
                } else {
                    $outputWidth = round($outputHeight * ($calculateWidth / $calculateHeight));
                }
                if ($outputWidth <= 0) $outputWidth = 1;
            }
            if ($outputHeight == 0) {
                if (isset($targetSize['aspectRatio']) && $targetSize['aspectRatio']) {
                    $outputHeight = round($outputWidth * $targetSize['aspectRatio']);
                } else {
                    $outputHeight = round($outputWidth * ($calculateHeight / $calculateWidth));
                }
                if ($outputHeight <= 0) $outputHeight = 1;
            }

            if (!$crop) { // crop from complete image
                $crop = array();
                // calculate crop depending on target-size
                if (($outputWidth / $outputHeight) >= ($originalSize[0] / $originalSize[1])) {
                    $crop['width'] = $originalSize[0];
                    $crop['height'] = $originalSize[0] * ($outputHeight / $outputWidth);
                } else {
                    $crop['height'] = $originalSize[1];
                    $crop['width'] = $originalSize[1] * ($outputWidth / $outputHeight);
                }
                // calculate x and y of crop
                $xDiff = $originalSize[0] - $crop['width'];
                $yDiff = $originalSize[1] - $crop['height'];
                if ($xDiff > 0) {
                    $crop['x'] = $xDiff / 2;
                } else {
                    $crop['x'] = 0;
                }
                if ($yDiff > 0) {
                    $crop['y'] = $yDiff / 2;
                } else {
                    $crop['y'] = 0;
                }
            }
        } elseif ($bestfit) { // image keeps aspectratio and will not be scaled up
            // calculateWidth is cropWidth if existing else originalWidth.
            // prevent image scale up
            if (!$crop) {
                $crop = array(
                    'x' => 0,
                    'y' => 0,
                    'width' => $originalSize[0],
                    'height' => $originalSize[1]
                );
            }

            if ($calculateWidth <= $outputWidth && $calculateHeight <= $outputHeight) {
                $outputWidth = $calculateWidth;
                $outputHeight = $calculateHeight;
            } else {
                if ($calculateWidth < $outputWidth) {
                    $outputWidth = $calculateWidth;
                }
                if ($calculateHeight < $outputHeight) {
                    $outputHeight = $calculateHeight;
                }
            }

            $widthRatio = $outputWidth ? $calculateWidth / $outputWidth : null;
            $heightRatio = $outputHeight ? $calculateHeight / $outputHeight : null;
            if ($widthRatio > $heightRatio) {
                $outputWidth = $calculateWidth / $widthRatio;
                $outputHeight = $calculateHeight / $widthRatio;
            } else if ($heightRatio > $widthRatio) {
                $outputWidth = $calculateWidth / $heightRatio;
                $outputHeight = $calculateHeight / $heightRatio;
            }
        }

        $outputWidth = round($outputWidth);
        if ($outputWidth <= 0) $outputWidth = 1;
        $outputHeight = round($outputHeight);
        if ($outputHeight <= 0) $outputHeight = 1;

        return array(
            'width' => round($outputWidth),
            'height' => round($outputHeight),
            'bestfit' => $bestfit,
            'rotate' => $rotate,
            'crop' => $crop
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
                    $im = new Imagick();
                    $im->readImage($source);
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
                    $im = new Imagick();
                    $im->readImage($source);
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

        $im->setImageColorspace(Imagick::COLORSPACE_RGB);
        $im->stripImage();
        $im->setImageCompressionQuality(90);
        $version = $im->getVersion();
        if (isset($version['versionNumber']) && (int)$version['versionNumber'] >= 1632) {
            if ($im->getImageProperty('date:create')) $im->setImageProperty('date:create', null);
            if ($im->getImageProperty('date:modify')) $im->setImageProperty('date:modify', null);
        }
        return $im;
    }
}
