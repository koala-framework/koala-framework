<?php
class Kwf_Assets_CommonJs_Parser
{
    public static function parse($filename)
    {
        $cmd = getcwd()."/".VENDOR_PATH."/bin/node ".__DIR__."/Parser.js ".$filename;
        $process = new Symfony\Component\Process\Process($cmd);
        $process->mustRun();
        $out = json_decode($process->getOutput(), true);
        $ret = array();
        foreach (array_keys($out[0]['deps']) as $i) {
            $ret[] = $i;
        }
        return $ret;
    }
}
