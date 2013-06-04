<?php
class Kwf_Controller_Action_Cli_Web_MaintenanceController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'maintenance (interal)';
    }

    public function clearCacheAction()
    {
        $view = new Kwf_View_Json();
        Kwf_Controller_Action_Maintenance_ClearCacheController::clearCache($this->getRequest(), $view);
        echo json_encode($view->getOutput());
        exit;
    }

    public function updateAction()
    {
        $view = new Kwf_View_Json();
        Kwf_Controller_Action_Maintenance_UpdateController::executeUpdates($this->getRequest(), $view);
        echo json_encode($view->getOutput());
        exit;
    }

    public function downloadUpdatesAction()
    {
        $view = new Kwf_View_Json();
        Kwf_Controller_Action_Maintenance_UpdateDownloaderController::downloadUpdates($this->getRequest(), $view);
        echo json_encode($view->getOutput());
        exit;
    }
}
