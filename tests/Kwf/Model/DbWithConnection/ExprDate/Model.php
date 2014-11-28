<?php
class Kwf_Model_DbWithConnection_ExprDate_Model extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;

        $config['exprs'] = array(
            'date_year' => new Kwf_Model_Select_Expr_Date_Year('date'),
            'date_year_two_digits' => new Kwf_Model_Select_Expr_Date_Year('date', Kwf_Model_Select_Expr_Date_Year::FORMAT_DIGITS_TWO),
            'date_format' => new Kwf_Model_Select_Expr_Date_Format('date'),
            'date_format2' => new Kwf_Model_Select_Expr_Date_Format('date', 'd.m.Y'),
        );

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            date DATE NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, date) VALUES (1, '1983-06-09')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, date) VALUES (2, '2003-06-20')");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
