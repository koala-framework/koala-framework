<?php
class Kwf_Model_DbWithConnection_ParentExprDeletedFlag_ParentModel extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_parent');
        $config['table'] = $this->_tableName;
        $config['hasDeletedFlag'] = true;

        $this->_dependentModels['Childs'] = 'Kwf_Model_DbWithConnection_ParentExprDeletedFlag_ChildModel';

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `foo` INT NOT NULL,
            `deleted` TINYINT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, deleted) VALUES (1, 5, 1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, deleted) VALUES (2, 7, 0)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
