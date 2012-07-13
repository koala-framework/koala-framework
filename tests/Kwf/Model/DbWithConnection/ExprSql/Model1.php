<?php
class Kwf_Model_DbWithConnection_ExprSql_Model1 extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;

        $config['exprs'] = array(
            'age' => new Kwf_Model_Select_Expr_Date_Age('birth'),
            'age2' => new Kwf_Model_Select_Expr_Sql("SELECT expr{age} + expr{age}")
        );

        parent::__construct($config);
    }

    public function setUp()
    {
        $today = date('Y-m-d');
        $eighteen = date("Y-m-d", strtotime("$today -18 year"));
        $twentytwo = date("Y-m-d", strtotime("$today -22 year"));
        $ten = date("Y-m-d", strtotime("$today -10 year"));
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            birth DATE NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, birth) VALUES (1, '$eighteen')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, birth) VALUES (2, '$twentytwo')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, birth) VALUES (3, '$ten')");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
