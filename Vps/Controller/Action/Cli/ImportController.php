<?php
class Vps_Controller_Action_Cli_ImportController extends Vps_Controller_Action_Cli_Abstract
{
    public function indexAction()
    {
        $ownConfig = Vps_Registry::get('config');

        $dbConfig = Zend_Registry::get('db')->getConfig();

        $mysqlLocalOptions = "--host=$dbConfig[host] ";
        //auskommentiert weil: es muss in ~/.my.cnf ein benutzer der das machen darf eingestellt sein!
        //ansonsten gibt es probleme für das erstellen von triggers, dazu benötigt man SUPER priviliges
        // -> scheiß mysql
        //$mysqlLocalOptions .= "--user={$dbConfig->username} --password={$dbConfig->password} ";

        $server = $this->_getParam('server');
        $config = new Zend_Config_Ini('application/config.ini', $server);
        if (!$config->server || !$config->server->host) {
            throw new Vps_ClientException("kein server konfiguriert");
        }
        $this->_sshHost = $config->server->user.'@'.$config->server->host;
        $this->_sshDir = $config->server->dir;

        if (Vps_Registry::get('config')->application->id != 'service') {
            $this->_copyServiceUsers();
        }

        if ($ownConfig->server->host == $config->server->host) {
            $cmd = "cd {$config->server->dir} && php bootstrap.php import get-update-revision";
        } else {
            $cmd = "sudo -u vps sshvps $this->_sshHost $this->_sshDir import get-update-revision";
        }
        if ($this->_getParam('debug')) echo $cmd."\n";
        exec($cmd, $onlineRevision, $ret);
        if ($ret != 0) throw new Vps_ClientException();
        $onlineRevision = implode('', $onlineRevision);

        if (!$onlineRevision) {
            throw new Vps_ClientException("Can't get onlineRevision");
        }

        if ($config->uploads && $ownConfig->uploads) {
            echo "kopiere uploads...\n";
            if ($ownConfig->server->host == $config->server->host) {
                if ($ownConfig->uploads == $config->uploads) {
                    throw new Vps_ClientException("Uplodas-Pfade für beide Server sind gleich!");
                }
                $this->_systemCheckRet("rsync --progress --delete --times --exclude=cache/ --recursive {$config->uploads} {$ownConfig->uploads}");
            } else {
                $this->_systemSshVps('copy-uploads '.$ownConfig->uploads.'/', $config->uploads);
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
                        if (substr($e, -1) == '*') $e .= '*';
                        if (!in_array($e, $includes)) {
                            $includes[] = $e;
                        }
                    }
                }

                $cmd = "sudo -u vps sshvps $this->_sshHost $this->_sshDir copy-files";
                $cmd .= " --includes=\"".implode(',', $includes)."\"";
                if ($this->_getParam('debug')) $cmd .= " --debug";
                if ($this->_getParam('debug')) echo $cmd."\n";
                passthru($cmd);
            }
        }

        echo "erstelle datenbank-backup...\n";

        $cacheTables = Vps_Util_ClearCache::getInstance()->getDbCacheTables();
        if ($config->import && $config->import->ignoreTables) {
            foreach ($config->import->ignoreTables as $t) {
                $cacheTables[] = $t;
            }
        }

        $tables = Zend_Registry::get('db')->fetchCol('SHOW TABLES');
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
            echo "erstelle dump f�r KeepTables...\n";
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
            $otherDbConfig = new Zend_Config_Ini($config->server->dir.'/application/config.db.ini', 'database');
            $otherDbConfig = $otherDbConfig->web;
            $cmd = $this->_getDumpCommand($otherDbConfig, array_merge($cacheTables, $keepTables));
        } else {
            $ignoreTables = '';
            if (!$this->_getParam('include-cache')) {
                $ignoreTables = Vps_Util_ClearCache::getInstance()->getDbCacheTables();
                if ($config->server->import && $config->server->import->ignoreTables) {
                    foreach ($config->server->import->ignoreTables as $t) {
                        $ignoreTables[] = $t;
                    }
                }
                $ignoreTables = implode(',', array_merge($ignoreTables, $keepTables));
                if ($ignoreTables) $ignoreTables = " --ignore-tables=$ignoreTables";
            }
            $cmd = "sudo -u vps sshvps $this->_sshHost $this->_sshDir db-dump";
            $cmd .= "$ignoreTables";
            if ($this->_getParam('debug')) $cmd .= " --debug";
            $cmd .= " | gunzip";
        }
        $descriptorspec = array(
            1 => array("pipe", "w")
        );
        if ($this->_getParam('debug')) file_put_contents('php://stderr', "$cmd\n");
        $procDump = new Vps_Util_Proc($cmd, $descriptorspec);

        $cmd = "mysql $mysqlLocalOptions $dbConfig[dbname]";
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
        echo "Service: Kopiere 'users' tabelle...\n";

        $targetModel->import(
            Vps_Model_Interface::FORMAT_SQL,
            $sourceModel->export(Vps_Model_Interface::FORMAT_SQL)
        );

        // copy users_to_web
        $targetUrl = $this->_getConfig('web')->service->usersRelation->url;
        $sourceConfig = $this->_getConfig('production');
        $sourceUrl = $sourceConfig->service->usersRelation->url;
        if ($targetUrl == $sourceUrl) return;

        $sourceModel = new Vps_Model_Service(array('serverUrl' => $sourceUrl));
        $targetModel = new Vps_Model_Service(array('serverUrl' => $targetUrl));

        echo "Service: Loesche Benutzerzuweisungen zu diesem Web...\n";
        $importSelect = new Vps_Model_Select();
        $importSelect->whereEquals('web_id', Vps_Registry::get('config')->application->id);
        $targetModel->deleteRows($importSelect);

        $sourceData = $sourceModel->export(Vps_Model_Interface::FORMAT_ARRAY, $importSelect);
        $toDeleteIds = array();
        $tdIndex = 0;
        foreach ($sourceData as $d) {
            if (empty($toDeleteIds[$tdIndex])) $toDeleteIds[$tdIndex] = array();
            $toDeleteIds[$tdIndex][] = $d['id'];
            if (count($toDeleteIds[$tdIndex]) >= 500) ++$tdIndex;
        }
        foreach ($toDeleteIds as $delArray) {
            $targetModel->deleteRows($targetModel->select()->whereEquals('id', $delArray));
        }
        echo "Service: Fuege Produktiv-Benutzerzuweisungen hinzu...\n";
        $targetModel->import(
            Vps_Model_Interface::FORMAT_ARRAY,
            $sourceData
        );
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
}
