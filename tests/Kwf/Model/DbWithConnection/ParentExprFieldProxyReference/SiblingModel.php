<?php
class Kwf_Model_DbWithConnection_ParentExprFieldProxyReference_SiblingModel extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_sibling');
        $config['table'] = $this->_tableName;

        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        $this->_referenceMap['Proxy'] = array(
            'column' => 'id',
            'refModelClass' => 'Kwf_Model_DbWithConnection_ParentExprFieldProxyReference_ProxyModel' // muss hier hardcodet sein, sonst endlos
        );
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `parent_id` INT UNSIGNED NOT NULL,
            `bar` INT
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar) VALUES (1, 1, 5)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar) VALUES (2, 2, 1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar) VALUES (3, 1, 4)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
