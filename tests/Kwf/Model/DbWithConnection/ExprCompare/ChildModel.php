<?php
class Kwf_Model_DbWithConnection_ExprCompare_ChildModel extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_child');
        $config['table'] = $this->_tableName;

        $this->_referenceMap = array(
            'Parent' => 'parent_id->Kwf_Model_DbWithConnection_ExprCompare_ParentModel'
        );

        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        $this->_exprs['expr_bar'] = new Kwf_Model_Select_Expr_Field('bar');
        $this->_exprs['expr_bar2'] = new Kwf_Model_Select_Expr_Field('bar2');
        $this->_exprs['expr_foo'] = new Kwf_Model_Select_Expr_Field('foo');
        $this->_exprs['expr_bar_compare'] = new Kwf_Model_Select_Expr_Equals('expr_bar', new Kwf_Model_Select_Expr_Field('bar2'));
        $this->_exprs['expr_foo_bar_higher'] = new Kwf_Model_Select_Expr_HigherEqual('expr_bar', new Kwf_Model_Select_Expr_Field('expr_foo'));
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `parent_id` INT UNSIGNED NOT NULL,
            `bar` INT,
            `bar2` INT,
            `foo` INT
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar, bar2, foo) VALUES (1, 1, 5, 5, 4)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar, bar2, foo) VALUES (2, 2, 1, 1, 2)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar, bar2, foo) VALUES (3, 1, 4, 4, 3)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
