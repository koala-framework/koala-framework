<?php
class Kwf_Util_Upload
{
    public static function onFileAccess($file)
    {
        if (Kwf_Config::getValue('onUploadAccessScript')) {
            $retVar = null;
            $out = array();
            $cmd = Kwf_Config::getValue('onUploadAccessScript').' '.escapeshellarg($file).' 2>&1';
            exec($cmd, $out, $retVar);
            if ($retVar) {
                throw new Kwf_Exception("onUploadAccessScript failed with $retVar");
            }
        }
    }
}

