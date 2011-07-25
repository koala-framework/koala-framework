<?php
class Vps_Controller_Action_Cli_Web_ExportController extends Vps_Controller_Action_Cli_Abstract
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
                'value'=> self::_getConfigSectionsWithHost(),
                'valueOptional' => false,
                'help' => 'where to update'
            ),
            array(
                'param'=> 'with-library',
                'help' => 'updates library as well'
            ),
            array(
                'param'=> 'skip-update',
                'help' => 'skip update scripts and so don\'t clear caches'
            )
        );
    }

    public function indexAction()
    {
        $config = Vps_Config_Web::getInstance($this->_getParam('server'));
        echo "updating ".$this->_getParam('server')."....\n";
        $this->_update($config);


        if (isset($config->server->subWebs) && $config->server->subWebs) {
            foreach ($config->server->subWebs as $web) {
                chdir($web);
                $ret = null;
                $cmd = "php bootstrap.php export --server=".$this->_getParam('server');
                passthru($cmd, $ret);
                chdir("..");
                if ($ret != 0) {
                    exit(1);
                }
            }
        }
        if (isset($config->server->subSections) && $config->server->subSections) {
            foreach ($config->server->subSections as $section) {
                $config = Vps_Config_Web::getInstance($section);
                echo "\nupdating $section...\n";
                $this->_update($config);
            }
        }
        exit;
    }

    private function _update($config)
    {
        if (!$config->server->host) {
            echo "kein host definiert...\n";
            return;
        }

        $sshHost = $config->server->user.'@'.$config->server->host.':'.$config->server->port;
        $sshDir = $config->server->dir;

        $params = '';
        if ($this->_getParam('with-library')) {
            $params .= ' --with-library';
        }
        if ($this->_getParam('skip-update')) {
            $params .= ' --skip-update';
        }

        if (!$config->server->useVpsForUpdate) {
            echo "updating $sshHost:$sshDir\n";
            $cmd = "svn up{$params}";
            $cmd = "sshvps $sshHost $sshDir $cmd";
            $cmd = "sudo -u vps $cmd";
            if ($this->_getParam('debug')) {
                echo $cmd."\n";
            }
            $this->_systemCheckRet($cmd);
        } else {
            $cmd = "svn-up{$params}";
            $cmd = "sshvps $sshHost $sshDir $cmd";
            $cmd = "sudo -u vps ".Vps_Util_Git::getAuthorEnvVars()." $cmd";
            if ($this->_getParam('debug')) {
                echo $cmd."\n";
            }
            $this->_systemCheckRet($cmd);
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
