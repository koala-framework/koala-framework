<?php
class Kwf_Assets_Util_CssSelectorCount
{
    public static function count($contents)
    {
        $cmd = getcwd()."/".VENDOR_PATH."/bin/node ".KWF_PATH."/Kwf/Assets/Util/CssSelectorCount.js";
        $cmd .= " 2>&1";
        $process = new Symfony\Component\Process\Process($cmd);
        $process->setEnv(array(
            'NODE_PATH' => getcwd().'/node_modules'
        ));
        $process->setInput($contents);

        $process->mustRun();

        $out = json_decode($process->getOutput(), true);
        return $out['selectors'];
    }
}
