<?php
class Vps_Model_DbWithConnection_ImportExport_Model extends Vps_Model_Db
{
    public function __construct($config = array())
    {
        $this->_tableName = $config['table'];
        Vps_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `foo` VARCHAR( 200 ) NOT NULL ,
            `bar` VARCHAR( 200 ) NOT NULL
        ) ENGINE = INNODB DEFAULT CHARSET=utf8");
        parent::__construct($config);
    }

    public function clearRows()
    {
        $this->_rows = array();
    }

    public function dropTable()
    {
        Vps_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }

    public function writeInitRows()
    {
        Vps_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, bar) VALUES ('1', 'aaabbbccc', 'abcd')");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, bar) VALUES ('2', 'bam', 'bum')");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, bar) VALUES ('3', 'bäm', 'büm')");
    }
}
