<?php
class Kwf_Model_DbWithConnection_ExprChildSum_FooModel extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_childsum_foo');
        $config['table'] = $this->_tableName;

        $this->_dependentModels['FooTooBar'] = 'Kwf_Model_DbWithConnection_ExprChildSum_FooToBarModel';

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `value` INT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value) VALUES (1, 4)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value) VALUES (2, 2)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
