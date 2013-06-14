<?php
class Kwf_Util_BackgroundProcess
{
    public static function start($cmd, $view)
    {
        $outputFile = tempnam('./temp', 'bgproc');
        $cmd .= " >$outputFile 2>$outputFile.err &";
        $cmd .= "  echo -n $!";
        $pid = shell_exec($cmd);

        $fi = pathinfo($outputFile);

        return array(
            'backgroundProcessRunning' => true,
            'outputFile' => $fi['filename'],
            'pid' => $pid,
        );
    }
}
