<?php
class Kwf_Model_DbWithConnection_ExprChildSum_BarModel extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_childsum_bar');
        $config['table'] = $this->_tableName;

        $this->_dependentModels['FooToBar'] = 'Kwf_Model_DbWithConnection_ExprChildSum_FooToBarModel';

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `bar` INT
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, bar) VALUES (1, 4)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, bar) VALUES (2, 2)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, bar) VALUES (3, 3)");
    }

    protected function _init()
    {
        parent::_init();
        $this->_exprs['foo_value_sum'] = new Kwf_Model_Select_Expr_Child_Sum('FooToBar', 'foo_value');
        $this->_exprs['or_expr'] = new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equal('bar', 4),
            new Kwf_Model_Select_Expr_Equal('bar', 2)
        ));
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
