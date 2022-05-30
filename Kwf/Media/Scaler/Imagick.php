<?php
class Kwf_Media_Scaler_Imagick extends Kwf_Media_Scaler_Abstract
{
    public function scale($source, $size, $mimeType, array $options)
    {
        if ($source instanceof Imagick) {
            $im = $source;
        } else {
            $blob = file_get_contents($source);
            Kwf_Util_Upload::onFileRead($source);
            if (!strlen($blob)) throw new Kwf_Exception("File is empty");
            $im = self::_createImagickFromBlob($blob, $mimeType);
        }
        if (!$options['skipCleanup']) {
            $im = self::_processCommonImagickSettings($im);
        }
        if (isset($size['rotate']) && $size['rotate']) {
            $im->rotateImage(new ImagickPixel('#FFF'), $size['rotate']);
        }

        $im->cropImage($size['crop']['width'],
            $size['crop']['height'],
            $size['crop']['x'],
            $size['crop']['y']);
        $im->resizeImage($size['width'], $size['height'], Imagick::FILTER_LANCZOS, 1);
        $im->setImagePage(0, 0, 0, 0);
//             $im->unsharpMaskImage(1, 0.5, 1.0, 0.05);
        if (isset($size['imageCompressionQuality'])) {
            $im->setImageCompressionQuality($size['imageCompressionQuality']);
        } else {
            $im->setImageCompressionQuality(Kwf_Config::getValue('imageCompressionQuality'));
        }
        $ret = $im->getImageBlob();
        $im->destroy();

        return $ret;
    }

    private static function _processCommonImagickSettings($im)
    {
        if (method_exists($im, 'getImageProfiles') && $im->getImageColorspace() == Imagick::COLORSPACE_CMYK) {
            $profiles = $im->getImageProfiles('icc', false);
            $hasIccProfile = in_array('icc', $profiles);
            // if it doesnt have a CMYK ICC profile, we add one
            if ($hasIccProfile === false) {
                $iccCmyk = file_get_contents(dirname(__FILE__).'/../icc/ISOuncoated.icc');
                $im->profileImage('icc', $iccCmyk);
                unset($iccCmyk);
            }
            // then we add an RGB profile
            $iccRgb = file_get_contents(dirname(__FILE__).'/../icc/sRGB_v4_ICC_preference.icc');
            $im->profileImage('icc', $iccRgb);
            unset($iccRgb);
        }
        if (method_exists($im, 'setColorspace')) {
            $im->setColorspace(Imagick::COLORSPACE_RGB);
        } else {
            $im->setImageColorspace(Imagick::COLORSPACE_RGB);
        }

        if (method_exists($im, 'setImageChannelDepth')) {
            $im->setImageChannelDepth(Imagick::CHANNEL_ALL, 8);
        }

        $im->stripImage();
        $im->setImageCompressionQuality(Kwf_Config::getValue('imageCompressionQuality'));

        $version = $im->getVersion();
        if (isset($version['versionNumber']) && (int)$version['versionNumber'] >= 1632) {
            if ($im->getImageProperty('date:create')) $im->setImageProperty('date:create', null);
            if ($im->getImageProperty('date:modify')) $im->setImageProperty('date:modify', null);
        }
        return $im;
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

    private static function _createImagickFromBlob($blob, $mime)
    {
        $im = new Imagick();
        $im->readImageBlob($blob, 'foo.'.str_replace('image/', '', $mime)); //add fake filename to help imagick with format detection
        if (method_exists($im, 'setColorspace')) {
            $im->setType(Imagick::IMGTYPE_TRUECOLORMATTE);
            $im->setColorspace($im->getImageColorspace());
        }
        return $im;
    }
}
