<?php
class Kwf_Model_DbWithConnection_ExprMax_Model extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;

        $this->_dependentModels = array(
            'Child' => 'Kwf_Model_DbWithConnection_ExprMax_ChildModel'
        );

        $config['exprs'] = array(
            'max' => new Kwf_Model_Select_Expr_Max('value1'),
//             'max_child' => new Kwf_Model_Select_Expr_Max(new Kwf_Model_Select_Expr_Child_Max('Child', 'value2')),
        //TODO does actually not work
            'max_child_count' => new Kwf_Model_Select_Expr_Max(new Kwf_Model_Select_Expr_Child_Count('Child'))
        );

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            value1 INT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value1) VALUES (1, 100)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, value1) VALUES (2, 400)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
