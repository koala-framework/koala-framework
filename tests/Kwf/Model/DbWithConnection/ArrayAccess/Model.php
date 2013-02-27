<?php
class Kwf_Model_DbWithConnection_ArrayAccess_Model extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;
        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
             `value` VARCHAR( 255 ) NULL 
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value) VALUES (1, 'value1')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value) VALUES (2, 'value2')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value) VALUES (3, 'Peter Griffin')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value) VALUES (4, 'value4')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value) VALUES (5, 'value5')");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
