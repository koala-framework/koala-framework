<?php
class Kwf_Controller_Action_Maintenance_ClearCacheController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->typeNames = Kwf_Util_ClearCache::getInstance()->getTypeNames();
        $this->view->assetsType = 'Kwf_Controller_Action_Maintenance:ClearCache';
        $this->view->xtype = 'kwf.maintenance.clearCache';
    }

    public function jsonClearCacheAction()
    {
        $cmd = "php bootstrap.php maintenance clear-cache ";
        $cmd .= "--type=".escapeshellarg($this->_getParam('types'));
        $cmd .= " --progressNum=".escapeshellarg($this->_getParam('progressNum'));
        $procData = Kwf_Util_BackgroundProcess::start($cmd, $this->view);
        $this->view->assign($procData);
    }
}