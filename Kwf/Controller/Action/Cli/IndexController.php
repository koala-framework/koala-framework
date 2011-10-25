<?php
class Kwf_Controller_Action_Cli_IndexController extends Kwf_Controller_Action_Cli_Abstract
{
    public function indexAction()
    {
        $this->_forward('index', 'help', 'kwf_controller_action_cli');
    }
    public static function getHelp()
    {
        return "show help";
    }
}
