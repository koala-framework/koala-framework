<?php
class Kwf_Controller_Action_Cli_ClearCacheController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "clears all caches";
    }

    public function indexAction()
    {
        Kwf_Util_ClearCache::getInstance()->clearCache($this->_getParam('type'), true);
        exit;
    }

    public static function getHelpOptions()
    {
        $types = Kwf_Util_ClearCache::getInstance()->getTypes();
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
