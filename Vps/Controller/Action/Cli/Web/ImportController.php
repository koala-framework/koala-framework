<?php
class Vps_Controller_Action_Cli_Web_ImportController extends Vps_Controller_Action_Cli_Abstract
{
    private $_useSshVps;
    private $_sshHost;
    private $_sshDir;
    public function indexAction()
    {
        $this->_useSshVps = file_exists('/usr/local/bin/sshvps');
        $localHosts = array(
            'vivid',
            'vivid-test-server'
        );

        $ownConfig = Vps_Registry::get('config');

        $server = $this->_getParam('server');
        if (!$server) {
            if ($ownConfig->server->testOf) {
                $server = $ownConfig->server->testOf;
            } else {
                $server = 'production';
            }
        }
        if (Vps_Setup::getConfigSection() == $server) {
            throw new Vps_ClientException("Von dir selbst importieren ist natuerlich nicht moeglich.");
        }
        if (Vps_Setup::getConfigSection() == 'production') {
            echo "ACHTUNG!!!!\n";
            echo "Du willst auf production importieren, dabei werden alle Daten auf production ueberschrieben.\n";
            echo "Bist du dir wirklich, wirklich sicher?\n";
            echo "[N/j]";
            $stdin = fopen('php://stdin', 'r');
            $input = trim(strtolower(fgets($stdin, 2)));
            fclose($stdin);
            if ($input != 'j' && $input != 'y') {
                exit;
            }
        }

        $config = Vps_Config_Web::getInstance($server);
        if (!$config->server || !$config->server->host) {
            throw new Vps_ClientException("kein server host konfiguriert");
        }
        if (in_array($config->server->host, $localHosts) && !in_array($ownConfig->server->host, $localHosts)) {
            if ($config->server->host != 'vivid') {
                throw new Vps_ClientException("Nur von vivid kann nach online importiert werden");
            }
            $config->server->host = 'intern.vivid-planet.com';
        }
        $this->_sshHost = $config->server->user.'@'.$config->server->host;
        $this->_sshPort = $config->server->port;
        $this->_sshDir = $config->server->dir;

        if ($ownConfig->server->host == $config->server->host) {
            $cmd = "cd {$config->server->dir} && php bootstrap.php import get-update-revision";
        } else if ($this->_useSshVps) {
            $cmd = "sudo -u vps sshvps $this->_sshHost:$this->_sshPort $this->_sshDir import get-update-revision";
        } else {
            $cmd = "ssh -p $this->_sshPort $this->_sshHost ".escapeshellarg("cd $this->_sshDir && php bootstrap.php import get-update-revision");
        }
        if ($this->_getParam('debug')) echo $cmd."\n";
        exec($cmd, $onlineRevision, $ret);
        if ($ret != 0) throw new Vps_ClientException();
        $onlineRevision = implode('', $onlineRevision);

        if (!$onlineRevision) {
            throw new Vps_ClientException("Can't get onlineRevision");
        }

        if (!$this->_getParam('skip-users') && Vps_Registry::get('config')->application->id != 'service') {
            if (Vps_Setup::getConfigSection() == 'production') {
                echo "\nAuf Production wird der user service NICHT importiert, das haette fatale Folgen.\n";
                echo "Moeglicherweise muss 'vps create-users' ausgefuehrt werden\n\n";
            } else {
                $isLocalServiceUrl = preg_match('#^http://[^/]+\\.vivid/#', $config->service->usersAll->url);
                if (!in_array($ownConfig->server->host, $localHosts) && $isLocalServiceUrl) {
                    echo "\nKann user nicht importieren da diese von einem lokalen service sind.\n";
                    echo "Moeglicherweise muss 'vps create-users' ausgefuehrt werden\n\n";
                } else {
                    $this->_copyServiceUsers($server);
                }
            }
        }

        if (!$this->_getParam('skip-files')) {

        if ($config->uploads && $ownConfig->uploads) {
            echo "kopiere uploads...\n";
            if ($ownConfig->server->host == $config->server->host) {
                if ($ownConfig->uploads == $config->uploads) {
                    throw new Vps_ClientException("Uplodas-Pfade für beide Server sind gleich!");
                }
                if (!file_exists($ownConfig->uploads)) {
                    mkdir($ownConfig->uploads);
                }
                $cmd = "rsync --progress --delete --times --exclude=cache/ --recursive {$config->uploads}/ {$ownConfig->uploads}/";
                if ($this->_getParam('debug')) echo "$cmd\n";
                $this->_systemCheckRet($cmd);
            } else if ($this->_useSshVps) {
                $this->_systemSshVps('copy-uploads '.$ownConfig->uploads.'/', $config->uploads);
            } else if ($config->server->host == 'vivid' && !in_array($ownConfig->server->host, $localHosts)) {
                $cmd = "rsync --progress --delete --times --exclude=cache/ --recursive {$this->_sshHost}:{$config->uploads}/ {$ownConfig->uploads}/";
                if ($this->_getParam('debug')) echo "$cmd\n";
                $this->_systemCheckRet($cmd);
            } else {
                $cmd = "rsync -e 'ssh -p $this->_sshPort' --progress --delete --times --exclude=cache/ --recursive {$this->_sshHost}:{$config->uploads}/ {$ownConfig->uploads}/";
                if ($this->_getParam('debug')) echo "$cmd\n";
                $this->_systemCheckRet($cmd);
            }
        }

        if ($config->server->import && $config->server->import->dirs) {
            foreach ($config->server->import->dirs as $dir) {
                echo "importing $dir...\n";
                $ignores = array();
                $ig = trim(`svn propget --recursive svn:ignore $dir`);
                if (substr($ig, 0, strlen($dir))==$dir) $ig = substr($ig, strlen($dir));
                foreach (preg_split("#\n".preg_quote($dir)."#", $ig) as $p) {
                    if (preg_match("#^([^ ]*) - (.*?)$#s", $p, $m)) {
                        foreach (explode("\n", trim($m[2])) as $i) {
                            $ignores[] = $dir.$m[1].'/'.trim($i);
                        }
                    }
                }
                if (!$ignores) continue;
                $this->_importFiles($config, $ownConfig, $ignores);
            }
        }

        }

        if ($this->_getParam('include-cache')) {
            echo "importing cache dirs...\n";
            $includes = array();
            $dirs = Vps_Util_ClearCache::getInstance()
                        ->getCacheDirs(Vps_Util_ClearCache::MODE_IMPORT);
            foreach ($dirs as $d) {
                if (is_dir("application/cache/$d")) {
                    $includes[] = "application/cache/$d/*";
                } else if (is_dir($d)) {
                    $includes[] = $d.'/*';
                }
            }
            if ($includes) {
                $this->_importFiles($config, $ownConfig, $includes);
            }
        }

        exec("echo \"SHOW DATABASES\" | mysql", $existingDatabases);

        $databases = $config->server->databases->toArray();
        if (!$databases) $databases = array('web');
        foreach ($databases as $dbKey) {
            if (!$dbKey) continue;
            try {
                $dbConfig = Vps_Registry::get('dao')->getDbConfig($dbKey);
            } catch (Vps_Dao_Exception $e) {
                echo "ignoriere $dbKey, nicht in lokaler db config vorhanden...\n";
                continue;
            }
            if (!in_array($dbConfig['dbname'], $existingDatabases)) {
                echo "Datenbank {$dbConfig['dbname']} nicht vorhanden, versuche sie zu erstellen...\n";
                system("echo \"CREATE DATABASE \`{$dbConfig['dbname']}\`;\" | mysql", $ret);
                if ($ret != 0) {
                    throw new Vps_ClientException("Kann Datenbank '{$dbConfig['dbname']}' nicht erstellen, bitte manuell anlegen od. config anpassen.");
                }
                echo "OK\n";
            }
            $db = Zend_Registry::get('dao')->getDb($dbKey);

            $mysqlLocalOptions = "--host=$dbConfig[host] ";
            //auskommentiert weil: es muss in ~/.my.cnf ein benutzer der das machen darf eingestellt sein!
            //ansonsten gibt es probleme für das erstellen von triggers, dazu benötigt man SUPER priviliges
            // -> scheiß mysql
            //$mysqlLocalOptions .= "--user={$dbConfig->username} --password={$dbConfig->password} ";


            $tables = $db->fetchCol('SHOW TABLES');

            $cacheTables = Vps_Util_ClearCache::getInstance()->getDbCacheTables();

            if ($config->server->import && $config->server->import->ignoreTables) {
                foreach ($config->server->import->ignoreTables as $t) {
                    if (substr($t, -1) == '*') {
                        foreach ($tables as $table) {
                            if (substr($table, 0, strlen($t)-1) == substr($t, 0, -1)) {
                                $cacheTables[] = $table;
                            }
                        }
                    } else if (in_array($t, $tables)){
                        $cacheTables[] = $t;
                    }
                }
            }

            $keepTables = array();
            if ($config->server->import && $config->server->import->keepTables) {
                foreach ($config->server->import->keepTables as $t) {
                    if (substr($t, -1) == '*') {
                        foreach ($tables as $table) {
                            if (substr($table, 0, strlen($t)-1) == substr($t, 0, -1)) {
                                $keepTables[] = $table;
                            }
                        }
                    } else if (in_array($t, $tables)) {
                        $keepTables[] = $t;
                    }
                }
            }
            if (!$this->_getParam('skip-backup')) {
                echo "erstelle datenbank-backup '$dbKey'...\n";
                $dumpname = $this->_backupDb(array_merge($cacheTables, $keepTables));
                if ($dumpname) {
                    $backupSize = filesize($dumpname);
                    $this->_systemCheckRet("nice bzip2 --fast $dumpname");
                    echo $dumpname.".bz2\n";
                }
            }


            if ($keepTables) {
                echo "erstelle dump fuer KeepTables '$dbKey'...\n";
                $keepTablesDump = tempnam('/tmp', 'importkeep');
                $cmd = "mysqldump --add-drop-table=false --no-create-info=true $mysqlLocalOptions $dbConfig[dbname] ".implode(' ', $keepTables).">> $keepTablesDump";
                if ($this->_getParam('debug')) file_put_contents('php://stderr', "$cmd\n");
                $this->_systemCheckRet($cmd);
            }

            echo "loesche lokale datenbank '$dbKey'...\n";
            $this->_systemCheckRet("echo \"SET foreign_key_checks = 0; DROP DATABASE \`$dbConfig[dbname]\`; SET foreign_key_checks = 1;\" | mysql $mysqlLocalOptions");

            echo "erstelle neue datenbank '$dbKey'...\n";
            $this->_systemCheckRet("echo \"CREATE DATABASE \`$dbConfig[dbname]\`\" | mysql $mysqlLocalOptions");


            echo "importiere datenbank '$dbKey'...\n";
            if ($ownConfig->server->host == $config->server->host) {
                $cmd = "cd {$config->server->dir} && php bootstrap.php import get-db-config --key=$dbKey";
                if ($this->_getParam('debug')) echo "$cmd\n";
                $otherDbConfig = unserialize(`$cmd`);
                $cmd = $this->_getDumpCommand($config, $otherDbConfig, array_merge($cacheTables, $keepTables));
            } else if ($this->_useSshVps) {
                $ignoreTables = '';
                if (!$this->_getParam('include-cache')) {
                    $ignoreTables = implode(',', array_merge($cacheTables, $keepTables));
                    if ($ignoreTables) $ignoreTables = " --ignore-tables=$ignoreTables";
                }
                $cmd = "sudo -u vps sshvps $this->_sshHost:$this->_sshPort $this->_sshDir db-dump --key=$dbKey";
                $cmd .= "$ignoreTables";
                if ($this->_getParam('debug')) $cmd .= " --debug";
                $cmd .= " | gunzip";
            } else {
                $cmd = "ssh -p $this->_sshPort $this->_sshHost ".escapeshellarg("cd $this->_sshDir && php bootstrap.php import get-db-config --key=$dbKey");
                if ($this->_getParam('debug')) echo "$cmd\n";
                $otherDbConfig = unserialize(`$cmd`);
                $cmd = $this->_getDumpCommand($config, $otherDbConfig, array_merge($cacheTables, $keepTables));
                $cmd = "ssh -p $this->_sshPort $this->_sshHost ".escapeshellarg($cmd);
            }
            $descriptorspec = array(
                1 => array("pipe", "w")
            );
            if ($this->_getParam('debug')) file_put_contents('php://stderr', "$cmd\n");
            $procDump = new Vps_Util_Proc($cmd, $descriptorspec);

            $cmd = "mysql $mysqlLocalOptions --default-character-set=utf8 $dbConfig[dbname]";
            $descriptorspec = array(
                0 => array("pipe", "r")
            );
            $procImport = new Vps_Util_Proc($cmd, $descriptorspec);

            $progress = false;
            if (isset($backupSize) && $backupSize) {
                $c = new Zend_ProgressBar_Adapter_Console();
                $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                        Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                        Zend_ProgressBar_Adapter_Console::ELEMENT_ETA,
                                        Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
                $progress = new Zend_ProgressBar($c, 0, $backupSize);
            }
            $size = 0;
            while (!feof($procDump->pipe(1))) {
                $buffer = fgets($procDump->pipe(1), 4096);
                fputs($procImport->pipe(0), $buffer);
                $size += strlen($buffer);
                if ($progress) $progress->update($size, Vps_View_Helper_FileSize::fileSize($size));
            }
            fclose($procDump->pipe(1));
            fclose($procImport->pipe(0));
            $procImport->close();
            $procDump->close();

            if ($keepTables) {
                echo "spiele KeepTables ein '$dbKey'...\n";
                $cmd = "mysql $mysqlLocalOptions $dbConfig[dbname] < $keepTablesDump";
                if ($this->_getParam('debug')) file_put_contents('php://stderr', "$cmd\n");
                $this->_systemCheckRet($cmd);
                unlink($keepTablesDump);
            }
        }
        echo "\n";


        

        /*
        echo "importiere logs...\n";
        if ($ownConfig->server->host == $config->server->host) {
            $cmd = "cd {$config->server->dir} && php bootstrap.php import get-logs | tar xzm";
        } else {
            $cmd = "sudo -u vps sshvps $this->_sshHost:$this->_sshPort $this->_sshDir import get-logs | tar xzm";
        }
        if ($this->_getParam('debug')) echo $cmd."\n";
        $this->_systemCheckRet($cmd);
        echo "\n";
        */

        if ($ownConfig->server->import && !$ownConfig->server->import->ignoreRrd) {
            echo "importiere rrds...\n";
            if ($ownConfig->server->host == $config->server->host) {
                $cmd = "cd {$config->server->dir} && php bootstrap.php import get-rrd";
            } else if ($this->_useSshVps) {
                $cmd = "sudo -u vps sshvps $this->_sshHost:$this->_sshPort $this->_sshDir import get-rrd";
            } else {
                $cmd = "ssh -p $this->_sshPort $this->_sshHost ".escapeshellarg("cd $this->_sshDir && php bootstrap.php import get-rrd");
            }
            if ($this->_getParam('debug')) echo $cmd."\n";
            $data = unserialize(`$cmd`);
            if (!is_array($data)) {
                throw new Vps_Exception("importing rrd failed, invalid data");
            }
            foreach ($data as $file=>$dump) {
                echo "   $file\n";
                $f = tempnam('/tmp', 'rrdimport');
                file_put_contents($f, $dump);
                $cmd = "gunzip -c $f > $f.xml";
                if ($this->_getParam('debug')) echo $cmd."\n";
                $this->_systemCheckRet($cmd);
                if (file_exists($file)) {
                    $backupDir = '../backup/';
                    if (!file_exists($backupDir)) mkdir($backupDir);
                    rename($file, $backupDir.$file.'-import-'.date('Y-m-d_H:i:s_').rand(1000,9999));
                }
                $cmd = "LC_ALL=C rrdtool restore $f.xml $file";
                if ($this->_getParam('debug')) echo $cmd."\n";
                $this->_systemCheckRet($cmd);
                unlink($f);
                unlink($f.".xml");
            }
            echo "\n";
        }


        if (!$this->_getParam('include-cache')) {

            echo "schreibe application/update...\n";
            file_put_contents('application/update', $onlineRevision);

            Vps_Controller_Action_Cli_Web_UpdateController::update();
        } else {
            echo "update uebersprungen, da include-cache aktiv\n";
        }

        echo "\n\nfertig!\n";

        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _importFiles($config, $ownConfig, $dirs)
    {

        $includes = array();
        foreach ($dirs as $i) {
            $p = '';
            foreach (explode('/', $i) as $j) {
                $p .= $j.'/';
                $e = trim($p, '/');
                //if (substr($e, -1) == '*') $e .= '*';
                if (!in_array($e, $includes)) {
                    $includes[] = $e;
                }
            }
        }

        if ($ownConfig->server->host == $config->server->host) {
            $cmd  = "cd {$config->server->dir} && ";
            $cmd .= "rsync --omit-dir-times --progress --delete --times --recursive ";
            $cmd .= "--exclude='.svn' ";
            foreach ($includes as $i) {
                $cmd .= "--include='$i' ";
            }
            $cmd .= "--exclude='*' ";
            $cmd .= ". {$ownConfig->server->dir}";
        } else if ($this->_useSshVps) {
            $cmd = "sudo -u vps sshvps $this->_sshHost:$this->_sshPort $this->_sshDir copy-files";
            $cmd .= " --includes=\"".implode(',', $includes)."\"";
            if ($this->_getParam('debug')) $cmd .= " --debug";
        } else {
            $cmd = "rsync -e 'ssh -p $this->_sshPort' --omit-dir-times --progress --delete --times --recursive ";
            $cmd .= "--exclude='.svn' ";
            foreach ($includes as $i) {
                $cmd .= "--include='$i' ";
            }
            $cmd .= "--exclude='*' ";
            $cmd .= "{$this->_sshHost}:{$this->_sshDir} {$ownConfig->server->dir}";
        }
        if ($this->_getParam('debug')) echo $cmd."\n";
        passthru($cmd, $ret);
        if ($ret) {
            throw new Vps_ClientException("");
        }
    }

    private function _copyServiceUsers($server)
    {
        try {
            $db = Vps_Registry::get('db');
        } catch (Exception $e) {
            return;
        }
        if (!$db) return;
        $tables = $db->fetchCol('SHOW TABLES');
        if (!in_array('vps_users', $tables)) return;
        if (!in_array('cache_users', $tables)) return;

        // copy users table
        $targetUrl = Vps_Registry::get('config')->service->usersAll->url;
        $sourceConfig = Vps_Config_Web::getInstance($server);
        $sourceUrl = $sourceConfig->service->usersAll->url;

        if ($targetUrl == $sourceUrl) return;

        $sourceModel = new Vps_Model_Service(array('serverUrl' => $sourceUrl, 'timeout' => 120));
        $targetModel = new Vps_Model_Service(array('serverUrl' => $targetUrl, 'timeout' => 120));

        echo "importiere service...\nsource=$sourceUrl target=$targetUrl\n";

        if (strpos($targetUrl, 'http://service.vivid-planet.com') !== false) {
            echo "Service: !!! ACHTUNG !!! Service Import verhindert, nach online wird nicht importiert!!!\n";
            return;
        }

        echo "importiere users tabelle...\n";

        $targetModel->copyDataFromModel($sourceModel, null, array('replace' => true));

        // copy users_to_web
        $targetUrl = Vps_Registry::get('config')->service->usersRelation->url;
        $sourceConfig = Vps_Config_Web::getInstance($server);
        $sourceUrl = $sourceConfig->service->usersRelation->url;
        if ($targetUrl == $sourceUrl) return;

        $sourceModel = new Vps_Model_Service(array('serverUrl' => $sourceUrl));
        $targetModel = new Vps_Model_Service(array('serverUrl' => $targetUrl));

        echo "importiere Produktiv-Benutzerzuweisungen...\n";
        $importSelect = $sourceModel->select();
        $importSelect->whereEquals('web_id', Vps_Registry::get('config')->application->id);
        $targetModel->deleteRows($importSelect);
        $targetModel->copyDataFromModel($sourceModel, $importSelect, array('ignorePrimaryKey' => true));
    }

    private function _systemSshVps($cmd, $dir = null)
    {
        if (!$dir) $dir = $this->_sshDir;
        $cmd = "sshvps $this->_sshHost:$this->_sshPort $dir $cmd";
        $cmd = "sudo -u vps $cmd";
        if ($this->_getParam('debug')) {
            $cmd .= " --debug";
            echo $cmd."\n";
        }
        return $this->_systemCheckRet($cmd);
    }
    public static function getHelp()
    {
        return "import uploads+database";
    }
    public static function getHelpOptions()
    {
        $ret = array(
            array(
                'param'=> 'server',
                'value'=> self::_getConfigSections(),
                'allowBlank' => true,
                'help' => 'what to import'
            )
        );
        $ret[] = array('param' => 'include-cache');
        $ret[] = array('param' => 'skip-users');
        return $ret;
    }

    public function backupDbAction()
    {
        exec('which mysqldump', $out, $ret);
        if ($ret) {
            echo "mysqldump nicht gefunden, ES WIRD KEIN DB BACKUP ERSTELLT!!\n";
        } else {
            echo "erstelle backup...\n";
            $dumpname = $this->_backupDb(Vps_Util_ClearCache::getInstance()->getDbCacheTables());
            if ($dumpname) {
                $this->_systemCheckRet("nice bzip2 --fast $dumpname");
                echo $dumpname.".bz2";
                echo "\n";
            } else {
                echo "uebersprungen...\n";
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _getDumpCommand($config, $dbConfig, array $cacheTables)
    {
        $ret = '';

        $mysqlOptions = "--host=$dbConfig[host] --user=$dbConfig[username] --password=$dbConfig[password] ";

        $mysqlDir = '';
        if ($config->server->host == 'vivid-planet.com') {
            $mysqlDir = '/usr/local/mysql/bin/';
        }

        $ignoreTables = '';
        foreach ($cacheTables as $t) {
            $ignoreTables .= " --ignore-table=$dbConfig[dbname].{$t}";
        }
        $ret = "{ {$mysqlDir}mysqldump{$ignoreTables} $mysqlOptions $dbConfig[dbname]";
        foreach ($cacheTables as $t) {
            $ret .= " && {$mysqlDir}mysqldump --no-data $mysqlOptions $dbConfig[dbname] $t";
        }
        $ret .= "; }";

        return $ret;
    }

    private function _backupDb($ignoreTables)
    {
        if (file_exists("/var/backups/vpsimport/")) {
            $dumpname = "/var/backups/vpsimport/";
        } else {
            $dumpname = getcwd().'/../backup/';
            if (!file_exists($dumpname)) mkdir($dumpname);
        }

        try {
            $db = Zend_Registry::get('db');
        } catch (Exception $e) {
            return null;
        }
        if (!$db) return null;
        $dbConfig = $db->getConfig();
        $dumpname .= date("Y-m-d_H:i:s_U")."_$dbConfig[dbname].sql";
        $cmd = $this->_getDumpCommand(Vps_Registry::get('config'), $dbConfig, $ignoreTables)." > $dumpname";
        $this->_systemCheckRet($cmd);

        return $dumpname;
    }

    public function getUpdateRevisionAction()
    {
        if (file_exists('application/update')) {
            echo file_get_contents('application/update');
        } else {
            try {
                $info = new SimpleXMLElement(`svn info --xml`);
                $onlineRevision = (int)$info->entry['revision'];
            } catch (Exception $e) {}
            if (!$onlineRevision) {
                throw new Vps_ClientException("Can't detect online revision");
            }
            echo $onlineRevision;
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function getDbConfigAction()
    {
        echo serialize(Zend_Registry::get('dao')->getDbConfig($this->_getParam('key')));
        exit;
    }

    public function getLogsAction()
    {
        $cmd = "tar cz application/log/*/*";
        $this->_systemCheckRet($cmd);
        exit;
    }

    public function getRrdAction()
    {
        $out = array();
        foreach (Vps_Registry::get('config')->rrd as $k=>$n) {
            $rrd = new $n;
            $file = $rrd->getFileName();
            if (file_exists($file)) {
                $out[$file] = `LC_ALL=C rrdtool dump $file | gzip`;
            }
        }
        echo serialize($out);
        exit;
    }
}
