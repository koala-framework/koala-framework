<?php
class Kwf_Model_DbWithConnection_MultipleReferences_ToFooModel extends Kwf_Model_Db
{
    private $_tableName;
    protected $_referenceMap = array(
        'Foo1' => array(
                'refModelClass' => 'Kwf_Model_DbWithConnection_MultipleReferences_FooModel',
                'column' => 'foo1_id',
        ),
        'Foo2' => array(
                'refModelClass' => 'Kwf_Model_DbWithConnection_MultipleReferences_FooModel',
                'column'        => 'foo2_id',
        )
    );
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_multiple_to_foo');
        $config['table'] = $this->_tableName;

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `foo1_id` INT UNSIGNED NOT NULL,
            `foo2_id` INT UNSIGNED NOT NULL,
            `bar` INT
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo1_id, foo2_id, bar) VALUES (1, 1, 2, 5)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo1_id, foo2_id, bar) VALUES (2, 2, 1, 1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo1_id, foo2_id, bar) VALUES (3, 1, 2, 4)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
