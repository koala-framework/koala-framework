<?php
class Kwf_Model_DbWithConnection_ExprChildSum_FooToBarModel extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_childsum_foo_to_bar');
        $config['table'] = $this->_tableName;

        $this->_referenceMap = array(
            'Foo' => 'foo_id->Kwf_Model_DbWithConnection_ExprChildSum_FooModel',
            'Bar' => 'bar_id->Kwf_Model_DbWithConnection_ExprChildSum_BarModel',
        );

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `foo_id` INT NOT NULL,
            `bar_id` INT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo_id, bar_id) VALUES (1, 1, 1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo_id, bar_id) VALUES (2, 2, 1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo_id, bar_id) VALUES (3, 2, 2)");
    }
    
    protected function _init()
    {
        parent::_init();
        $this->_exprs['foo_value'] = new Kwf_Model_Select_Expr_Parent('Foo', 'value');
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
