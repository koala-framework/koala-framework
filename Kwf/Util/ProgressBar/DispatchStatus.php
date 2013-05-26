<?php
class Kwf_Util_ProgressBar_DispatchStatus
{
    public static function dispatch()
    {
        Kwf_Loader::registerAutoload();
        if (empty($_REQUEST['progressNum'])) {
            throw new Kwf_Exception('progressNum required');
        }
        $pbarAdapter = new Kwf_Util_ProgressBar_Adapter_Cache($_REQUEST['progressNum']);
        $pbarStatus = $pbarAdapter->getStatus();
        if (!$pbarStatus) {
            $pbarStatus = array();
        }
        $pbarStatus['success'] = true;

        if (isset($_REQUEST['outputFile']) && isset($_REQUEST['pid'])) {
            $processes = Kwf_Util_Process::getRunningProcesses();
            if (isset($processes[$_REQUEST['pid']])) {
                $pbarStatus['bgFinished'] = false;
            } else {
                $pbarStatus['bgFinished'] = true;
                if (!preg_match('#^bgproc[a-z0-9]+$#i', $_REQUEST['outputFile'])) throw new Kwf_Exception_AccessDenied();
                $output = file_get_contents('./temp/'.$_REQUEST['outputFile']);
                $outputErr = file_get_contents('./temp/'.$_REQUEST['outputFile'].'.err');
                $outputJson = json_decode($output);
                if (!$outputJson) {
                    //assign as string
                    $pbarStatus['bgError'] = $outputErr;
                } else {
                    $pbarStatus['bgResponse'] = $outputJson;
                    $pbarStatus['bgError'] = file_get_contents('./temp/'.$_REQUEST['outputFile'].'.err');
                    $pbarStatus['bgError'] = preg_replace('#^(PHP )?Deprecated: .*$#m', '', $pbarStatus['bgError']); //ignore errors from deprecated php.ini settings
                    $pbarStatus['bgError'] = trim($pbarStatus['bgError']);
                }
            }
        }

        echo Zend_Json::encode($pbarStatus);
        exit;
    }
}