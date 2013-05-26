<?php
class Kwf_Controller_Action_Maintenance_UpdateDownloaderController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $this->view->assetsType = 'Kwf_Controller_Action_Maintenance:UpdateDownloader';
        $this->view->xtype = 'kwf.maintenance.updateDownloader';
    }

    public function jsonDownloadUpdatesAction()
    {
        if (!is_writable('.')) {
            throw new Kwf_Exception_Client("Root directory is not writeable for ".`whoami`." user");
        }

        $errors = array();
        $files = glob('{,.}*', GLOB_BRACE);
        foreach ($files as $f) {
            if ($f == '..') continue;
            if (!is_writable($f)) {
                $errors[] = $f;
            }
        }
        if ($errors) {
            throw new Kwf_Exception_Client("Following directories are not writeable for ".`whoami`." user:<br />".implode("<br />", $errors));
        }

        $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php maintenance download-updates ";
        $cmd .= " --progressNum=".escapeshellarg($this->_getParam('progressNum'));
        if ($this->_getParam('libraryUrl')) {
            $cmd .= " --libraryUrl=".escapeshellarg($this->_getParam('libraryUrl'));
        }
        $cmd .= " --kwfUrl=".escapeshellarg($this->_getParam('kwfUrl'));
        $cmd .= " --appUrl=".escapeshellarg($this->_getParam('appUrl'));
        $procData = Kwf_Util_BackgroundProcess::start($cmd, $this->view);
        $this->view->assign($procData);
    }
}
