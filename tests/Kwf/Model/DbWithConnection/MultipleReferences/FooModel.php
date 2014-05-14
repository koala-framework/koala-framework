<?php
class Kwf_Model_DbWithConnection_MultipleReferences_FooModel extends Kwf_Model_Db
{
    private $_tableName;
    protected $_dependentModels = array(
        'Reference1' => array(
            'model' => 'Kwf_Model_DbWithConnection_MultipleReferences_ToFooModel',
            'rule' => 'Foo1',
        ),
        'Reference2' => array(
            'model' => 'Kwf_Model_DbWithConnection_MultipleReferences_ToFooModel',
            'rule' => 'Foo2',
        ),
    );
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_multiple_foo');
        $config['table'] = $this->_tableName;

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `foo` INT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo) VALUES (1, 5)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo) VALUES (2, 7)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
