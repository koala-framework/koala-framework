<?php
class Vps_Controller_Action_Cli_Web_ShellController extends Vps_Controller_Action_Cli_Abstract
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
                'allowBlank' => true,
                'help' => 'which server'
            )
        );
    }
    public function indexAction()
    {
        $section = $this->_getParam('server');
        if (!$section) {
            echo "Choose a server:\n";
            $sections = self::_getConfigSections();
            foreach ($sections as $k=>$i) {
                echo ($k+1).": ".$i."\n";
            }
            $stdin = fopen('php://stdin', 'r');
            $input = fgets($stdin, 3);
            fclose($stdin);
            $input = $input-1;
            if (!isset($sections[$input])) {
                throw new Vps_Exception("Invalid server number");
            }
            $section = $sections[$input];
        }

        $config = Vps_Config_Web::getInstance($section);


        if (!$config->server->host) {
            throw new Vps_ClientException("No host configured for $section server");
        }

        $host = $config->server->user.'@'.$config->server->host.':'.$config->server->port;
        $dir = $config->server->dir;

        $cmd = "sudo -u vps ".Vps_Util_Git::getAuthorEnvVars()." sshvps $host $dir shell";
        if ($this->_getParam('debug')) $cmd .= " --debug";
        if ($this->_getParam('debug')) echo $cmd."\n";
        passthru($cmd);
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
