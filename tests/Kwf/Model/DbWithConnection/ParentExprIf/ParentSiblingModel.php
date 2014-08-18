<?php
class Kwf_Model_DbWithConnection_ParentExprIf_ParentSiblingModel extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_parent_sibling');
        $config['table'] = $this->_tableName;
        $this->_referenceMap = array(
            'Parent' => 'id->Kwf_Model_DbWithConnection_ParentExprIf_ParentModel'
        );
        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `sibling_value` INT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, sibling_value) VALUES (1, 105)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, sibling_value) VALUES (2, 107)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
