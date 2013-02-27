<?php
class Kwf_Model_DbWithConnection_ProxyReferences_ChildModel extends Kwf_Model_Db_Proxy
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_child');
        $config['table'] = $this->_tableName;

        $this->_referenceMap = array(
            'Parent' => 'parent_id->Kwf_Model_DbWithConnection_ProxyReferences_ParentModel',
            'Parent2' => 'parent2_id->Kwf_Model_DbWithConnection_ProxyReferences_Parent2Model'
        );

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `parent_id` INT UNSIGNED NOT NULL,
            `parent2_id` INT UNSIGNED NOT NULL,
            `bar` INT
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, parent2_id, bar) VALUES (1, 1, 1, 5)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, parent2_id, bar) VALUES (2, 1, 1, 6)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, parent2_id, bar) VALUES (3, 1, 1, 7)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, parent2_id, bar) VALUES (4, 2, 2, 7)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
