<?php
class Kwf_Util_Media
{
    public static function onCacheFileRead($file)
    {
        if (Kwf_Config::getValue('onMediaCacheReadScript')) {

            $retVar = null;
            $out = array();
            exec(Kwf_Config::getValue('onMediaCacheReadScript').' '.escapeshellarg($file), $out, $retVar);

            $retVar = null;
            $out = array();
            exec(Kwf_Config::getValue('onMediaCacheReadScript').' '.escapeshellarg($file), $out, $retVar);

            if ($retVar) {
                throw new Kwf_Exception("onMediaCacheReadScript failed with $retVar");
            }
        }
    }
    public static function onCacheFileWrite($file)
    {
        if (Kwf_Config::getValue('onMediaCacheWriteScript')) {

            $retVar = null;
            $out = array();
            exec(Kwf_Config::getValue('onMediaCacheWriteScript').' '.escapeshellarg($file), $out, $retVar);

            if ($retVar) {
                throw new Kwf_Exception("onMediaCacheWriteScript failed with $retVar");
            }
        }
    }
}

