<?php
class Vps_Controller_Action_Cli_Web_CopyToTestController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "copy from prod to test (database+uploads)";
    }

    public static function getHelpOptions()
    {
        $sections = self::_getConfigSections();
        if (in_array('production', $sections)) {
            unset($sections[array_search('production', $sections)]);
            $sections = array_values($sections);
        }
        return array(
            array(
                'param'=> 'server',
                'value'=> $sections,
                'help' => 'where to copy from prod',
                'valueOptional'=>true
            )
        );
    }

    private function _systemSshVps($cmd)
    {
        $cmd = "sshvps $this->_sshHost $this->_sshDir $cmd";
        $cmd = "sudo -u vps $cmd";
        if ($this->_getParam('debug')) {
            $cmd .= " --debug";
            echo $cmd."\n";
        }
        return $this->_systemCheckRet($cmd);
    }

    public function indexAction()
    {
        $config = Vps_Config_Web::getInstance($this->_getParam('server'));

        $this->_sshHost = $config->server->user.'@'.$config->server->host.':'.$config->server->port;
        $this->_sshDir = $config->server->dir;

        $this->_systemSshVps("import");

        $this->_helper->viewRenderer->setNoRender(true);
    }
}
