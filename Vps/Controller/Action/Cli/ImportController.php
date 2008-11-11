<?php
class Vps_Controller_Action_Cli_ImportController extends Vps_Controller_Action_Cli_Abstract
{
    public function indexAction()
    {
        if (Vps_Registry::get('config')->server->host != 'vivid') {
            throw new Vps_ClientException("Import is only possible on vivid server");
        }
        $dbConfig = new Zend_Config_Ini('application/config.db.ini', 'database');
        $dbConfig = $dbConfig->web;
        $mysqlLocalOptions = "--host={$dbConfig->host} --user={$dbConfig->username} --password={$dbConfig->password} ";

        $config = new Zend_Config_Ini('application/config.ini', $this->_getParam('server'));
        $this->_sshHost = $config->server->user.'@'.$config->server->host;
        $this->_sshDir = $config->server->dir;

        $onlineRevision = `sudo -u www-data sshvps $this->_sshHost $this->_sshDir import get-update-revision`;

        echo "kopiere uploads...\n";
        $this->_systemSshVps('copy-uploads '.Vps_Registry::get('config')->uploads.'/', $config->uploads);

        //$this->_systemCheckRet("rsync --progress --delete --update --exclude=cache/ ".
        //    "--recursive {$this->_sshHost}:{$config->uploads}/ ".Vps_Registry::get('config')->uploads."/");


        $p = "/var/backups/vpsimport/".date("Y-m-d_H:i:s_U")."_{$dbConfig->dbname}.sql";
        echo "erstelle datenbank-backup in $p...\n";
        $this->_systemCheckRet("mysqldump $mysqlLocalOptions {$dbConfig->dbname} > $p");

        echo "erstelle datenbank dump (online)...\n";
        $dumpname = `sudo -u www-data sshvps $this->_sshHost $this->_sshDir import create-dump`;

        echo "kopiere datenbank dump...\n";
        $this->_systemSshVps('copy-dump '.$dumpname);

        echo "loesche lokale datenbank...\n";
        $this->_systemCheckRet("echo \"DROP DATABASE \`{$dbConfig->dbname}\`\" | mysql $mysqlLocalOptions");

        echo "erstelle neue datenbank...\n";
        $this->_systemCheckRet("echo \"CREATE DATABASE \`{$dbConfig->dbname}\`\" | mysql $mysqlLocalOptions");

        echo "spiele dump in lokale datenbank ein...\n";
        $dumpname = substr($dumpname, 0, -4);
        $this->_systemCheckRet("mysql $mysqlLocalOptions {$dbConfig->dbname} < $dumpname");

        echo "schreibe application/update ($onlineRevision)...\n";
        file_put_contents('application/update', $onlineRevision);

        echo "fertig!\n";

        exit();
    }
    private function _systemSsh($cmd)
    {
        return $this->_systemCheckRet("ssh {$this->_sshHost} \"nice ".$cmd."\"");
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

    public function createDumpAction()
    {
        $dbConfig = new Zend_Config_Ini('application/config.db.ini', 'database');
        $dbConfig = $dbConfig->web;
        $mysqlOptions = "--host={$dbConfig->host} --user={$dbConfig->username} --password={$dbConfig->password} ";
        $config = Zend_Registry::get('config');

        $mysqlDir = '';
        if ($config->server->host == 'vivid-planet.com') {
            $mysqlDir = '/usr/local/mysql/bin/';
        }

        $dumpname = tempnam('/tmp', 'vpsimport');

        $tables = Zend_Registry::get('db')->fetchCol('SHOW TABLES');


        $this->_systemCheckRet("{$mysqlDir}mysqldump --ignore-table={$dbConfig->dbname}.cache_component $mysqlOptions {$dbConfig->dbname} > $dumpname");
        if (in_array('cache_component', $tables)) {
            $this->_systemCheckRet("{$mysqlDir}mysqldump --no-data $mysqlOptions {$dbConfig->dbname} cache_component >> $dumpname");
        }

        $this->_systemCheckRet("bzip2 $dumpname");

        echo $dumpname.".bz2";

        $this->_helper->viewRenderer->setNoRender(true);
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
