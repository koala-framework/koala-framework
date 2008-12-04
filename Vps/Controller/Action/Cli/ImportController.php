<?php
class Vps_Controller_Action_Cli_ImportController extends Vps_Controller_Action_Cli_Abstract
{
    public function indexAction()
    {
        $ownConfig = Vps_Registry::get('config');

        $dbConfig = new Zend_Config_Ini('application/config.db.ini', 'database');
        $dbConfig = $dbConfig->web;
        $mysqlLocalOptions = "--host={$dbConfig->host} --user={$dbConfig->username} --password={$dbConfig->password} ";

        $server = $this->_getParam('server');
        $config = new Zend_Config_Ini('application/config.ini', $server);
        $this->_sshHost = $config->server->user.'@'.$config->server->host;
        $this->_sshDir = $config->server->dir;

        if ($ownConfig->server->host == $config->server->host) {
            $cmd = "cd {$config->server->dir} && php bootstrap.php import get-update-revision";
        } else {
            $cmd = "sudo -u www-data sshvps $this->_sshHost $this->_sshDir import get-update-revision";
        }
        exec($cmd, $onlineRevision, $ret);
        if ($ret != 0) throw new Vps_ClientException();

        if (!$onlineRevision) {
            throw new Vps_ClientException("Can't get onlineRevision");
        }

        echo "kopiere uploads...\n";
        if ($ownConfig->server->host == $config->server->host) {
            if ($ownConfig->uploads == $config->uploads) {
                throw new Vps_ClientException("Uplodas-Pfade für beide Server sind gleich!");
            }
            $this->_systemCheckRet("rsync --progress --delete --update --exclude=cache/ --recursive {$config->uploads}/ {$ownConfig->uploads}/");
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
        if ($ownConfig->server->host == $config->server->host) {
            $cmd = "cd {$config->server->dir} && php bootstrap.php import create-dump";
        } else {
            $cmd = "sudo -u www-data sshvps $this->_sshHost $this->_sshDir import create-dump";
        }
        exec($cmd, $dumpname, $ret);
        if ($ret != 0) throw new Vps_ClientException();

        if ($ownConfig->server->host != $config->server->host) {
            echo "kopiere datenbank dump...\n";
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

        echo "schreibe application/update...\n";
        file_put_contents('application/update', $onlineRevision);

        Vps_Controller_Action_Cli_ClearCacheController::clearCache();

        echo "fertig!\n";

        $this->_helper->viewRenderer->setNoRender(true);
    }
    private function _systemSshVps($cmd, $dir = null)
    {
        if (!$dir) $dir = $this->_sshDir;
        $cmd = "sshvps $this->_sshHost $dir $cmd";
        $cmd = "sudo -u www-data $cmd";
        return $this->_systemCheckRet($cmd);
    }
    public static function getHelp()
    {
        return "import uploads+database";
    }
    public static function getHelpOptions()
    {
        return array(
            array(
                'param'=> 'server',
                'value'=> self::_getConfigSections(),
                'valueOptional' => true,
                'help' => 'what to import'
            )
        );
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
        $this->_createDump($dumpname);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private function _createDump($dumpname)
    {
        $dbConfig = new Zend_Config_Ini('application/config.db.ini', 'database');
        $dbConfig = $dbConfig->web;
        $mysqlOptions = "--host={$dbConfig->host} --user={$dbConfig->username} --password={$dbConfig->password} ";
        $config = Zend_Registry::get('config');

        $mysqlDir = '';
        if ($config->server->host == 'vivid-planet.com') {
            $mysqlDir = '/usr/local/mysql/bin/';
        }

        $tables = Zend_Registry::get('db')->fetchCol('SHOW TABLES');


        $this->_systemCheckRet("{$mysqlDir}mysqldump --ignore-table={$dbConfig->dbname}.cache_component $mysqlOptions {$dbConfig->dbname} > $dumpname");
        if (in_array('cache_component', $tables)) {
            $this->_systemCheckRet("{$mysqlDir}mysqldump --no-data $mysqlOptions {$dbConfig->dbname} cache_component >> $dumpname");
        }

        $this->_systemCheckRet("bzip2 $dumpname");

        echo $dumpname.".bz2";
    }

    public function getUpdateRevisionAction()
    {
        if (file_exists('application/update')) {
            echo file_get_contents('application/update');
        } else {
            try {
                $info = new SimpleXMLElement(`svn info --xml"`);
                $onlineRevision = (int)$info->entry['revision'];
            } catch (Exception $e) {}
            if (!$onlineRevision) {
                throw new Vps_ClientException("Can't detect online revision");
            }
        }
        $this->_helper->viewRenderer->setNoRender(true);
    }
}
