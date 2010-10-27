<?php
class Vps_Controller_Action_Cli_SetupOnlineController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        if (!Vps_Controller_Action_Cli_TagController::getProjectName()) return null;
        if (Vps_Registry::get('config')->server->host != 'vivid') return null;
        return "setup online";
    }

    public static function getHelpOptions()
    {
        $ret = array();
        $ret[] = array(
            'param' => 'server',
            'value' => 'test,production',
            'valueOptional' => true
        );
        $ret[] = array('param' => 'debug');
        return $ret;
    }

    private function _systemSshVps($config, $cmd)
    {
        $sshHost = $config->server->user.'@'.$config->server->host;
        $sshDir = $config->server->dir;

        $cmd = "ssh $sshHost ".escapeshellarg("cd $sshDir && $cmd");
        if ($this->_getParam('debug')) {
            echo $cmd."\n";
        }
        return $this->_systemCheckRet($cmd);
    }

    private static function _getProjectPath()
    {
        if (preg_match("#(trunk/vps-projekte/.*)\n#", `svn info`, $m)) {
            return $m[1];
        } else if (preg_match("#(branches/.*)\n#", `svn info`, $m)) {
            return $m[1];
        } else if (preg_match("#(trunk/vw-projekte/.*)\n#", `svn info`, $m)) {
            return $m[1];
        }
        return false;
    }

    private function _execSql($config, $sql)
    {
        $sshHost = $config->server->user.'@'.$config->server->host;
        $sshDir = $config->server->dir;
        $cmd = "echo ".escapeshellarg($sql)." | mysql";
        $cmd = "ssh $sshHost ".escapeshellarg("$cmd");
        $ret = null;
        system($cmd, $ret);
        return !$ret;
    }

    public function indexAction()
    {
        $projectName = Vps_Controller_Action_Cli_TagController::getProjectName();
        $projectPath = self::_getProjectPath();
        $servers = explode(',', $this->_getParam('server'));
        foreach ($servers as $server) {
            $config = Vps_Config_Web::getInstance($server);
            $sshHost = $config->server->user.'@'.$config->server->host;
            $sshDir = $config->server->dir;
            $cmd = "ssh $sshHost ".escapeshellarg("cd $sshDir && ls -l");
            if (trim(exec($cmd)) != "total 0") {
                throw new Vps_Exception_Client("$server ist nicht leer, bitte alle Dateien löschen.");
            }
        }


        $servers = explode(',', $this->_getParam('server'));
        foreach ($servers as $server) {
            $config = Vps_Config_Web::getInstance($server);

            if (!isset($dbPassword)) {
                $filter = new Vps_Filter_Random(16);
                $dbPassword = $filter->filter('');
                $dbUser = $projectName;
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

                $sql = "GRANT ALL PRIVILEGES ON `$projectName%` . * TO '$dbUser'@'localhost'";
                $cmd = "echo ".escapeshellarg($sql)." | mysql";
                $cmd = "ssh $sshHost ".escapeshellarg("$cmd");
                system($cmd, $ret);
                if ($ret) {
                    throw new Vps_ClientException("Konnte berechtigungen nicht setzen");
                }
            }

            $svnBase = 'svn://intern.vivid-planet.com/'; //TODO: ist bei POI anders, in config einstellbar machen

            echo "\n$server: [1/8] svn checkout\n";
            $cmd = "svn co $svnBase/$projectPath .";
            $this->_systemSshVps($config, $cmd);

            echo "\n$server: [2/8] set include_path\n";
            $cmd = "echo \"{$config->libraryPath}/vps/%vps_branch%\" > application/include_path";
            $this->_systemSshVps($config, $cmd);

            if ($config->uploads) {
                echo "\n$server: [3/8] create uploads\n";
                $cmd = "mkdir {$config->uploads}";
                try {
                    $this->_systemSshVps($config, $cmd);
                } catch (Exception $e) {}
            }

            echo "\n$server: [4/8] create include_path\n";
            $cmd = "echo \"{$config->libraryPath}/vps/%vps_branch%\" > application/include_path";
            $this->_systemSshVps($config, $cmd);


            echo "\n$server: [5/8] create config.db.ini\n";

            $dbConfig = array();
            $dbConfig[] = "web.host = localhost";
            $dbConfig[] = "web.username = $dbUser";
            $dbConfig[] = "web.password = $dbPassword";
            $dbName = $projectName;
            if ($server != 'production') $dbName .= "_$server";
            $dbConfig[] = "web.dbname = $dbName";
            $cmd = "echo \"[database]\" > application/config.db.ini";
            foreach ($dbConfig as $line) {
                $cmd .= " && echo \"$line\" >> application/config.db.ini";
            }
            $this->_systemSshVps($config, $cmd);

            echo "\n$server: [6/8] set permissions\n";
            $this->_systemSshVps($config, "chmod a+w application/cache/*");
            $this->_systemSshVps($config, "chmod a+w application/temp");
            $this->_systemSshVps($config, "chmod a+w application/log");
            $this->_systemSshVps($config, "chmod a+w application/log/*");
            $this->_systemSshVps($config, "chmod a+w $config->uploads");

            echo "\n$server: [7/8] import\n";
            $cmd = "php bootstrap.php import --server=".Vps_Setup::getConfigSection();
            if (!$this->_getParam('debug')) $cmd .= " --debug";
            $this->_systemSshVps($config, $cmd);

            echo "\n$server: [8/8] create-users\n";
            $cmd = "php bootstrap.php create-users";
            if (!$this->_getParam('debug')) $cmd .= " --debug";
            $this->_systemSshVps($config, $cmd);
        }

        exit(0);
    }
}
