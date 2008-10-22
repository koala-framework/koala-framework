<?php
class Vps_Controller_Action_Cli_SvnUpController extends Vps_Controller_Action_Cli_Abstract
{
    public function indexAction()
    {
        echo "updating web\n";
        passthru('svn up');
        echo "\nupdating vps\n";
        passthru('svn up '.VPS_PATH);
        exit;
    }
}
