<?php
class Vps_Controller_Action_Cli_ExportController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "update svn online";
    }

    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'server',
                'value'=> self::_getConfigSections(),
                'valueOptional' => false,
                'help' => 'where to update'
            ),
            array(
                'param'=> 'with-library',
                'help' => 'updates library as well'
            )
        );
    }

    private function _systemSshVps($cmd)
    {
        $cmd = "sshvps $this->_sshHost $this->_sshDir $cmd";
        $cmd = "sudo -u vps $cmd";
        return $this->_systemCheckRet($cmd);
    }

    public function indexAction()
    {
        $config = Vps_Config_Web::getInstance($this->_getParam('server'));


        $this->_sshHost = $config->server->user.'@'.$config->server->host;
        $this->_sshDir = $config->server->dir;

        $params = '';
        if ($this->_getParam('with-library')) {
            $params .= ' --with-library';
        }

        $this->_systemSshVps("svn-up{$params}");
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
