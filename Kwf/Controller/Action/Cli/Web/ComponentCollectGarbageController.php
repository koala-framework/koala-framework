<?php
class Kwf_Controller_Action_Cli_Web_ComponentCollectGarbageController extends Kwf_Controller_Action
{
    public static function getHelp()
    {
        return "collect component garbage, execute once a day";
    }

    public function indexAction()
    {
        Kwf_Component_Cache::getInstance()->collectGarbage($this->_getParam('debug'));
        Kwf_Component_Cache_Url_Abstract::getInstance()->collectGarbage($this->_getParam('debug'));
        exit;
    }
}
