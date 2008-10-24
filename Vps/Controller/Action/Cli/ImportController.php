<?php
class Vps_Controller_Action_Cli_ImportController extends Vps_Controller_Action_Cli_Abstract
{
    public function indexAction()
    {
        $dbConfig = new Zend_Config_Ini('application/config.db.ini', 'database');
        $dbConfig = $dbConfig->web;
        $mysqlLocalOptions = "--host={$dbConfig->host} --user={$dbConfig->username} --password={$dbConfig->password} ";

        $config = new Zend_Config_Ini('application/config.ini', $this->_getParam('server'));

        if ($config->server->host == 'vivid-planet.com') {
            $this->_sshHost = 'vivid@vivid-planet.com';
        } else {
            throw new Vps_ClientException("Unknown server-host: {$config->server->host}");
        }

        echo "ermittle db optionen...\n";
        $dbConfigIni = tempnam('/tmp', 'vpsimport');
        $this->_systemCheckRet("scp {$this->_sshHost}:{$config->server->dir}/application/config.db.ini $dbConfigIni");
        $onlineDbConfig = new Zend_Config_Ini($dbConfigIni, 'database');
        unlink($dbConfigIni);
        $onlineDbConfig = $onlineDbConfig->web;
        $mysqlOnlineOptions = "--host={$onlineDbConfig->host} --user={$onlineDbConfig->username} --password={$onlineDbConfig->password} ";

        echo "kopiere uploads...\n";
        $this->_systemCheckRet("rsync --progress --delete --update --exclude=cache/ ".
            "--recursive {$this->_sshHost}:{$config->uploads}/ ".Vps_Registry::get('config')->uploads."/");

        $p = "/var/backups/vpsimport/".date("Y-m-d_H:i:s_U")."_{$dbConfig->dbname}.sql";
        echo "erstelle datenbank-backup in $p...\n";
        $this->_systemCheckRet("mysqldump $mysqlLocalOptions {$dbConfig->dbname} > $p");

        $dumpname = tempnam('/tmp', 'vpsimport');
        echo "erstelle datenbank dump (online) - $dumpname...\n";
        $this->_systemSsh("/usr/local/mysql/bin/mysqldump --ignore-table={$onlineDbConfig->dbname}.cache_component $mysqlOnlineOptions {$onlineDbConfig->dbname} > $dumpname");
        $this->_systemSsh("/usr/local/mysql/bin/mysqldump --no-data $mysqlOnlineOptions {$onlineDbConfig->dbname} cache_component >> $dumpname");

        $this->_systemSsh("bzip2 $dumpname");
        $this->_systemSsh("ls -lh $dumpname.bz2");


        echo "kopiere datenbank dump...\n";
        $this->_systemCheckRet("scp {$this->_sshHost}:{$dumpname}.bz2 {$dumpname}.bz2");

        echo "loesche datenbank dump online...\n";
        $this->_systemSsh("rm $dumpname.bz2");

        echo "loesche lokale datenbank...\n";
        $this->_systemCheckRet("echo \"DROP DATABASE \`{$dbConfig->dbname}\`\" | mysql $mysqlLocalOptions");

        echo "erstelle neue datenbank...\n";
        $this->_systemCheckRet("echo \"CREATE DATABASE \`{$dbConfig->dbname}\`\" | mysql $mysqlLocalOptions");

        echo "spiele dump in lokale datenbank ein...\n";
        $this->_systemCheckRet("rm {$dumpname}");
        $this->_systemCheckRet("bunzip2 {$dumpname}.bz2");
        $this->_systemCheckRet("mysql $mysqlLocalOptions {$dbConfig->dbname} < $dumpname");

        echo "loesche datebank dump lokal...\n";
        $this->_systemCheckRet("rm $dumpname");

        echo "fertig!\n";

        exit();
    }
    private function _systemSsh($cmd)
    {
        return $this->_systemCheckRet("ssh {$this->_sshHost} \"nice ".$cmd."\"");
    }

    public static function getHelp()
    {
        return "import uplodas+database";
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
}
