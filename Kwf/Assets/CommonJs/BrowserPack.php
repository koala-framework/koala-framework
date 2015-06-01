<?php
class Kwf_Assets_CommonJs_BrowserPack
{
    public static function pack($data)
    {
        $cmd = getcwd()."/".VENDOR_PATH."/bin/node ".__DIR__."/BrowserPack.js";
        $process = new Symfony\Component\Process\Process($cmd);
        $process->setInput(json_encode($data));
        $process->mustRun();
        return $process->getOutput();
    }
}
