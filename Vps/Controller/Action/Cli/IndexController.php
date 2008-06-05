<?php
class Vps_Controller_Action_Cli_IndexController extends Vps_Controller_Action_Cli_Abstract
{
    public function indexAction()
    {
        $this->_forward('index', 'help', 'vps_controller_action_cli');
    }
}
