<?php
class Kwf_Assets_CommonJs_ModuleDepsParser
{
    public static function parse($filename)
    {
        $cmd = getcwd()."/".VENDOR_PATH."/bin/node ".__DIR__."/ModuleDepsParser.js ".$filename;
        $process = new Symfony\Component\Process\Process($cmd);
        $process->mustRun();
        $out = json_decode($process->getOutput(), true);
        $ret = array(
            'source' => $out[0]['source'],
            'deps' => array()
        );
        foreach (array_keys($out[0]['deps']) as $i) {
            $ret['deps'][] = $i;
        }
        return $ret;
    }
}
