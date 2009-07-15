<?php
class Vps_Controller_Action_Cli_ImportController extends Vps_Controller_Action_Cli_Abstract
{
    public function indexAction()
    {
        $ownConfig = Vps_Registry::get('config');

        $config = Vps_Config_Web::getInstance($this->_getParam('server'));
        if (!$config->server || !$config->server->host) {
            throw new Vps_ClientException("kein server konfiguriert");
        }
        $this->_sshHost = $config->server->user.'@'.$config->server->host;
        $this->_sshDir = $config->server->dir;

        if ($ownConfig->server->host == $config->server->host) {
            $cmd = "cd {$config->server->dir} && php bootstrap.php import get-update-revision";
        } else if (file_exists('/usr/local/bin/sshvps')) {
            $cmd = "sudo -u vps sshvps $this->_sshHost $this->_sshDir import get-update-revision";
        } else {
            $cmd = "ssh $this->_sshHost ".escapeshellarg("cd $this->_sshDir && php bootstrap.php import get-update-revision");
        }
        if ($this->_getParam('debug')) echo $cmd."\n";
        exec($cmd, $onlineRevision, $ret);
        if ($ret != 0) throw new Vps_ClientException();
        $onlineRevision = implode('', $onlineRevision);

        if (!$onlineRevision) {
            throw new Vps_ClientException("Can't get onlineRevision");
        }

        try {
            Vps_Registry::get('db');
        } catch (Zend_Db_Adapter_Exception $e) {
            $dbConfig = Vps_Registry::get('dao')->getDbConfig();
            $dbConfig['dbname'] = 'test';
            $db = Zend_Db::factory('PDO_MYSQL', $dbConfig);
            $databases = array();
            foreach ($db->query("SHOW DATABASES")->fetchAll() as $r) {
                $databases[] = $r['Database'];
            }

            $dbConfig = Vps_Registry::get('dao')->getDbConfig();
            if (!in_array($dbConfig['dbname'], $databases)) {
                echo "Datenbank {$dbConfig['dbname']} nicht vorhanden, versuche sie zu erstellen...\n";
                $db->query("CREATE DATABASE {$dbConfig['dbname']}");
                echo "OK\n";
            }
        }

        if (Vps_Registry::get('config')->application->id != 'service') {
            $this->_copyServiceUsers();
        }

        if ($config->uploads && $ownConfig->uploads) {
            echo "kopiere uploads...\n";
            if ($ownConfig->server->host == $config->server->host) {
                if ($ownConfig->uploads == $config->uploads) {
                    throw new Vps_ClientException("Uplodas-Pfade für beide Server sind gleich!");
                }
                $cmd = "rsync --progress --delete --times --exclude=cache/ --recursive {$config->uploads} {$ownConfig->uploads}";
                if ($this->_getParam('debug')) echo "$cmd\n";
                $this->_systemCheckRet($cmd);
            } else if (file_exists('/usr/local/bin/sshvps')) {
                $this->_systemSshVps('copy-uploads '.$ownConfig->uploads.'/', $config->uploads);
            } else {
                $cmd = "rsync --progress --delete --times --exclude=cache/ --recursive {$this->_sshHost}:{$config->uploads} {$ownConfig->uploads}";
                if ($this->_getParam('debug')) echo "$cmd\n";
                $this->_systemCheckRet($cmd);
            }
        }

        if ($config->server->import && $config->server->import->dirs) {
            foreach ($config->server->import->dirs as $dir) {
                echo "importing $dir...\n";
                $ig = simplexml_load_string(`svn propget --recursive --xml svn:ignore $dir`);
                $ignores = array();
                foreach ($ig->target as $t) {
                    $p = explode("\n", trim((string)$t->property));
                    foreach ($p as $i) {
                        $ignores[] = (string)$t['path'] . '/' . trim($i);
                    }
                }
                if (!$ignores) continue;

                $includes = array();
                foreach ($ignores as $i) {
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
                } else if (file_exists('/usr/local/bin/sshvps')) {
                    $cmd = "sudo -u vps sshvps $this->_sshHost $this->_sshDir copy-files";
                    $cmd .= " --includes=\"".implode(',', $includes)."\"";
                    if ($this->_getParam('debug')) $cmd .= " --debug";
                } else {
                    $cmd = "rsync --omit-dir-times --progress --delete --times --recursive ";
                    $cmd .= "--exclude='.svn' ";
                    foreach ($includes as $i) {
                        $cmd .= "--include='$i' ";
                    }
                    $cmd .= "--exclude='*' ";
                    $cmd .= "{$this->_sshHost}:{$this->_sshDir} {$ownConfig->server->dir}";
                }
                if ($this->_getParam('debug')) echo $cmd."\n";
                passthru($cmd);
            }
        }


        if (Zend_Registry::get('db')) {
            $dbConfig = Zend_Registry::get('db')->getConfig();

            $mysqlLocalOptions = "--host=$dbConfig[host] ";
            //auskommentiert weil: es muss in ~/.my.cnf ein benutzer der das machen darf eingestellt sein!
            //ansonsten gibt es probleme für das erstellen von triggers, dazu benötigt man SUPER priviliges
            // -> scheiß mysql
            //$mysqlLocalOptions .= "--user={$dbConfig->username} --password={$dbConfig->password} ";


            echo "erstelle datenbank-backup...\n";

            $tables = Zend_Registry::get('db')->fetchCol('SHOW TABLES');

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
                    } else {
                        $keepTables[] = $t;
                    }
                }
            }

            $dumpname = $this->_backupDb(array_merge($cacheTables, $keepTables));
            $backupSize = filesize($dumpname);
            $this->_systemCheckRet("bzip2 --fast $dumpname");
            echo $dumpname.".bz2\n";


            if ($keepTables) {
                echo "erstelle dump fuer KeepTables...\n";
                $keepTablesDump = tempnam('/tmp', 'importkeep');
                $cmd = "mysqldump --add-drop-table=false --no-create-info=true $mysqlLocalOptions $dbConfig[dbname] ".implode(' ', $keepTables).">> $keepTablesDump";
                if ($this->_getParam('debug')) file_put_contents('php://stderr', "$cmd\n");
                $this->_systemCheckRet($cmd);
            }

            echo "loesche lokale datenbank...\n";
            $this->_systemCheckRet("echo \"DROP DATABASE \`$dbConfig[dbname]\`\" | mysql $mysqlLocalOptions");

            echo "erstelle neue datenbank...\n";
            $this->_systemCheckRet("echo \"CREATE DATABASE \`$dbConfig[dbname]\`\" | mysql $mysqlLocalOptions");


            echo "importiere datenbank...\n";
            if ($ownConfig->server->host == $config->server->host) {
                $otherDbConfig = unserialize(`cd {$config->server->dir} && php bootstrap.php import get-db-config`);
                $cmd = $this->_getDumpCommand($otherDbConfig, array_merge($cacheTables, $keepTables));
            } else if (file_exists('/usr/local/bin/sshvps')) {
                $ignoreTables = '';
                if (!$this->_getParam('include-cache')) {
                    $ignoreTables = implode(',', array_merge($cacheTables, $keepTables));
                    if ($ignoreTables) $ignoreTables = " --ignore-tables=$ignoreTables";
                }
                $cmd = "sudo -u vps sshvps $this->_sshHost $this->_sshDir db-dump";
                $cmd .= "$ignoreTables";
                if ($this->_getParam('debug')) $cmd .= " --debug";
                $cmd .= " | gunzip";
            } else {
                $cmd = "ssh $this->_sshHost ".escapeshellarg("cd $this->_sshDir && php bootstrap.php import get-db-config");
                if ($this->_getParam('debug')) echo "$cmd\n";
                $otherDbConfig = unserialize(`$cmd`);
                $cmd = $this->_getDumpCommand($otherDbConfig, array_merge($cacheTables, $keepTables));
                $cmd = "ssh $this->_sshHost ".escapeshellarg($cmd);
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

            $c = new Zend_ProgressBar_Adapter_Console();
            $c->setElements(array(Zend_ProgressBar_Adapter_Console::ELEMENT_PERCENT,
                                    Zend_ProgressBar_Adapter_Console::ELEMENT_BAR,
                                    Zend_ProgressBar_Adapter_Console::ELEMENT_ETA,
                                    Zend_ProgressBar_Adapter_Console::ELEMENT_TEXT));
            $progress = new Zend_ProgressBar($c, 0, $backupSize);
            $size = 0;
            while (!feof($procDump->pipe(1))) {
                $buffer = fgets($procDump->pipe(1), 4096);
                fputs($procImport->pipe(0), $buffer);
                $size += strlen($buffer);
                $progress->update($size, Vps_View_Helper_FileSize::fileSize($size));
            }
            fclose($procDump->pipe(1));
            fclose($procImport->pipe(0));
            $procImport->close();
            $procDump->close();

            if ($keepTables) {
                echo "spiele KeepTables ein...\n";
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
            $cmd = "sudo -u vps sshvps $this->_sshHost $this->_sshDir import get-logs | tar xzm";
        }
        if ($this->_getParam('debug')) echo $cmd."\n";
        $this->_systemCheckRet($cmd);
        echo "\n";
        */

        echo "importiere rrds...\n";
        if ($ownConfig->server->host == $config->server->host) {
            $cmd = "cd {$config->server->dir} && php bootstrap.php import get-rrd";
        } else if (file_exists('/usr/local/bin/sshvps')) {
            $cmd = "sudo -u vps sshvps $this->_sshHost $this->_sshDir import get-rrd";
        } else {
            $cmd = "ssh $this->_sshHost ".escapeshellarg("cd $this->_sshDir && php bootstrap.php import get-rrd");
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
                rename($file, $file.'-'.date('Y-m-DH:i:s'));
            }
            $cmd = "LC_ALL=C rrdtool restore $f.xml $file";
            if ($this->_getParam('debug')) echo $cmd."\n";
            $this->_systemCheckRet($cmd);
            unlink($f);
            unlink($f.".xml");
        }
        echo "\n";


        if (!$this->_getParam('include-cache')) {

            echo "schreibe application/update...\n";
            file_put_contents('application/update', $onlineRevision);

            Vps_Controller_Action_Cli_UpdateController::update();
        } else {
            echo "update uebersprungen, da include-cache aktiv\n";
        }

        echo "\n\nfertig!\n";

        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _copyServiceUsers()
    {
        if (!Vps_Registry::get('db')) return;
        $tables = Vps_Registry::get('db')->fetchCol('SHOW TABLES');
        if (!in_array('vps_users', $tables)) return;
        if (!in_array('cache_users', $tables)) return;

        // copy users table
        $targetUrl = $this->_getConfig('web')->service->usersAll->url;
        $sourceConfig = $this->_getConfig('production');
        $sourceUrl = $sourceConfig->service->usersAll->url;
        if ($targetUrl == $sourceUrl) return;

        $sourceModel = new Vps_Model_Service(array('serverUrl' => $sourceUrl));
        $targetModel = new Vps_Model_Service(array('serverUrl' => $targetUrl));

        echo "\n*** Service: source=$sourceUrl target=$targetUrl\n";
        echo "Service: Kopiere 'users' tabelle (neu)...\n";

        $targetModel->copyDataFromModel($sourceModel);

        // copy users_to_web
        $targetUrl = $this->_getConfig('web')->service->usersRelation->url;
        $sourceConfig = $this->_getConfig('production');
        $sourceUrl = $sourceConfig->service->usersRelation->url;
        if ($targetUrl == $sourceUrl) return;

        $sourceModel = new Vps_Model_Service(array('serverUrl' => $sourceUrl));
        $targetModel = new Vps_Model_Service(array('serverUrl' => $targetUrl));

        echo "Service: Fuege Produktiv-Benutzerzuweisungen hinzu (neu)...\n";
        $importSelect = $sourceModel->select();
        $importSelect->whereEquals('web_id', Vps_Registry::get('config')->application->id);
        $targetModel->deleteRows($importSelect);
        $targetModel->copyDataFromModel($sourceModel, $importSelect);
    }

    private function _getConfig($type = 'web')
    {
        if ($type == 'web') {
            return Vps_Registry::get('config');
        } else if ($type == 'production') {
            $webConfig = new Zend_Config_Ini('application/config.ini', $this->_getParam('server'));
            $vpsConfig = new Zend_Config_Ini(VPS_PATH.'/config.ini', $this->_getParam('server'),
                            array('allowModifications'=>true));
            $vpsConfig->merge($webConfig);
            return $vpsConfig;
        }
        return null;
    }

    private function _systemSshVps($cmd, $dir = null)
    {
        if (!$dir) $dir = $this->_sshDir;
        $cmd = "sshvps $this->_sshHost $dir $cmd";
        $cmd = "sudo -u vps $cmd";
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
                'valueOptional' => true,
                'help' => 'what to import'
            )
        );
        $ret[] = array('param' => 'include-cache');
        return $ret;
    }

    public function backupDbAction()
    {
        echo "erstelle backup...\n";
        $dumpname = $this->_backupDb(Vps_Util_ClearCache::getInstance()->getDbCacheTables());
        $this->_systemCheckRet("bzip2 --fast $dumpname");
        echo $dumpname.".bz2";
        echo "\n";
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _getDumpCommand($dbConfig, array $cacheTables)
    {
        $ret = '';

        $mysqlOptions = "--host=$dbConfig[host] --user=$dbConfig[username] --password=$dbConfig[password] ";
        $config = Zend_Registry::get('config');

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

        $dbConfig = Zend_Registry::get('db')->getConfig();
        $dumpname .= date("Y-m-d_H:i:s_U")."_$dbConfig[dbname].sql";
        $cmd = $this->_getDumpCommand($dbConfig, $ignoreTables)." > $dumpname";
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
        echo serialize(Zend_Registry::get('db')->getConfig());
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
