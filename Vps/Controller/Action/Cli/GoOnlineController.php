<?php
class Vps_Controller_Action_Cli_GoOnlineController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        if (!Vps_Controller_Action_Cli_TagController::getProjectName()) return null;
        if (Vps_Registry::get('config')->server->host != 'vivid') return null;
        return "go online";
    }

    public static function getHelpOptions()
    {
        $ret = Vps_Controller_Action_Cli_TagController::getHelpOptions();
        $ret['vpsVersion']['allowBlank'] = false;
        $ret['webVersion']['allowBlank'] = false;
        return $ret;
    }

    private function _systemSshVps($cmd, $config)
    {
        $sshHost = $config->server->user.'@'.$config->server->host;
        $sshDir = $config->server->dir;
        $cmd = "sshvps $sshHost $sshDir $cmd";
        $cmd = "sudo -u www-data $cmd";
        return $this->_systemCheckRet($cmd);
    }

    public function indexAction()
    {
        $prodConfig = new Zend_Config_Ini('application/config.ini', 'production');
        if (!$prodConfig || !$prodConfig->server->host || !$prodConfig->server->dir) {
            throw new Vps_ClientException("Prod-Server not configured");
        }

        $testConfig = new Zend_Config_Ini('application/config.ini', 'test');
        if (!$testConfig || !$testConfig->server->host || !$testConfig->server->dir) {
            throw new Vps_ClientException("Test-Server not configured");
        }
        if ($testConfig->server->dir == $prodConfig->server->dir) {
            throw new Vps_ClientException("Test-Server not configured, same dir as production");
        }

        echo "\n\n*** [01/11] vps-tag erstellen\n";
        $vpsVersion = $this->_getParam('vps-version');
        Vps_Controller_Action_Cli_TagController::createVpsTag($this->_getParam('vps-branch'), $vpsVersion);

        echo "\n\n*** [02/11] web-tag erstellen\n";
        $webVersion = $this->_getParam('web-version');
        Vps_Controller_Action_Cli_TagController::createWebTag($this->_getParam('web-branch'), $webVersion);

        echo "\n\n*** [03/11] prod daten auf test uebernehmen\n";
        $this->_systemSshVps("import", $testConfig);

        echo "\n\n*** [04/11] vps tag auschecken\n";
        $this->_systemSshVps("tag-checkout vps-checkout --version=$vpsVersion", $testConfig);

        echo "\n\n*** [05/11] test: vps-version anpassen\n";
        $this->_systemSshVps("tag-checkout vps-use --version=$vpsVersion", $testConfig);

        echo "\n\n*** [06/11] test: web tag switchen\n";
        $this->_systemSshVps("tag-checkout web-switch --version=$webVersion", $testConfig);

        echo "\n\n*** [07/11] test: update ausführen\n";
        $this->_systemSshVps("update", $testConfig);

        echo "\n\n*** [08/11] test: unit-tests laufen lassen\n";
        Vps_Controller_Action_Cli_TestController::initForTests();
        $suite = new Vps_Test_TestSuite();
        $runner = new PHPUnit_TextUI_TestRunner;

        Vps_Registry::set('testDomain', $testConfig->server->domain);

        $arguments = array();
        $arguments['colors'] = true;
        $result = $runner->doRun($suite, $arguments);
        if (!$result->wasSuccessful()) {
            throw new Vps_ClientException("Tests failed");
        }
/*
        echo "\n\n*** [09/11] prod: vps-version anpassen\n";
        $this->_systemSshVps("tag-checkout vps-use --version=$vpsVersion", $prodConfig);

        echo "\n\n*** [10/11] prod: web tag switchen\n";
        $this->_systemSshVps("tag-checkout web-switch --version=$webVersion", $prodConfig);

        echo "\n\n*** [11/11] prod: update ausführen\n";
        $this->_systemSshVps("update", $prodConfig);
*/
        echo "\n\n\n\033[32mF E R T I G ! ! !\033[0m\n";

        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function vpsTagCheckoutAction()
    {
    }
}
