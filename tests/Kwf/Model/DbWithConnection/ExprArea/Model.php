<?php
class Kwf_Model_DbWithConnection_ExprArea_Model extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;
        $config['exprs'] = array(
            'inrange' => new Kwf_Model_Select_Expr_Area(47.8904081, 13.1834356, 50)
        );
        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            latitude FLOAT,
            longitude FLOAT
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        // Inrange
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (1, 47.8904081, 13.1834356)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (2, 47.586918, 12.697234)"); // Lofer, Hotel Dax
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (3, 47.475, 13.188889)"); // Werfen
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (4, 47.8071383, 13.779037)"); // Ebensee
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (5, 48.21272, 13.49272)"); // Ried im Innkreis
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (6, 47.823405, 12.6403154)"); // Siegsdorf, Deutschland
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (7, 48.25573, 13.04432)"); // Braunau
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (8, 47.7123709, 13.6210153)"); // Bad Ischl
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (9, 48.00334, 13.65613)"); // Vöcklabruck

        // out of range
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (10, 48.0032901, 13.925147)"); // Vorchdorf
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (11, 47.4172124, 13.2188864)"); // Bischofshofen
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (12, 47.4273901, 12.8411445)"); // Saalfelden
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (13, 47.8571272, 12.1181047)"); // Rosenheim
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, latitude, longitude) VALUES (14, 48.2246432, 12.6767839)"); // Altötting
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
