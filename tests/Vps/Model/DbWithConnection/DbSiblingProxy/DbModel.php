<?php
class Vps_Model_DbWithConnection_DbSiblingProxy_DbModel extends Vps_Model_Db
{
    public function __construct($config = array())
    {
        $this->_tableName = 'master'.uniqid();
        $config['table'] = $this->_tableName;
        Vps_Registry::get('db')->query("CREATE TABLE {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `foo` VARCHAR( 200 ) NOT NULL ,
            `bar` VARCHAR( 200 ) NOT NULL
        ) ENGINE = INNODB");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, bar) VALUES ('1', 'aaabbbccc', 'abcd')");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, bar) VALUES ('2', 'bam', 'bum')");
        parent::__construct($config);
    }

    public function dropTable()
    {
        Vps_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }

    public function clearRows()
    {
        $this->_rows = array();
    }
}
