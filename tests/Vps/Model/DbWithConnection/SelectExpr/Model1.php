<?php
class Vps_Model_DbWithConnection_SelectExpr_Model1 extends Vps_Model_Db
{
    private $_tableName;
    protected $_dependentModels = array(
        'Model2' => 'Vps_Model_DbWithConnection_SelectExpr_Model2'
    );
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;

        $config['exprs'] = array(
            'count_model2' => new Vps_Model_Select_Expr_Child_Count('Model2')
        );
        parent::__construct($config);
    }

    public function setUp()
    {
        Vps_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `foo` VARCHAR( 200 ) NOT NULL ,
            `bar` VARCHAR( 200 ) NOT NULL
        ) ENGINE = INNODB");
        Vps_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, bar) VALUES ('1', 'aaabbbccc', 'abcd')");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, bar) VALUES ('2', 'bam', 'bum')");
    }

    public function dropTable()
    {
        Vps_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
