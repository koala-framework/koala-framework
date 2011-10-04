<?php
class Vps_Controller_Action_Cli_ClearCacheController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "clears all caches";
    }

    public function indexAction()
    {
        if ($this->_getParam('server')) {
            $config = Vps_Config_Web::getInstance($this->_getParam('server'));

            if ($config->server->useVpsForUpdate) {
                $sshHost = $config->server->user.'@'.$config->server->host;
                $sshDir = $config->server->dir;
                $cmd = "sshvps $sshHost $sshDir clear-cache";
                $cmd = "sudo -u vps $cmd";
                $this->_systemCheckRet($cmd);
            } else {
                Vps_Util_ClearCache::getInstance()->clearCache(
                    $this->_getParam('type'),
                    true,
                    true,
                    $config->server);
            }
        } else {
            Vps_Util_ClearCache::getInstance()->clearCache($this->_getParam('type'), true);
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public static function getHelpOptions()
    {
        $types = Vps_Util_ClearCache::getInstance()->getTypes();
        return array(
            array(
                'param'=> 'type',
                'value'=> $types,
                'valueOptional' => true,
                'help' => 'what to clear'
            ),
            array(
                'param'=> 'server',
                'help' => 'server',
                'allowBlank' => true
            )
        );
    }
}
