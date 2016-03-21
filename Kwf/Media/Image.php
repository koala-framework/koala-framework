<?php
class Kwf_Media_Image
{
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
     * Returns an image with a size which should be good to work with.
     * Acutally this is a 600x600 max-width. If it's smaller in both dimensions
     * it will keep it's original size.
     */
    public static function getHandyScaleFactor($originalPath)
    {
        $targetSize = array(600, 600, 'cover' => false);
        $original = @getimagesize($originalPath);
        if (abs(self::getExifRotation($originalPath)) == 90) {
            $original = array($original[1], $original[0]);
        }
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
     * targetSize options: width, height, cover, aspectRatio
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
        if (isset($sourceSize[0])) $w = $sourceSize[0];
        if (isset($sourceSize['width'])) $w = $sourceSize['width'];

        $h = null;
        if (isset($sourceSize[1])) $h = $sourceSize[1];
        if (isset($sourceSize['height'])) $h = $sourceSize['height'];

        if (!$w || !$h) return false;

        $originalSize = array($w, $h);

        // get output-width
        $outputWidth = 0;
        if (isset($targetSize[0])) $outputWidth = $targetSize[0];
        if (isset($targetSize['width'])) $outputWidth = $targetSize['width'];

        // get output-height
        $outputHeight = 0;
        if (isset($targetSize[1])) $outputHeight = $targetSize[1];
        if (isset($targetSize['height'])) $outputHeight = $targetSize['height'];

        // get crop-data
        $crop = isset($targetSize['crop']) ? $targetSize['crop'] : null;

        // get cover
        $cover = isset($targetSize['cover']) ? $targetSize['cover'] : true;

        if ($outputWidth == 0 && $outputHeight == 0) {
            if ($crop) {
                return array(
                    'width' => $crop['width'],
                    'height' => $crop['height'],
                    'rotate' => 0,
                    'crop' => array(
                        'x' => $crop['x'],
                        'y' => $crop['y'],
                        'width' => $crop['width'],
                        'height' => $crop['height']
                    ),
                );
            } else {
                // Handle keep original
                return array(
                    'width' => $originalSize[0],
                    'height' => $originalSize[1],
                    'rotate' => 0,
                    'crop' => array(
                        'x' => 0,
                        'y' => 0,
                        'width' => $originalSize[0],
                        'height' => $originalSize[1]
                    ),
                    'keepOriginal' => true,
                );
            }
        }

        // Check if image has to be rotated
        $rotate = 0;
        if ($source) {
            $rotate = self::getExifRotation($source);
            if (abs($rotate) == 90) {
                $originalSize = array($originalSize[1], $originalSize[0]);
            }
        }

        // Calculate missing dimension
        $calculateWidth = $originalSize[0];
        $calculateHeight = $originalSize[1];
        if ($crop) {
            $calculateWidth = $crop['width'];
            $calculateHeight = $crop['height'];
        }

        if ($cover) { // image will always have defined size

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
                $crop['x'] = $xDiff > 0 ? $xDiff / 2 : 0;
                $yDiff = $originalSize[1] - $crop['height'];
                $crop['y'] = $yDiff > 0 ? $yDiff / 2 : 0;
            } else {
                $oldCrop['width'] = $crop['width'];
                $oldCrop['height'] = $crop['height'];
                if (($outputWidth / $outputHeight) >= ($crop['width'] / $crop['height'])) {
                    $crop['width'] = $crop['width'];
                    $crop['height'] = $crop['width'] * ($outputHeight / $outputWidth);
                } else {
                    $crop['height'] = $crop['height'];
                    $crop['width'] = $crop['height'] * ($outputWidth / $outputHeight);
                }
                $xDiff = $oldCrop['width'] - $crop['width'];
                $crop['x'] += $xDiff > 0 ? $xDiff / 2 : 0;
                $yDiff = $oldCrop['height'] - $crop['height'];
                $crop['y'] += $yDiff > 0 ? $yDiff / 2 : 0;
            }

        } elseif (!$cover) { // image keeps aspectratio and will not be scaled up

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

        $ret = array(
            'width' => round($outputWidth),
            'height' => round($outputHeight),
            'rotate' => $rotate,
            'crop' => $crop
        );

        //Set values to match original-parameters when original won't change
        if ($ret['crop']['x'] == 0
            && $ret['crop']['y'] == 0
            && $ret['crop']['width'] == $originalSize[0]
            && $ret['crop']['height'] == $originalSize[1]
            && $ret['width'] == $originalSize[0]
            && $ret['height'] == $originalSize[1]
        ) {
            $ret['rotate'] = 0;
            $ret['keepOriginal'] = true;
        }

        return $ret;
    }

    private static function _preScale($source, $sourceSize, $size, $uploadId)
    {
        $preScaleFactor = 0;
        $preScaleCacheFile = null;

        $preScaleWidth = $sourceSize[0];
        $preScaleHeight = $sourceSize[1];
        if (isset($size['rotate'])
            && ($size['rotate'] == 90 || $size['rotate'] == -90)
        ) {
            list($preScaleWidth, $preScaleHeight) = array($preScaleHeight, $preScaleWidth); //swap
        }
        $preScaleTargetWidth = $size['width'];
        $preScaleTargetHeight = $size['height'];
        if (isset($size['crop']['width'])) {
            $preScaleTargetWidth *= $preScaleWidth / $size['crop']['width'];
            $preScaleTargetHeight *= $preScaleHeight / $size['crop']['height'];
        }

        $previousCacheFile = null;
        while ($preScaleWidth/2 > $preScaleTargetWidth && $preScaleHeight/2 > $preScaleTargetHeight) {
            //generate pre scaled versions of the image, every versions half the size of the previous
            $preScaleWidth /= 2;
            $preScaleHeight /= 2;
            $preScaleFactor++;
            $dir = Kwf_Config::getValue('uploads') . "/mediaprescale/$uploadId";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $preScaleCacheFile = "$dir/$preScaleFactor";
            if (!file_exists($preScaleCacheFile)) {
                $f = $source;
                if ($previousCacheFile) {
                    $f = $previousCacheFile;
                }
                $im = self::_createImagickFromBlob(file_get_contents($f), $sourceSize['mime']);
                if (!$previousCacheFile) {
                    $im = self::_processCommonImagickSettings($im); //only once
                }
                $realWidth = $preScaleWidth;
                $realHeight = $preScaleHeight;
                if (isset($size['rotate'])
                    && ($size['rotate'] == 90 || $size['rotate'] == -90)
                ) {
                    $realWidth = $preScaleHeight;
                    $realHeight = $preScaleWidth;
                }
                $im->resizeImage($realWidth, $realHeight, Imagick::FILTER_LANCZOS, 1);
                $blob = $im->getImageBlob();
                if (!strlen($blob)) throw new Kwf_Exception("imageblob is empty");
                file_put_contents($preScaleCacheFile, $blob);
                $im->destroy();
            }
            $previousCacheFile = $preScaleCacheFile;
        }
        return array(
            'factor' => $preScaleFactor,
            'file' => $preScaleCacheFile
        );
    }

    public static function scale($source, $size, $uploadId = null)
    {
        if ($source instanceof Kwf_Uploads_Row) {
            $source = $source->getFileSource();
        }
        if (is_string($source) && !is_file($source)) {
            return false;
        }

        $sourceSize = @getimagesize($source);

        $size = self::calculateScaleDimensions($source, $size);
        if ($size === false) return false;

        // if image already has the correct size return original
        // needed e.g. for animated gifs because they will lose animation if changed
        if (isset($size['keepOriginal']) && $size['keepOriginal']) {
            if ($source instanceof Imagick) {
                $ret = $source->getImageBlob();
            } else {
                $ret = file_get_contents($source);
            }
            return $ret;
        }

        if (class_exists('Imagick')) {
            $preScale = array('factor'=>0);
            if ($uploadId && !$source instanceof Imagick) {
                $preScale = self::_preScale($source, $sourceSize, $size, $uploadId);
            }

            if ($source instanceof Imagick) {
                $im = $source;
            } else {
                $f = $source;
                if ($preScale['factor']) {
                    $f = $preScale['file'];
                }
                $blob = file_get_contents($f);
                if (!strlen($blob)) throw new Kwf_Exception("File is empty");
                $im = self::_createImagickFromBlob($blob, $sourceSize['mime']);
            }
            if (!$preScale['factor']) {
                //preScale does this already
                $im = self::_processCommonImagickSettings($im);
            }
            if (isset($size['rotate']) && $size['rotate']) {
                $im->rotateImage('#FFF', $size['rotate']);
            }

            $factor = pow(2, $preScale['factor']); //1 if factor==0
            $im->cropImage($size['crop']['width']/$factor,
                           $size['crop']['height']/$factor,
                           $size['crop']['x']/$factor,
                           $size['crop']['y']/$factor);
            $im->resizeImage($size['width'], $size['height'], Imagick::FILTER_LANCZOS, 1);
            $im->setImagePage(0, 0, 0, 0);
//             $im->unsharpMaskImage(1, 0.5, 1.0, 0.05);
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
            imagecopyresampled($destination, $source, 0, 0, $size['crop']['x'], $size['crop']['y'],
                                $size['width'], $size['height'],
                                $size['crop']['width'], $size['crop']['width']);
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
