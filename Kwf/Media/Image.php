<?php
class Kwf_Media_Image
{
    /**
     * Returns next supported image-width
     */
    public static function getResponsiveWidthStep($width, $widths)
    {
        foreach ($widths as $cachedWidth) {
            if ($width <= $cachedWidth) {
                return $cachedWidth;
            }
        }
        return end($widths);
    }

    //formular that increases the offset between two width steps based on the width
    //1000: ~100
    //2000: ~200
    //3000: ~400
    //4000: ~700
    private static function _getOffsetAtWidth($width)
    {
        return floor(pow($width/300, 2.5)+100);
    }

    /**
     * Returns supported image-widths of specific image with given base-dimensions
     */
    public static function getResponsiveWidthSteps($dim, $imageDimensions)
    {
        $ret = array();
        if (is_string($imageDimensions)) {
            if (!file_exists($imageDimensions)) {
                return array();
            }
            $size = getimagesize($imageDimensions);
            $imageDimensions = array(
                'width' => $size[0],
                'height' => $size[1],
            );
        } else if ($imageDimensions instanceof Kwf_Uploads_Row) {
            $imageDimensions = $imageDimensions->getImageDimensions();
        } else if ($imageDimensions instanceof Imagick) {
            $imageDimensions = array(
                'width' => $imageDimensions->getImageWidth(),
                'height' => $imageDimensions->getImageHeight()
            );
        }

        if (!$imageDimensions['width']) {
            throw new Kwf_Exception("Image must have a width");
        }

        $maxWidth = $dim['width'] * 2;
        if ($imageDimensions['width'] < $dim['width'] * 2) {
            $maxWidth = $imageDimensions['width'];
        }
        $calculateWidth = $dim['width'];
        if ($imageDimensions['width'] < $dim['width']) {
            $calculateWidth = $imageDimensions['width'];
        }

        $width = $calculateWidth;
        do {
            $ret[] = $width;
            $width -= self::_getOffsetAtWidth($width);
        } while ($width > 0);


        $width = $calculateWidth;
        while (true) {
            $width += self::_getOffsetAtWidth($width);
            if ($width >= $maxWidth) {
                break;
            }
            $ret[] = $width;
        }
        $ret[] = $maxWidth;
        sort($ret);

        return $ret;
    }

    private static function _generateIntValue($high, $low, $reversed)
    {
        return $reversed ? ord($low)*256 + ord($high) : ord($high)*256 + ord($low);
    }

    /**
     * Got information from http://www.media.mit.edu/pia/Research/deepview/exif.html
     * and http://www.impulseadventure.com/photo/exif-orientation.html
     */
    public static function getExifRotation($source)
    {
        if (!Kwf_Registry::get('config')->image->autoExifRotate) {
            return 0;
        }

        $handle = fopen($source, 'rb'); // b for windows compatibility
        // Check if image is jpg file
        if (fread($handle, 2) != chr(0xFF).chr(0xD8)) {
            fclose($handle);
            return 0;
        }

        $fileSize = filesize($source);
        $count = 0;
        $rotation = 0;
        while ($count < 3) { // Run through marker
            $count++;
            $marker = fread($handle, 2);
            // Marks start of stream (SOS)
            if ($marker == chr(0xFF).chr(0xDA)) break;

            $size = self::_generateIntValue(fread($handle, 1), fread($handle, 1), false);
            if ($marker == chr(0xFF).chr(0xE1)) { // Start of exif-data-block
                if (fread($handle, 6) != 'Exif'.chr(0).chr(0)) break;

                $tiffHeaderBytes = fread($handle, 4); // should be 0x49492A00 or 0x4D4D002A
                if ($tiffHeaderBytes == 'II'.chr(0x2A).chr(0x00)) { // Motorola
                    $reversed = true;
                } else if ($tiffHeaderBytes == 'MM'.chr(0x00).chr(0x2A)) { // Intel
                    $reversed = false;
                } else { // this case should not exist
                    break;
                }

                $count = 0;
                while ($count < 5) {
                    $count++;
                    // IFD = Image File Directory => Image properties
                    // check offset from tiffHeader-Start to IFD's, if 0 end of all IFD-Blocks
                    if (fread($handle, 4) == chr(0x00).chr(0x00).chr(0x00).chr(0x00)) break 2;

                    $ifdCount = self::_generateIntValue(fread($handle, 1), fread($handle, 1), $reversed);
                    if ($fileSize < ftell($handle) + $ifdCount * 12 + 6) break 2; // check to handle eof
                    if ($ifdCount > 100) break 2; // check if ifdCount is a possible value
                    for ($i = 0; $i < $ifdCount; $i++) {
                        $ifdBytes = fread($handle, 12);
                        $tag = self::_generateIntValue($ifdBytes[0], $ifdBytes[1], $reversed);
                        if ($tag == 0x0112) {
                            // reversed saved in this form: 00 06 | 00 00 should be 00 00 | 00 06
                            // normally saved in this form: 06 00 | 00 00
                            $highBytes = self::_generateIntValue($ifdBytes[10], $ifdBytes[11], $reversed);
                            $lowBytes = self::_generateIntValue($ifdBytes[8], $ifdBytes[9], $reversed);
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
                            break 3;
                        }
                    }
                }

            } else { // Any other marker (e.g. JFIF-0xFFE0, Photoshop-Stuff), not supported. Set file-pointer to end of marker-data
                fseek($handle, $size -2, SEEK_CUR);
            }
        }
        fclose($handle);
        return $rotation;
    }

    /**
     * Returns an image with a size which should be good to work with.
     * Acutally this is a 600x600 max-width. If it's smaller in both dimensions
     * it will keep it's original size.
     */
    public static function getHandyScaleFactor($original)
    {
        $targetSize = array(600, 600, 'cover' => false);

        if (is_string($original)) {
            if (!file_exists($original)) return 1;
            $size = getimagesize($original);
            $original = array(
                'width' => $size[0],
                'height' => $size[1],
                'rotation' => self::getExifRotation($original),
            );
        }

        $target = Kwf_Media_Image::calculateScaleDimensions($original, $targetSize);
        if (abs($original['rotation']) == 90) {
            $original = array('width'=>$original['height'], 'height'=>$original['width']);
        }
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
            $sourceSize['rotation'] = self::getExifRotation($source);
        } else if ($source instanceof Imagick) {
            $sourceSize = $source->getImageGeometry();
            $source = null;
        } else if ($source instanceof Kwf_Uploads_Row) {
            $sourceSize = $source->getImageDimensions();
            $source = null;
        } else {
            $sourceSize = $source;
            $source = null;
        }

        if (!$sourceSize) return false;
        if (!isset($sourceSize['rotation'])) $sourceSize['rotation'] = 0;

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

        // Check if image has to be rotated
        if ($sourceSize['rotation'] == 90) {
            $originalSize = array($originalSize[1], $originalSize[0]);
        }

        if ($outputWidth == 0 && $outputHeight == 0) {
            if ($crop) {
                $ret = array(
                    'width' => $crop['width'],
                    'height' => $crop['height'],
                    'rotate' => 0,
                    'crop' => array(
                        'x' => $crop['x'],
                        'y' => $crop['y'],
                        'width' => $crop['width'],
                        'height' => $crop['height']
                    )
                );
                if (isset($targetSize['imageCompressionQuality'])) {
                    $ret['imageCompressionQuality'] = $targetSize['imageCompressionQuality'];
                }

                return $ret;
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
            'rotate' => $sourceSize['rotation'],
            'crop' => $crop
        );
        if (isset($targetSize['imageCompressionQuality'])) {
            $ret['imageCompressionQuality'] = $targetSize['imageCompressionQuality'];
        }

        //Set values to match original-parameters when original won't change
        if ($ret['crop']['x'] == 0
            && $ret['crop']['y'] == 0
            && $ret['crop']['width'] == $originalSize[0]
            && $ret['crop']['height'] == $originalSize[1]
            && $ret['width'] == $originalSize[0]
            && $ret['height'] == $originalSize[1]
            && !$ret['rotate']
        ) {
            $ret['keepOriginal'] = true;
        }

        return $ret;
    }

    private static function _preScale($source, $sourceSize, $mimeType, $size, $uploadId)
    {
        $preScaleFactor = 0;
        $preScaleCacheFile = null;

        $preScaleWidth = $sourceSize['width'];
        $preScaleHeight = $sourceSize['height'];
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
            $folderId = substr($uploadId, 0, 2);
            $dir = Kwf_Config::getValue('uploads') . "/mediaprescale/$folderId/$uploadId";
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $preScaleCacheFile = "$dir/$preScaleFactor";
            if (!file_exists($preScaleCacheFile)) {
                $f = $source;
                if ($previousCacheFile) {
                    $f = $previousCacheFile;
                }
                $realWidth = $preScaleWidth;
                $realHeight = $preScaleHeight;
                if (isset($size['rotate'])
                    && ($size['rotate'] == 90 || $size['rotate'] == -90)
                ) {
                    $realWidth = $preScaleHeight;
                    $realHeight = $preScaleWidth;
                }
                $blob = Kwf_Media_Scaler_Abstract::getInstance()->scale(
                    $f,
                    array('width' => $realWidth, 'height' => $realHeight, 'crop' => array('x'=>0, 'y' => 0, 'width' => $sourceSize['width'], 'height' => $sourceSize['height'])),
                    $mimeType,
                    array('skipCleanup' => !!$previousCacheFile)
                );

                file_put_contents($preScaleCacheFile, $blob);
                Kwf_Util_Upload::onFileWrite($preScaleCacheFile);
            }
            $previousCacheFile = $preScaleCacheFile;
        }
        return array(
            'factor' => $preScaleFactor,
            'file' => $preScaleCacheFile
        );
    }

    public static function scale($source, $size, $uploadId = null, $sourceSize = null, $mimeType = null)
    {
        if ($source instanceof Kwf_Uploads_Row) {
            $sourceSize = $source->getImageDimensions();
            $mimeType = $source->mime_type;
            $uploadId = $source->id;
            $source = $source->getFileSource();
        }
        if (is_string($source) && !is_file($source)) {
            return false;
        }

        if (!$sourceSize || !$mimeType) {
            if ($source instanceof Imagick) {
                if (!$sourceSize) {
                    $sourceSize = array(
                        $source->getImageWidth(),
                        $source->getImageHeight()
                    );
                }
                if (!$mimeType) {
                    $mimeType = $source->getImageMimeType();
                }
            } else {
                $s = @getimagesize($source);
                if (!$sourceSize) {
                    $sourceSize = array(
                        'width' => $s[0],
                        'height' => $s[1],
                    );
                }
                if (!$mimeType) {
                    $mimeType = $s['mime'];
                }
                unset($s);
            }
        }

        $size = self::calculateScaleDimensions($sourceSize, $size);

        if ($size === false) return false;

        // if image already has the correct size return original
        // needed e.g. for animated gifs because they will lose animation if changed
        if (isset($size['keepOriginal']) && $size['keepOriginal']) {
            if ($source instanceof Imagick) {
                $ret = $source->getImageBlob();
            } else {
                $ret = file_get_contents($source);
                Kwf_Util_Upload::onFileRead($source);
            }
            return $ret;
        }
        $preScale = array('factor'=>0);
        if ($uploadId && !$source instanceof Imagick) {
            $preScale = self::_preScale($source, $sourceSize, $mimeType, $size, $uploadId);
        }

        if ($preScale['factor']) {
            $source = $preScale['file'];
            $factor = pow(2, $preScale['factor']); //1 if factor==0
            $size['crop']['width'] = $size['crop']['width']/$factor;
            $size['crop']['height'] = $size['crop']['height']/$factor;
            $size['crop']['x'] = $size['crop']['x']/$factor;
            $size['crop']['y'] = $size['crop']['y']/$factor;
        }

        if ($source instanceof Imagick) {
            $scaler = new Kwf_Media_Scaler_Imagick();
        } else {
            $scaler = Kwf_Media_Scaler_Abstract::getInstance();
        }

        $skipCleanup = $preScale['factor'] > 0;
        if ($skipCleanup) {
            $skipCleanup = $scaler->isSkipCleanup($source, $mimeType);
        }

        return $scaler->scale($source, $size, $mimeType, array(
            'skipCleanup' => $skipCleanup
        ));
    }
}
