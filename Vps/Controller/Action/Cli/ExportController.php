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
                'value'=> self::_getConfigSectionsWithHost(),
                'valueOptional' => false,
                'help' => 'where to update'
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

        if (!$config->server->useVpsForUpdate) {
            echo "updating $this->_sshHost:$this->_sshDir\n";
            $cmd = "svn up";
            $cmd = "sshvps $this->_sshHost $this->_sshDir $cmd";
            $cmd = "sudo -u vps $cmd";
            $this->_systemCheckRet($cmd);
        } else {
            $this->_systemSshVps("svn-up");
        }

        if (isset($config->server->subWebs)) {
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
        exit;
    }

}
