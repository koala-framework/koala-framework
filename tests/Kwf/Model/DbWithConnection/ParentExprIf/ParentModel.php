<?php
class Kwf_Model_DbWithConnection_ParentExprIf_ParentModel extends Kwf_Model_Db_Proxy
{
    private $_tableName;
    protected $_siblingModels = array(
        'sibling' => 'Kwf_Model_DbWithConnection_ParentExprIf_ParentSiblingModel'
    );

    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_parent');
        $config['proxyModel'] = new Kwf_Model_Db(array('table' => $this->_tableName));

        $this->_dependentModels['Childs'] = 'Kwf_Model_DbWithConnection_ParentExprIf_ChildModel';

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `parent_value` INT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_value) VALUES (1, 205)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_value) VALUES (2, 207)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
