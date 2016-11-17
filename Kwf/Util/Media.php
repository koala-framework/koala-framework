<?php
class Kwf_Util_Media
{
    public static function onCacheFileAccess($file)
    {
        if (Kwf_Config::getValue('onMediaCacheAccessScript')) {

            $retVar = null;
            $out = array();
            exec(Kwf_Config::getValue('onMediaCacheAccessScript').' '.escapeshellarg($file), $out, $retVar);

            $retVar = null;
            $out = array();
            exec(Kwf_Config::getValue('onMediaCacheAccessScript').' '.escapeshellarg($file), $out, $retVar);

            if ($retVar) {
                throw new Kwf_Exception("onMediaCacheAccessScript failed with $retVar");
            }
        }
    }
}

