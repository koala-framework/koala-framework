<?php
class Kwf_Model_DbWithConnection_ExprPosition_Model extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;

        $config['exprs'] = array(
            'position' => new Kwf_Model_Select_Expr_Position('points', array('gr')),
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
                        (id, points, gr) VALUES (1, 1000, 1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, points, gr) VALUES (2, 500, 1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, points, gr) VALUES (3, 0, 2)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, points, gr) VALUES (4, -50, 2)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, points, gr) VALUES (5, 10000, 1)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
