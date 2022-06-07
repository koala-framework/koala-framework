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
        return true;
    }
}
