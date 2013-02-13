<?php
class Kwf_Model_DbWithConnection_ProxyReferences_StartChildModel extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_childstart');
        $config['table'] = $this->_tableName;

        $this->_dependentModels['Childs'] = 'Kwf_Model_DbWithConnection_ProxyReferences_ParentModel';

        $this->_referenceMap = array(
            'StartParent' => 'parent_id->Kwf_Model_DbWithConnection_ProxyReferences_ParentModel'
        );
        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `parent_id` INT NOT NULL,
            `start_child` TEXT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, start_child) VALUES (1, 1, 'Child #1')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, start_child) VALUES (2, 2, 'Child #2')");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
