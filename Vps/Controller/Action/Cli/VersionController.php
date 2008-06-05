<?php
class Vps_Controller_Action_Cli_VersionController extends Vps_Controller_Action_Cli_Abstract
{
    public function indexAction()
    {
        $c = Zend_Registry::get('config');
        echo $c->application->name . ' Version ' . $c->application->version."\n";
        echo $c->application->vps->name . ' Version ' . $c->application->vps->version;
        echo ' (Revision ' . $c->application->vps->revision.")\n";
        exit;
    }

    public static function getHelp()
    {
        return "show version information";
    }
}
