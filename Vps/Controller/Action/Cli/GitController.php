<?php
class Vps_Controller_Action_Cli_GitController extends Vps_Controller_Action_Cli_Abstract
{
    public function checkoutStagingAction()
    {
        Vps_Util_Git::vps()->fetch();
        Vps_Util_Git::web()->fetch();

        Vps_Util_Git::web()->checkout("staging");
        $appId = Vps_Registry::get('config')->application->id;
        Vps_Util_Git::vps()->checkout("$appId-staging");
        exit;
    }

    public function checkoutMasterAction()
    {
        Vps_Util_Git::vps()->fetch();
        Vps_Util_Git::web()->fetch();

        Vps_Util_Git::vps()->checkout(trim(file_get_contents('application/vps_branch')));
        $appId = Vps_Registry::get('config')->application->id;
        Vps_Util_Git::web()->checkout("master");
        exit;
    }

    public function checkoutProductionAction()
    {
        Vps_Util_Git::vps()->fetch();
        Vps_Util_Git::web()->fetch();

        $appId = Vps_Registry::get('config')->application->id;
        Vps_Util_Git::vps()->checkout("$appId-production");
        Vps_Util_Git::web()->checkout("production");

        system("php bootstrap.php update", $ret);
        exit($ret);
    }
}
