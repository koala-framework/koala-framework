<?php
class Vps_Controller_Action_Cli_Web_SetupOnlineController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "setup online";
    }

    public static function getHelpOptions()
    {
        $ret = array();
        $ret[] = array(
            'param' => 'server',
            'value' => 'vivid-test-server,test,production',
            'valueOptional' => true
        );
        $ret[] = array('param' => 'debug');
        return $ret;
    }

    private function _systemSshVps($config, $cmd)
    {
        $sshHost = $config->server->user.'@'.$config->server->host;
        $sshDir = $config->server->dir;

        $cmd = "ssh -p {$config->server->port} $sshHost ".escapeshellarg("cd $sshDir && ".Vps_Util_Git::getAuthorEnvVars()." $cmd");
        if ($this->_getParam('debug')) {
            echo $cmd."\n";
        }
        return $this->_systemCheckRet($cmd);
    }

    private function _execSql($config, $sql)
    {
        $sshHost = $config->server->user.'@'.$config->server->host;
        $sshDir = $config->server->dir;
        $cmd = "echo ".escapeshellarg($sql)." | mysql";
        $cmd = "ssh -p {$config->server->port} $sshHost ".escapeshellarg("$cmd");
        $ret = null;
        if ($this->_getParam('debug')) echo "$cmd\n";
        system($cmd, $ret);
        return !$ret;
    }

    public function indexAction()
    {
        if (Vps_Registry::get('config')->server->host != 'vivid') {
            throw new Vps_Exception("setup-online may only be called on local vivid server");
        }

        $servers = explode(',', $this->_getParam('server'));
        $setupVividTestServer = false;
        foreach ($servers as $server) {
            $config = Vps_Config_Web::getInstance($server);
            $sshHost = $config->server->user.'@'.$config->server->host;
            $sshDir = $config->server->dir;
            $cmd = "if [ -d $sshDir ]; then echo \"yes\"; else echo \"no\"; fi";
            $cmd = "ssh -p {$config->server->port} $sshHost ".escapeshellarg($cmd);
            if ($this->_getParam('debug')) echo "$cmd\n";
            if (trim(exec($cmd)) != "yes") {
                if ($server != 'vivid-test-server') {
                    throw new Vps_Exception_Client("$sshDir ist nicht vorhanden.");
                } else {
                    $setupVividTestServer = true;
                    continue;
                }
            }
            $cmd = "ssh -p {$config->server->port} $sshHost ".escapeshellarg("cd $sshDir && ls -l");
            if ($this->_getParam('debug')) echo "$cmd\n";
            $l = trim(exec($cmd));
            if ($l != "total 0" && $l != "insgesamt 0") {
                throw new Vps_Exception_Client("$server ist nicht leer, bitte alle Dateien löschen.");
            }
        }

        if ($setupVividTestServer) {
            $config = Vps_Config_Web::getInstance('vivid-test-server');
            $sshHost = $config->server->user.'@'.$config->server->host;
            $args = array(
                $config->application->id,
                $config->server->dir,
                $config->server->domain
            );
            $cmd  = "sudo /usr/local/bin/setup-web";
            foreach ($args as $a) {
                $cmd .= " ".escapeshellarg($a);
            }
            $cmd = "ssh -p {$config->server->port} $sshHost ".escapeshellarg($cmd);
            if ($this->_getParam('debug')) echo "$cmd\n";
            $this->_systemCheckRet($cmd);
        }


        $servers = explode(',', $this->_getParam('server'));
        foreach ($servers as $server) {
            $config = Vps_Config_Web::getInstance($server);

            if ($server != 'vivid-test-server' && in_array('web', $config->server->databases->toArray())) {
                if (!isset($dbPassword)) {
                    $filter = new Vps_Filter_Random(16);
                    $dbPassword = $filter->filter('');
                    $dbUser = Vps_Registry::get('config')->application->id;
                    while (strlen($dbUser) > 16) {
                        echo "Kann MySQL Benutzername $dbUser ist laenger als 16 Zeichen (".strlen($dbUser)."),\n";
                        echo "darf jedoch max. 16 Zeichen haben, neuer Name: ";
                        $stdin = fopen('php://stdin', 'r');
                        $dbUser = trim(strtolower(fgets($stdin, 128)));
                        fclose($stdin);
                    }
                    $createSql = "CREATE USER '$dbUser'@'localhost' IDENTIFIED BY '$dbPassword'";
                    if (!$this->_execSql($config, $createSql)) {
                        echo "Kann Benutzer $dbUser nicht anlegen, er existiert\n";
                        echo "moeglicherweise bereits.\n";
                        echo "* (l)oeschen und neu anlegen\n";
                        echo "* bestehenden verwenden und (p)asswort eingeben\n";
                        $stdin = fopen('php://stdin', 'r');
                        $input = trim(strtolower(fgets($stdin, 2)));
                        fclose($stdin);
                        if ($input == 'l') {
                            $this->_execSql($config, "DROP USER '$dbUser'@'localhost'");
                            $createSql = "CREATE USER '$dbUser'@'localhost' IDENTIFIED BY '$dbPassword'";
                            if (!$this->_execSql($config, $createSql)) {
                                throw new Vps_ClientException("Kann Benutzer nicht anlegen");
                            }
                        } else if ($input == 'p') {
                            $stdin = fopen('php://stdin', 'r');
                            echo "enter password: ";
                            $dbPassword = trim(strtolower(fgets($stdin, 128)));
                            fclose($stdin);
                        } else {
                            throw new Vps_ClientException("unbekannte option");
                        }
                    }

                    $dbName = Vps_Registry::get('config')->application->id;
                    $sql = "GRANT ALL PRIVILEGES ON `$dbName%` . * TO '$dbUser'@'localhost'";
                    $cmd = "echo ".escapeshellarg($sql)." | mysql";
                    $cmd = "ssh -p {$config->server->port} $sshHost ".escapeshellarg("$cmd");
                    if ($this->_getParam('debug')) echo "$cmd\n";
                    system($cmd, $ret);
                    if ($ret) {
                        throw new Vps_ClientException("Konnte berechtigungen nicht setzen");
                    }
                }
            }

            echo "\n$server: [1/9] git clone web\n";
            $cmd = "git clone ";
            $cmd .= "ssh://vivid@git.vivid-planet.com/git/".Vps_Registry::get('config')->application->id." wc && mv wc/* . && mv wc/.??* . && rmdir wc";
            $this->_systemSshVps($config, $cmd);

            echo "\n$server: [2/9] git clone vps-lib\n";
            $cmd = "git clone ";
            $cmd .= "ssh://vivid@git.vivid-planet.com/git/vps vps-lib";
            $this->_systemSshVps($config, $cmd);

            echo "\n$server: [2.3/9] git checkout branch\n";
            $branch = trim(file_get_contents('application/vps_branch'));
            $cmd = "cd vps-lib && git checkout -b $branch origin/$branch";
            $this->_systemSshVps($config, $cmd);

            echo "\n$server: [2.5/9] set vps-lib/include_path\n";
            $path = "{$config->libraryPath}/zend/%version%";
            $cmd = "echo -n ".escapeshellarg($path)." > vps-lib/include_path";
            $this->_systemSshVps($config, $cmd);

            if ($config->uploads) {
                echo "\n$server: [4/9] create uploads\n";
                $cmd = "mkdir {$config->uploads}";
                try {
                    $this->_systemSshVps($config, $cmd);
                } catch (Exception $e) {}
            }

            echo "\n$server: [5/9] create config.db.ini\n";
            
            if (!in_array('web', $config->server->databases->toArray())) {
                echo "(uebersprungen) kein server.databases[] = web eintrag in config\n";
            } else {
                $dbConfig = array();
                $dbConfig[] = "web.host = localhost";
                if ($server == 'vivid-test-server') {
                    $dbConfig[] = "web.username = root";
                    $dbConfig[] = "web.password = ";
                } else {
                    $dbConfig[] = "web.username = $dbUser";
                    $dbConfig[] = "web.password = $dbPassword";
                }
                $dbName = Vps_Registry::get('config')->application->id;
                if ($server != 'production' && $server != 'vivid-test-server') {
                    $dbName .= "_$server";
                }
                $dbConfig[] = "web.dbname = $dbName";
                $cmd = "echo \"[database]\" > application/config.db.ini";
                foreach ($dbConfig as $line) {
                    $cmd .= " && echo \"$line\" >> application/config.db.ini";
                }
                $this->_systemSshVps($config, $cmd);
            }

            echo "\n$server: [6/9] set permissions\n";
            $this->_systemSshVps($config, "chmod a+w application/cache/*");
            $this->_systemSshVps($config, "chmod a+w application/temp");
            $this->_systemSshVps($config, "chmod a+w application/log");
            $this->_systemSshVps($config, "chmod a+w application/log/*");
            if ($config->uploads) {
                $this->_systemSshVps($config, "chmod a+w $config->uploads");
            }

            echo "\n$server: [7/9] set mysql file rights\n";
            // globale file rechte für csv import setzen
            if ($server == 'vivid-test-server') {
                echo "skipped for vivid-test-server - root user has all rights\n";
            } else {
                if (in_array('web', $config->server->databases->toArray())) {
                    $cmd = "php bootstrap.php setup-online set-mysql-file-right --user=$dbUser";
                    $this->_systemSshVps($config, $cmd);
                }
            }

            echo "\n$server: [8/9] import\n";
            $cmd = "php bootstrap.php import --skip-backup --server=".Vps_Setup::getConfigSection();
            if (!$this->_getParam('debug')) $cmd .= " --debug";
            $this->_systemSshVps($config, $cmd);

            echo "\n$server: [9/9] create-users\n";
            if (!in_array('vps', $config->server->updateTags->toArray())) {
                echo "(uebersprungen) kein vps updateTag in config\n";
            } else {
                $cmd = "php bootstrap.php create-users";
                if (!$this->_getParam('debug')) $cmd .= " --debug";
                $this->_systemSshVps($config, $cmd);
            }
        }

        exit(0);
    }

    public function setMysqlFileRightAction()
    {
        $dbUser = $this->_getParam('user');
        if (!$dbUser) {
            throw new Vps_Exception("--user=foo must be set!");
        }
        Vps_Util_Mysql::grantFileRight($dbUser);
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
