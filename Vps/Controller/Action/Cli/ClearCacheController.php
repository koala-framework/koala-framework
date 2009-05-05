<?php
class Vps_Controller_Action_Cli_ClearCacheController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "clears all caches";
    }

    public function indexAction()
    {
        Vps_Util_ClearCache::getInstance()->clearCache($this->_getParam('type'), true);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public static function getHelpOptions()
    {
        $types = Vps_Util_ClearCache::getInstance()->getTypes();
        return array(
            array(
                'param'=> 'type',
                'value'=> $types,
                'valueOptional' => true,
                'help' => 'what to clear'
            )
        );
    }
}
