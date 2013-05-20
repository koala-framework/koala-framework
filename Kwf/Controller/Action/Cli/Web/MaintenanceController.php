<?php
class Kwf_Controller_Action_Cli_Web_MaintenanceController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'maintenance (interal)';
    }

    public function clearCacheAction()
    {
        $options = array();
        if ($this->_getParam('skip-other-servers')) {
            $options['skipOtherServers'] = true;
        }

        $c = new Kwf_Util_ProgressBar_Adapter_Cache($this->_getParam('progressNum'));
        $options['progressAdapter'] = $c;
        Kwf_Util_ClearCache::getInstance()->clearCache($this->_getParam('type'), false, true, $options);
        $out = array(
            'success' => true
        );
        echo json_encode($out);
        exit;
    }
}
