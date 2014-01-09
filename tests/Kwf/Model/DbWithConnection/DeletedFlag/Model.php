<?php
class Kwf_Model_DbWithConnection_DeletedFlag_Model extends Kwf_Model_Db
{
    private $_tableName;
    protected $_hasDeletedFlag = true;
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
            foo VARCHAR(255) NULL ,
            deleted TINYINT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, deleted) VALUES (1, 'bar', 0)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, deleted) VALUES (2, 'bar2', 0)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
