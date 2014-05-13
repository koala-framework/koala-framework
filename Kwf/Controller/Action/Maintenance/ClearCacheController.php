<?php
class Kwf_Controller_Action_Maintenance_ClearCacheController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        if (!file_exists('build/assets')) {
            throw new Kwf_Exception_Client("Installation incomplete: 'build' folder does not exist. You can generate it by calling 'php bootstrap.php build' on commandline. On production servers you should upload locally generated build.");
        }

        $this->view->typeNames = Kwf_Util_ClearCache::getInstance()->getTypeNames();
        $this->view->assetsPackage = Kwf_Assets_Package_Maintenance::getInstance('Maintenance');
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
        $options['types'] = $request->getParam('type');
        $options['output'] = false;
        $options['refresh'] = true;
        $types = Kwf_Util_ClearCache::getInstance()->clearCache($options);
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
