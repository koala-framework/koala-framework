<?php
class Kwf_Controller_Action_Maintenance_ClearCacheController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->typeNames = Kwf_Util_ClearCache::getInstance()->getTypeNames();
        $this->view->assetsType = 'Kwf_Controller_Action_Maintenance:ClearCache';
        $this->view->xtype = 'kwf.maintenance.clearCache';
    }

    public static function clearCache($request, $view)
    {
        $options = array();
        if ($request->getParam('skip-other-servers')) {
            $options['skipOtherServers'] = true;
        }

        $c = new Kwf_Util_ProgressBar_Adapter_Cache($request->getParam('progressNum'));
        $options['progressAdapter'] = $c;
        $types = Kwf_Util_ClearCache::getInstance()->clearCache($request->getParam('type'), false, true, $options);
        $message = '';
        foreach ($types as $t) {
            if (!$t->getSuccess()) {
                $message .= $t->getTypeName()." ERROR: ".$t->getOutput();
            }
        }
        $view->success = true;
        $view->message = $message;
    }

    public function jsonClearCacheAction()
    {
        if (Kwf_Config::getValue('server.phpCli')) {
            $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php maintenance clear-cache ";
            $cmd .= "--type=".escapeshellarg($this->_getParam('type'));
            $cmd .= " --progressNum=".escapeshellarg($this->_getParam('progressNum'));
            $procData = Kwf_Util_BackgroundProcess::start($cmd, $this->view);
            $this->view->assign($procData);
        } else {
            self::clearCache($this->getRequest(), $this->view);
        }
    }
}
