<?php
class Kwf_Model_DbWithConnection_ExprInteger_Model extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;

        $config['exprs'] = array(
            'price' => new Kwf_Model_Select_Expr_Integer(100),
            'amount' => new Kwf_Model_Select_Expr_Integer('3'),
            'total' => new Kwf_Model_Select_Expr_Multiply(array(
                new Kwf_Model_Select_Expr_Field('price'),
                new Kwf_Model_Select_Expr_Field('amount')
            ))
        );

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id) VALUES (1)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}

