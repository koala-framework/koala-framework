<?php
class Vps_Controller_Action_Cli_ImportController extends Vps_Controller_Action_Cli_Abstract
{
    public function indexAction()
    {
        $ownConfig = Vps_Registry::get('config');

        $dbConfig = new Zend_Config_Ini('application/config.db.ini', 'database');
        $dbConfig = $dbConfig->web;
        $mysqlLocalOptions = "--host={$dbConfig->host} ";
        //auskommentiert weil: es muss in ~/.my.cnf ein benutzer der das machen darf eingestellt sein!
        //ansonsten gibt es probleme für das erstellen von triggers, dazu benötigt man SUPER priviliges
        // -> scheiß mysql
        //$mysqlLocalOptions .= "--user={$dbConfig->username} --password={$dbConfig->password} ";

        $server = $this->_getParam('server');
        $config = new Zend_Config_Ini('application/config.ini', $server);
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
        if ($this->_getParam('debug')) {
            echo $cmd."\n";
        }
        exec($cmd, $onlineRevision, $ret);
        if ($ret != 0) throw new Vps_ClientException();
        $onlineRevision = implode('', $onlineRevision);

        if (!$onlineRevision) {
            throw new Vps_ClientException("Can't get onlineRevision");
        }

        echo "kopiere uploads...\n";
        if ($ownConfig->server->host == $config->server->host) {
            if ($ownConfig->uploads == $config->uploads) {
                throw new Vps_ClientException("Uplodas-Pfade für beide Server sind gleich!");
            }
            $this->_systemCheckRet("rsync --progress --delete --times --exclude=cache/ --recursive {$config->uploads} {$ownConfig->uploads}");
        } else {
            $this->_systemSshVps('copy-uploads '.$ownConfig->uploads.'/', $config->uploads);
        }

        if (file_exists("/var/backups/vpsimport/")) {
            $p = "/var/backups/vpsimport/";
        } else {
            $p = getcwd().'/../backup/';
            if (!file_exists($p)) mkdir($p);
        }
        $p .= date("Y-m-d_H:i:s_U")."_{$dbConfig->dbname}.sql";
        echo "erstelle datenbank-backup in $p...\n";
        $this->_systemCheckRet("mysqldump $mysqlLocalOptions {$dbConfig->dbname} > $p");

        echo "erstelle datenbank dump ($server)...\n";
        $cmd = "import create-dump";
        if ($this->_getParam('include-cache')) {
            $cmd .= " --include-cache";
        }
        if ($ownConfig->server->host == $config->server->host) {
            $cmd = "cd {$config->server->dir} && php bootstrap.php $cmd";
        } else {
            $cmd = "sudo -u vps sshvps $this->_sshHost $this->_sshDir $cmd";
        }
        exec($cmd, $dumpname, $ret);
        if ($ret != 0) throw new Vps_ClientException();
        $dumpname = implode('', $dumpname);

        if ($ownConfig->server->host != $config->server->host) {
            echo "kopiere datenbank dump $dumpname...\n";
            $this->_systemSshVps('copy-dump '.$dumpname);
        } else {
            $this->_systemCheckRet("bunzip2 $dumpname");
        }

        echo "loesche lokale datenbank...\n";
        $this->_systemCheckRet("echo \"DROP DATABASE \`{$dbConfig->dbname}\`\" | mysql $mysqlLocalOptions");

        echo "erstelle neue datenbank...\n";
        $this->_systemCheckRet("echo \"CREATE DATABASE \`{$dbConfig->dbname}\`\" | mysql $mysqlLocalOptions");

        echo "spiele dump in lokale datenbank ein...\n";
        $dumpname = substr($dumpname, 0, -4);
        $this->_systemCheckRet("mysql $mysqlLocalOptions {$dbConfig->dbname} < $dumpname");

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
        $dbConfig = new Zend_Config_Ini('application/config.db.ini', 'database');
        $dbConfig = $dbConfig->web;

        if (file_exists("/var/backups/vpsimport/")) {
            $dumpname = "/var/backups/vpsimport/";
        } else {
            $dumpname = getcwd().'/../backup/';
            if (!file_exists($dumpname)) mkdir($dumpname);
        }
        $dumpname .= date("Y-m-d_H:i:s_U")."_{$dbConfig->dbname}.sql";
        echo "erstelle backup...\n";
        $this->_createDump($dumpname);
        echo "\n";
        $this->_helper->viewRenderer->setNoRender(true);
    }

    public function createDumpAction()
    {
        $dumpname = tempnam('/tmp', 'vpsimport');
        $skipCacheTables = true;
        if ($this->_getParam('include-cache')) {
            $skipCacheTables = false;
        }
        $this->_createDump($dumpname, $skipCacheTables);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _createDump($dumpname, $skipCacheTables = true)
    {
        $dbConfig = new Zend_Config_Ini('application/config.db.ini', 'database');
        $dbConfig = $dbConfig->web;
        $mysqlOptions = "--host={$dbConfig->host} --user={$dbConfig->username} --password={$dbConfig->password} ";
        $config = Zend_Registry::get('config');

        $mysqlDir = '';
        if ($config->server->host == 'vivid-planet.com') {
            $mysqlDir = '/usr/local/mysql/bin/';
        }

        $ignoreTables = '';
        if ($skipCacheTables) {
            $cacheTables = Vps_Util_ClearCache::getInstance()->getDbCacheTables();
            foreach ($cacheTables as $t) {
                $ignoreTables .= " --ignore-table={$dbConfig->dbname}.{$t}";
            }
        }

        $this->_systemCheckRet("{$mysqlDir}mysqldump{$ignoreTables} $mysqlOptions {$dbConfig->dbname} > $dumpname");
        if ($skipCacheTables) {
            foreach ($cacheTables as $t) {
                $this->_systemCheckRet("{$mysqlDir}mysqldump --no-data $mysqlOptions {$dbConfig->dbname} $t >> $dumpname");
            }
        }

        $this->_systemCheckRet("bzip2 --fast $dumpname");

        echo $dumpname.".bz2";
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
}
