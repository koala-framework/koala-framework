<?php
class Kwf_Model_DbWithConnection_ProxyReferences_Parent2Model extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_parent2');
        $config['table'] = $this->_tableName;

        $this->_dependentModels['Childs'] = 'Kwf_Model_DbWithConnection_ProxyReferences_ChildModel';

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `foo2` INT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo2) VALUES (1, 3)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo2) VALUES (2, 4)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
