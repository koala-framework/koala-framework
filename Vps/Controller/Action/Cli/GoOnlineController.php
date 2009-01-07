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
        $ret[] = array('param' => 'skip-test');
        $ret[] = array('param' => 'skip-prod');
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
        Zend_Session::start(); //wegen tests

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
        $vpsVersion = $this->_getParam('vps-version');
        $webVersion = $this->_getParam('web-version');


        echo "\n\n*** [01/14] vps-tag erstellen\n";
        Vps_Controller_Action_Cli_TagController::createVpsTag($this->_getParam('vps-branch'), $vpsVersion);

        echo "\n\n*** [02/14] web-tag erstellen\n";
        Vps_Controller_Action_Cli_TagController::createWebTag($this->_getParam('web-branch'), $webVersion);

        echo "\n\n*** [03/14] prod daten auf test uebernehmen\n";
        $this->_systemSshVps("import", $testConfig);

        echo "\n\n*** [04/14] vps tag auschecken\n";
        $this->_systemSshVps("tag-checkout vps-checkout --version=$vpsVersion", $testConfig);

        echo "\n\n*** [05/14] test: vps-version anpassen\n";
        $this->_systemSshVps("tag-checkout vps-use --version=$vpsVersion", $testConfig);

        echo "\n\n*** [06/14] test: web tag switchen\n";
        $this->_systemSshVps("tag-checkout web-switch --version=$webVersion", $testConfig);

        echo "\n\n*** [07/14] test: update ausfüuehren\n";
        $this->_systemSshVps("update", $testConfig);

        echo "\n\n*** [08/14] test: unit-tests laufen lassen\n";
        if ($this->_getParam('skip-test')) {
            echo "(uebersprungen)\n";
        } else {
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
        }

        echo "\n\n*** [09/14] test: web zurueck auf trunk switchen\n";
        $this->_systemSshVps("tag-checkout web-switch --version=trunk", $testConfig);

        echo "\n\n*** [10/14] test: vps-version zurueck auf trunk anpassen\n";
        $this->_systemSshVps("tag-checkout vps-use --version=trunk", $testConfig);

        if ($this->_getParam('skip-prod')) {
            echo "\n\n*** [11/14] prod: (uebersprungen)\n";
        } else {
            echo "\n\n*** [11/14] prod: erstelle datenbank backup\n";
            $this->_systemSshVps("import backup-db", $prodConfig);

            echo "\n\n*** [12/14] prod: vps-version anpassen\n";
            $this->_systemSshVps("tag-checkout vps-use --version=$vpsVersion", $prodConfig);

            echo "\n\n*** [13/14] prod: web tag switchen\n";
            $this->_systemSshVps("tag-checkout web-switch --version=$webVersion", $prodConfig);

            echo "\n\n*** [14/14] prod: update ausführen\n";
            $this->_systemSshVps("update", $prodConfig);
        }

        echo "\n\n\n\033[32mF E R T I G ! ! !\033[0m\n";

        $this->_helper->viewRenderer->setNoRender(true);
    }
}
