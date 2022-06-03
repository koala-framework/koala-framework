<?php
abstract class Kwf_Media_Scaler_Abstract
{
    public static function getInstance()
    {
        static $instance;
        if (isset($instance)) return $instance;
        $scaler = Kwf_Config::getValue('mediaScaler');
        if (!$scaler) {
            if (class_exists('Imagick')) {
                $scaler = 'Imagick';
            } else {
                $scaler = 'Gd';
            }
        }
        $scaler = 'Kwf_Media_Scaler_'.$scaler;
        $instance = new $scaler();
        return $instance;
    }

    abstract public function scale($source, $size, $mimeType, array $options);

    public function isSkipCleanup($source, $mimeType)
    {
        if ($source instanceof Imagick) {
            $im = $source;
        } else {
            $blob = file_get_contents($source);
            Kwf_Util_Upload::onFileRead($source);
            if (!strlen($blob)) throw new Kwf_Exception("File is empty");
            $im = Kwf_Media_Scaler_Imagick::createImagickFromBlob($blob, $mimeType);
        }

        if (method_exists($im, 'setImageChannelDepth') && $im->getImageChannelDepth(Imagick::CHANNEL_ALL) >= 16) {
            return false;
        } else {
            return true;
        }
    }
}
