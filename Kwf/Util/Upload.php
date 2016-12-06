<?php
class Kwf_Util_Upload
{
    public static function onFileRead($file)
    {
        if (Kwf_Config::getValue('onUploadReadScript')) {
            $retVar = null;

            $out = array();
            $cmd = Kwf_Config::getValue('onUploadReadScript').' '.escapeshellarg($file).' 2>&1';
            exec($cmd, $out, $retVar);

            if ($retVar) {
                throw new Kwf_Exception("onUploadReadScript failed with $retVar");
            }

        }
    }

    public static function onFileWrite($file)
    {
        if (Kwf_Config::getValue('onUploadWriteScript')) {
            $retVar = null;

            $out = array();
            $cmd = Kwf_Config::getValue('onUploadWriteScript').' '.escapeshellarg($file).' 2>&1';
            exec($cmd, $out, $retVar);
            if ($retVar) {
                throw new Kwf_Exception("onUploadWriteScript failed with $retVar");
            }

        }
    }
}

