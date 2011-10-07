<?php
class Vps_Util_ProgressBar_DispatchStatus
{
    public static function dispatch()
    {
        Vps_Loader::registerAutoload();
        if (empty($_REQUEST['progressNum'])) {
            throw new Vps_Exception('progressNum required');
        }
        $pbarAdapter = new Vps_Util_ProgressBar_Adapter_Cache($_REQUEST['progressNum']);
        $pbarStatus = $pbarAdapter->getStatus();
        if (!$pbarStatus) {
            $pbarStatus = array();
        }
        $pbarStatus['success'] = true;
        echo Zend_Json::encode($pbarStatus);
        exit;
    }
}