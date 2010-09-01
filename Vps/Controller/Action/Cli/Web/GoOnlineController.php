<?php
class Vps_Controller_Action_Cli_Web_GoOnlineController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        if (Vps_Registry::get('config')->server->host != 'vivid') return null;
        return "go online";
    }

    public static function getHelpOptions()
    {
        $ret = array();
        if (file_exists('.svn')) {
            $ret = Vps_Controller_Action_Cli_Web_TagController::getHelpOptions();
        }
        $ret[] = array('param' => 'skip-copy-to-test');
        $ret[] = array('param' => 'skip-test');
        $ret[] = array('param' => 'skip-prod');
        $ret[] = array('param' => 'skip-check');
        $ret[] = array('param' => 'skip-backup');
        return $ret;
    }

    private function _systemSshVps($cmd, $config)
    {
        if (!$config->server->host) {
            echo " -> Kommando nicht ausgefuehrt, host in section '".($config->getSectionName())."' nicht gesetzt: $cmd \n";
            return 0;
        }

        $sshHost = $config->server->user.'@'.$config->server->host.':'.$config->server->port;
        $sshDir = $config->server->dir;
        $cmd = "sshvps $sshHost $sshDir $cmd";
        $cmd = "sudo -u vps ".Vps_Util_Git::getAuthorEnvVars()." $cmd";
        if ($this->_getParam('debug')) {
            $cmd .= " --debug";
            echo $cmd."\n";
        }
        return $this->_systemCheckRet($cmd);
    }

    private function _systemSshVpsWithSubSections($cmd, $section)
    {
        echo "$section...\n";
        $config = Vps_Config_Web::getInstance($section);
        $this->_systemSshVps($cmd, $config);
        if ($config->server->subSections) {
            foreach ($config->server->subSections as $s) {
                if (!$s) continue;
                $this->_systemSshVpsWithSubSections($cmd, $s);
            }
        }
    }

    public function indexAction()
    {
        if ($this->_getParam('debug')) {
            Vps_Util_Git::setDebugOutput(true);
        }
        $useSvn = file_exists('.svn');
        $appId = Vps_Registry::get('config')->application->id;

        Zend_Session::start(); //wegen tests

        $prodConfig = Vps_Config_Web::getInstance('production');
        $hasProdHost = true;
        if (!$prodConfig || !$prodConfig->server->host || !$prodConfig->server->dir) {
            echo "Prod-Server not configured.\n";
            $hasProdHost = false;
        }
        if (!$hasProdHost && !$prodConfig->server->subSections) {
            throw new Vps_ClientException("Prod-Server not configured and no subsections are set");
        }

        $testConfig = Vps_Config_Web::getInstance('test');
        $hasTestHost = true;
        if (!$testConfig || !$testConfig->server->host || !$testConfig->server->dir) {
            echo "Test-Server not configured.\n";
            $hasTestHost = false;
        }
        $hasTestSubsections = false;
        if ($testConfig->server->subSections) {
            $hasTestSubsections  = true;
        }
        if ($hasTestHost && $hasProdHost) {
            if ($testConfig->server->dir == $prodConfig->server->dir && $testConfig->server->host == $prodConfig->server->host) {
                throw new Vps_ClientException("Test-Server not configured, same dir as production");
            }
        }
        if ($useSvn) {
            $vpsVersion = $this->_getParam('vps-version');
            $webVersion = $this->_getParam('web-version');
            if (!$vpsVersion || !$webVersion) {
                $msg = "Parameters --vps-version and --web-version are required.\n";
                $o = Vps_Controller_Action_Cli_Web_TagController::getHelpOptions();
                $msg .= "--web-version=".implode(' --web-version=', $o['webVersion']['value'])."\n";
                $msg .= "--vps-version=".implode(' --vps-version=', $o['vpsVersion']['value'])."";
                throw new Vps_ClientException($msg);
            }
        }

        if (date('w')==5) {
            echo "Heute ist aber Freitag!\n";
            echo "Trotzdem weitermachen? [Y/n]";
            $stdin = fopen('php://stdin', 'r');
            $input = trim(strtolower(fgets($stdin, 2)));
            fclose($stdin);
            if (!($input == '' || $input == 'j' || $input == 'y')) {
                exit;
            }
        }

        echo "\n\n*** [00/13] ueberpruefe auf nicht eingecheckte dateien\n";

        if ($this->_getParam('skip-check')) {
            echo "(uebersprungen)\n";
        } else {
            echo "lokal:\n";


            //TODO: getBranchesNotMerged und isEmptyLog machen das gleiche
            //      zweiteres ist schoener, ersters sollte entfernt werden
            Vps_Util_Git::web()->fetch();
            $activeBranch = Vps_Util_Git::web()->getActiveBranch();
            if (Vps_Util_Git::web()->revParse('production')) {
                $branches = Vps_Util_Git::web()->getBranchesNotMerged();
                if (in_array('production', $branches)
                    || !Vps_Util_Git::web()->isEmptyLog("$activeBranch..production")
                ) {
                    throw new Vps_Exception_Client("web: production branch is NOT merged into your current branch.");
                }
            }
            if (Vps_Util_Git::web()->revParse('origin/production')) {
                $branches = Vps_Util_Git::web()->getBranchesNotMerged();
                if (in_array('remotes/origin/production', $branches)
                    || in_array('origin/production', $branches)
                    || !Vps_Util_Git::web()->isEmptyLog("$activeBranch..origin/production")
                ) {
                    throw new Vps_Exception_Client("web: production branch is NOT merged into your current branch.");
                }
            }

            Vps_Util_Git::vps()->fetch();
            $activeBranch = Vps_Util_Git::vps()->getActiveBranch();
            if (Vps_Util_Git::vps()->revParse("production/$appId")) {
                $branches = Vps_Util_Git::vps()->getBranchesNotMerged();
                if (in_array('production/'.$appId, $branches)
                    || !Vps_Util_Git::vps()->isEmptyLog("$activeBranch..production/$appId")
                ) {
                    throw new Vps_Exception_Client("vps: production branch is NOT merged into your current branch.");
                }
            }
            if (Vps_Util_Git::vps()->revParse("origin/production/$appId")) {
                $branches = Vps_Util_Git::vps()->getBranchesNotMerged();
                if (in_array('remotes/origin/production/'.$appId, $branches)
                    || in_array('origin/production/'.$appId, $branches)
                    || !Vps_Util_Git::vps()->isEmptyLog("$activeBranch..origin/production/$appId")
                ) {
                    throw new Vps_Exception_Client("vps: production branch is NOT merged into your current branch.");
                }
            }

            Vps_Controller_Action_Cli_Web_SvnUpController::checkForModifiedFiles(true);

            if ($hasTestHost || $hasTestSubsections) {
                $this->_systemSshVpsWithSubSections("svn-up check-for-modified-files", 'test');
            }
            $this->_systemSshVpsWithSubSections("svn-up check-for-modified-files", 'production');
        }

        if (Vps_Util_Git::web()->getActiveBranch() != 'master') {
            throw new Vps_Exception_Client("web: current branch is not master. This is not yet supported.");
        }
        if (Vps_Util_Git::vps()->getActiveBranch() != trim(file_get_contents('application/vps_branch'))) {
            throw new Vps_Exception_Client("vps: current branch is not ".trim(file_get_contents('application/vps_branch')).". This is not yet supported.");
        }

        if ($useSvn) {
            echo "\n\n*** [01/13] vps-tag erstellen\n";
            Vps_Controller_Action_Cli_Web_TagController::createVpsTag($vpsVersion);
        } else {
            $stagingVps = Vps_Util_Git::vps()->revParse("HEAD");
        }

        if ($useSvn) {
            echo "\n\n*** [02/13] web-tag erstellen\n";
            Vps_Controller_Action_Cli_Web_TagController::createWebTag($webVersion);
        } else {
            $stagingWeb = Vps_Util_Git::web()->revParse("HEAD");
        }

        if ($useSvn) {
            echo "\n\n*** [03/13] vps tag auschecken\n";
            if ($hasTestHost || $hasTestSubsections) {
                $this->_systemSshVpsWithSubSections("tag-checkout vps-checkout --version=$vpsVersion", 'test');
            }
            $this->_systemSshVpsWithSubSections("tag-checkout vps-checkout --version=$vpsVersion", 'production');
        }

        if ($hasTestHost || $hasTestSubsections) {
            if ($useSvn) {
                echo "\n\n*** [04/13] test: vps-version anpassen\n";
                $this->_systemSshVpsWithSubSections("tag-checkout vps-use --version=$vpsVersion", 'test');

                echo "\n\n*** [05/13] test: web tag switchen\n";
                $this->_systemSshVpsWithSubSections("tag-checkout web-switch --version=$webVersion", 'test');
            } else {
                echo "\n\n*** [04/13] test: checkout staging\n";
                $this->_systemSshVpsWithSubSections("git checkout-staging --revVps=$stagingVps --revWeb=$stagingWeb", 'test');
            }
        }

        $skipCopyToTest = ($this->_getParam('skip-copy-to-test') || $this->_getParam('skip-copy-to-test'));
        if ($hasTestHost || $hasTestSubsections) {
            echo "\n\n*** [06/13] prod daten auf test uebernehmen\n";
            if ($skipCopyToTest) {
                echo "(uebersprungen)\n";
            } else {
                $this->_systemSshVpsWithSubSections("import", 'test');
            }
        } else {
            echo "\n\n*** [06/13] prod daten importieren\n";
            if ($skipCopyToTest) {
                echo "(uebersprungen)\n";
            } else {
                $this->_systemCheckRet("php bootstrap.php import");
            }
        }

        echo "\n\n*** [07/13] test: unit-tests laufen lassen (zuerst Web-Tests, dann VPS-Tests)\n";
        $skipTest = ($this->_getParam('skip-test') || $this->_getParam('skip-tests'));
        if ($skipTest) {
            echo "(uebersprungen)\n";
        } else if (!$hasTestHost) {
            echo "(uebersprungen, kein test server angegeben)\n";
        } else {
            Vps_Controller_Action_Cli_TestController::initForTests();
            $runner = new Vps_Test_TestRunner();
            $arguments = array();
            $arguments['colors'] = true;
            $arguments['stopOnFailure'] = true;
            $arguments['excludeGroups'] = array('skipGoOnline');
            $arguments['retryOnError'] = true;
            if ($this->_getParam('verbose')) $arguments['verbose'] = true;

            // Web
            if ($testConfig) {
                $cfg = $testConfig;
            } else {
                $cfg = Vps_Registry::get('config');
            }
            Vps_Registry::set('testDomain', $cfg->server->domain);
            Vps_Registry::set('testServerConfig', $cfg);
            $result = $runner->doRun(new Vps_Test_TestSuite(), $arguments);
            if (!$result->wasSuccessful()) {
                if ($testConfig) {
                    if ($useSvn) {
                        $this->_systemSshVpsWithSubSections("tag-checkout web-switch --version=trunk", 'test');
                        $this->_systemSshVpsWithSubSections("tag-checkout vps-use --version=branch", 'test');
                    } else {
                        $this->_systemSshVpsWithSubSections("git checkout-master", 'test');
                    }
                }
                throw new Vps_ClientException("Tests failed");
            }

            // VPS
            $dir = getcwd();
            chdir(VPS_PATH);
            $config = Vps_Registry::get('config');
            $trl = Vps_Registry::get('trl');
            Vps_Registry::set('trl', new Vps_Trl());
            $cfg = new Vps_Config_Web(Vps_Setup::getConfigSection());
            Vps_Registry::set('config', $cfg);
            Vps_Registry::set('testDomain', $cfg->server->domain);
            Vps_Registry::set('testServerConfig', $cfg);
            $result = $runner->doRun(new Vps_Test_TestSuite(), $arguments);
            if (!$result->wasSuccessful()) {
                if ($testConfig) {
                    if ($useSvn) {
                        $this->_systemSshVpsWithSubSections("tag-checkout web-switch --version=trunk", 'test');
                        $this->_systemSshVpsWithSubSections("tag-checkout vps-use --version=branch", 'test');
                    } else {
                        $this->_systemSshVpsWithSubSections("git checkout-master", 'test');
                    }
                }
                throw new Vps_ClientException("Tests failed");
            }

            chdir($dir);
            Vps_Registry::set('config', $config);
            Vps_Registry::set('trl', $trl);
        }

        if ($hasTestHost || $hasTestSubsections) {
            echo "\n\n*** [08/13] test: zurueck auf trunk switchen\n";
            if ($useSvn) {
                $this->_systemSshVpsWithSubSections("tag-checkout web-switch --version=trunk", 'test');
                $this->_systemSshVpsWithSubSections("tag-checkout vps-use --version=branch", 'test');
            } else {
                $this->_systemSshVpsWithSubSections("git checkout-master", 'test');
            }
        }

        $updateProd = false;
        $doneTodos = array();
        if ($this->_getParam('skip-prod')) {
            echo "\n\n*** [10/13] prod: (uebersprungen)\n";
        } else {
            echo "\nUpdate Production?  [Y/n]";
            $this->_notifyUser("Test Finished. Update Production?");
            $stdin = fopen('php://stdin', 'r');
            $input = trim(strtolower(fgets($stdin, 2)));
            fclose($stdin);
            if ($input == '' || $input == 'j' || $input == 'y') {
                $updateProd = true;
            }
        }

        if ($updateProd) {

            if (!$this->_getParam('skip-backup')) {
                echo "\n\n*** [10/13] prod: erstelle datenbank backup\n";
                $this->_systemSshVpsWithSubSections("import backup-db", 'production');
            }

            if ($useSvn) {
                echo "\n\n*** [11/13] prod: vps-version anpassen\n";
                $this->_systemSshVpsWithSubSections("tag-checkout vps-use --version=$vpsVersion", 'production');

                echo "\n\n*** [12/13] prod: web tag switchen\n";
                $this->_systemSshVpsWithSubSections("tag-checkout web-switch --version=$webVersion", 'production');

                echo "\n\n*** [13/13] prod: update ausführen\n";
                $this->_systemSshVpsWithSubSections("update", 'production');
            } else {

                echo "\n\n*** [12/13] prod: production branches erstellen\n";

                Vps_Util_Git::vps()->productionBranch('production/'.$appId, $stagingVps);
                Vps_Util_Git::web()->productionBranch('production', $stagingWeb);

                $this->_systemSshVpsWithSubSections("scp-vps --file=".escapeshellarg('Vps/Util/Git.php'), 'production');
                $this->_systemSshVpsWithSubSections("scp-vps --file=".escapeshellarg('Vps/Controller/Action/Cli/GitController.php'), 'production');

                echo "\n\n*** [13/13] prod: updaten\n";
                $this->_systemSshVpsWithSubSections("git checkout-production", 'production');
            }

            $projectIds = Vps_Model_Abstract::getInstance('Vps_Util_Model_Projects')
                                ->getApplicationProjectIds();
            if ($projectIds && !$useSvn) {
                $m = Vps_Model_Abstract::getInstance('Vps_Util_Model_Todo');
                $s = $m->select()
                        ->whereEquals('project_id', $projectIds)
                        ->whereNotEquals('status', 'prod');
                foreach ($m->getRows($s) as $todo) {
                    if (!$todo->done_revision) continue;

                    if (Vps_Util_Git::web()->getActiveBranchContains($todo->done_revision)
                        || Vps_Util_Git::vps()->getActiveBranchContains($todo->done_revision)
                    ) {
                        $todo->status = 'prod';
                        $todo->prod_date = date('Y-m-d');
                        $todo->save();
                        echo "\ntodo #{$todo->id} ({$todo->title}) als auf prod markiert";
                        $doneTodos[] = $todo;
                    }
                }
                echo "\n";
            }

            echo "\n";
            $cfg = Vps_Registry::get('config');
            if (isset($_SERVER['USER'])) {
                $user = ucfirst($_SERVER['USER']);
            } else {
                $user = 'Jemand';
            }
            $msg = "$user hat soeben {$cfg->application->name} ";
            if ($useSvn) $msg .= "mit Version $webVersion (Vps $vpsVersion) ";
            $msg .= "online gestellt.\n";
            if (date('w')==5) {
                $msg .= "Und das obwohl heute Freitag ist!\n";
            }
            if ($skipTest) {
                $msg .= "\nUnit-Tests wurden NICHT ausgeführt.";
            } else if (!$testConfig) {
                $msg .= "\nUnit-Tests wurden lokal erfolgreich ausgeführt, Testserver ist keiner verfügbar.";
            } else {
                $msg .= "\nUnit-Tests wurden erfolgreich ausgeführt.";
            }
            if (count($doneTodos)) {
                $msg .= "\n\nFolgende Todos wurden erledigt:";
                foreach ($doneTodos as $todo) {
                    $msg .= "\ntodo #{$todo->id} ({$todo->title})";
                }
            }
            file_put_contents('/www/public/zeiterfassung/irc/messagequeue/'.date('Y-m-d_H:i:s').uniqid(), 'WICHTIG'.$msg);

            Vps_Util_Git::web()->fetch();
            Vps_Util_Git::vps()->fetch();
            $cmd = "cd /www/public/zeiterfassung && php bootstrap.php insert-go-online-log-entry";
            $cmd .= " --applicationId=".escapeshellarg($cfg->application->id);
            $cmd .= " --webBranch=".escapeshellarg(Vps_Util_Git::web()->getActiveBranch());
            $cmd .= " --vpsBranch=".escapeshellarg(Vps_Util_Git::vps()->getActiveBranch());
            $cmd .= " --webVersion=".escapeshellarg(Vps_Util_Git::web()->revParse('origin/production'));
            $cmd .= " --vpsVersion=".escapeshellarg(Vps_Util_Git::vps()->revParse('origin/production/'.$appId));
            if ($this->_getParam('debug')) {
                echo $cmd."\n";
            }
            $this->_systemCheckRet($cmd);
        }

        echo "\n\n\n\033[32mF E R T I G ! ! !\033[0m\n";

        exit;
    }
    private function _hasRevisionInHistory($project, $version, $revision)
    {
        if (!file_exists('.svn')) {
            return false;
        }
        $log = `svn log --xml --revision $revision http://svn/tags/$project/$version`;
        $log = new SimpleXMLElement($log);
        if (count($log->logentry)) return true;
    }

    private function _notifyUser($msg)
    {
        if (isset($_SERVER['USER']) && $_SERVER['USER']=='niko') {
            $msg = Vps_Registry::get('config')->application->name.' Go Online: '.$msg;
            $msg = str_replace(" ", "\ ", utf8_decode($msg));
            system("ssh niko \"export DISPLAY=:0 && /usr/bin/kdialog --passivepopup $msg 10\"");
        }
    }
}
