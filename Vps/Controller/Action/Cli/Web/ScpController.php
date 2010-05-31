<?php
class Vps_Controller_Action_Cli_Web_ScpController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "copy using scp";
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'server',
                'value'=> self::_getConfigSections(),
                'valueOptional' => false,
                'help' => 'which server'
            ),
            array(
                'param'=> 'file',
                'valueOptional' => false,
                'help' => 'file to copy, must be relative and in web'
            )

        );
    }
    public function indexAction()
    {
        $file = $this->_getParam('file');
        if (!is_file($file)) {
            throw new Vps_ClientException('file not found');
        }
        if (substr($file, 0, 1) == '/') {
            throw new Vps_ClientException('file must be relative');
        }
        $p = realpath($file);
        if (substr($p, 0, strlen(getcwd())) != getcwd()) {
            throw new Vps_ClientException('file must be in web');
        }
        $section = $this->_getParam('server');

        $config = Vps_Config_Web::getInstance($section);


        if (!$config->server->host) {
            throw new Vps_ClientException("No host configured for $section server");
        }

        $host = $config->server->user.'@'.$config->server->host.':'.$config->server->port;
        $dir = $config->server->dir;

        $cmd = "sudo -u vps sshvps $host $dir scp";
        $cmd .= " --file=".escapeshellarg($file);
        if ($this->_getParam('debug')) $cmd .= " --debug";
        if ($this->_getParam('debug')) echo $cmd."\n";
        passthru($cmd);
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
