<?php
class Kwf_Model_DbWithConnection_ExprGroupConcat_Model extends Kwf_Model_Db
{
    protected $_dependentModels = array(
        'Children' => 'Kwf_Model_DbWithConnection_ExprGroupConcat_ChildModel'
    );
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;

        $s = new Kwf_Model_Select();
        $config['exprs'] = array(
            'foo1' => new Kwf_Model_Select_Expr_Child_GroupConcat('Children', 'id'),
            'foo2' => new Kwf_Model_Select_Expr_Child_GroupConcat('Children', 'id', ', '),
            'foo3' => new Kwf_Model_Select_Expr_Child_GroupConcat('Children', 'id', ', ', $s, 'sort_field'),
            'foo4' => new Kwf_Model_Select_Expr_Child_GroupConcat('Children', 'id', ', ', $s, array(
                'field' => 'sort_field',
                'direction' => 'DESC'
            )),
        );

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            points INT NOT NULL,
            gr INT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id) VALUES (1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id) VALUES (2)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id) VALUES (3)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
