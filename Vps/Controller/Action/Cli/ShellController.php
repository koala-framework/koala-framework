<?php
class Vps_Controller_Action_Cli_ShellController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "open shell";
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'server',
                'value'=> self::_getConfigSections(),
                'valueOptional' => false,
                'help' => 'which server'
            )
        );
    }
    public function indexAction()
    {
        $config = new Zend_Config_Ini('application/config.ini', $this->_getParam('server'));

        $host = $config->server->user.'@'.$config->server->host;
        $dir = $config->server->dir;

        $cmd = "sudo -u vps sshvps $host $dir shell";
        if ($this->_getParam('debug')) $cmd .= " --debug";
        if ($this->_getParam('debug')) echo $cmd."\n";
        passthru($cmd);
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
