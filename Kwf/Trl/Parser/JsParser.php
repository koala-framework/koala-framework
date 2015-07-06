<?php
class Kwf_Trl_Parser_JsParser
{
    public static function parseContent($content)
    {
        $vendorPath = 'vendor';
        if (defined('VENDOR_PATH')) {
            $vendorPath = VENDOR_PATH;
        }
        $process = new Symfony\Component\Process\Process(getcwd()."/$vendorPath/bin/node ".__DIR__.'/JsParser.js');
        $process = $process->setInput($content);
        $process->run();
        if (!$process->isSuccessful()) {
            throw new Kwf_Exception("Parsing js file failed: ".$process->getErrorOutput());
        }
        return json_decode($process->getOutput(), true);
    }
}
