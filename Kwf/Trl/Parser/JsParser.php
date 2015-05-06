<?php
class Kwf_Trl_Parser_JsParser
{
    public static function parseContent($content)
    {
        $cmd = getcwd().'/'.VENDOR_PATH.'/bin/node '.__DIR__.'/JsParser.js '.escapeshellarg($content);
        exec($cmd, $cmdOutput, $retVal);
        if ($retVal) {
            throw new Kwf_Exception("Parsing js file failed: $content".implode("\n", $cmdOutput));
        }
        return json_decode($cmdOutput[0], true);
    }
}
