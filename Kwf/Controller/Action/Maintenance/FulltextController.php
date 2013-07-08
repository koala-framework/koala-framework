<?php
class Kwf_Controller_Action_Maintenance_FulltextController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->typeNames = Kwf_Util_ClearCache::getInstance()->getTypeNames();
        $this->view->assetsType = 'Kwf_Controller_Action_Maintenance:Fulltext';
        $this->view->xtype = 'kwf.maintenance.fulltext';
    }

    public function jsonRebuildAction()
    {
        if (!Kwf_Config::getValue('server.phpCli')) {
            throw new Kwf_Exception_Client("Not (yet?) supported without phpCli");
        }

        $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php fulltext rebuild --silent";
        $procData = Kwf_Util_BackgroundProcess::start($cmd, $this->view);
        $this->view->assign($procData);
    }
}
