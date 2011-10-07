<?php
class Kwf_Model_DbWithConnection_ExprSumFields_Model extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;

        $config['exprs'] = array(
            'sum_field_int' => new Kwf_Model_Select_Expr_SumFields(array('value2', 10)),
            'sum_int_int' => new Kwf_Model_Select_Expr_SumFields(array(100, 10, 99)),
            'sum_field_field' => new Kwf_Model_Select_Expr_SumFields(array('value2', 'id')),
        );

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            value1 INT NOT NULL ,
            value2 INT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value1, value2) VALUES (1, 0, 100)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value1, value2) VALUES (2, 0, 400)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value1, value2) VALUES (3, 0, 400)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
