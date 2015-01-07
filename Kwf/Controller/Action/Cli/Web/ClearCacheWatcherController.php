<?php
class Kwf_Controller_Action_Cli_Web_ClearCacheWatcherController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'watch filesystem for modification and clear affected caches';
    }

    public function indexAction()
    {
        Kwf_Util_ClearCache_Watcher::watch();
    }
}
