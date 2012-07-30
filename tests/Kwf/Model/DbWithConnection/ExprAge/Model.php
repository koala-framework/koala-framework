<?php
class Kwf_Model_DbWithConnection_ExprAge_Model extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $today = date('Y-m-d');
        $tomorrow = date('Y-m-d', strtotime("$today +1 day"));
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;

        $config['exprs'] = array(
            'age' => new Kwf_Model_Select_Expr_Date_Age('birth'),
            'age_ref' => new Kwf_Model_Select_Expr_Date_Age('birth', new Kwf_Date($tomorrow)),
        );

        parent::__construct($config);
    }

    public function setUp()
    {
        $today = date('Y-m-d');
        $today = date('Y-m-d', strtotime("$today -1 year"));
        $eighteen = date("Y-m-d", strtotime("$today -17 year"));
        $tomorrow = date("Y-m-d", strtotime("$today +1 day"));
        $yesterday = date("Y-m-d", strtotime("$today - 1 day"));
        $newYearsEve = date('Y')-1 . "-12-31";
        $newYear = date('Y')-1 . "-01-01";
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            birth DATE NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, birth) VALUES (1, '$eighteen')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, birth) VALUES (2, '$today')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, birth) VALUES (3, '$tomorrow')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, birth) VALUES (4, '$yesterday')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, birth) VALUES (5, '$newYearsEve')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, birth) VALUES (6, '$newYear')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, birth) VALUES (7, NULL)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
