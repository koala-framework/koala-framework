<?php
class Vps_Model_DbWithConnection_SelectExpr_Model2 extends Vps_Model_Db
{
    private $_tableName;
    protected $_referenceMap = array(
        'Model1' => array(
            'column' => 'model1_id',
            'refModelClass' => 'Vps_Model_DbWithConnection_SelectExpr_Model1'
        )
    );

    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest2');
        $config['table'] = $this->_tableName;
        parent::__construct($config);
    }

    public function setUp()
    {
        Vps_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `model1_id` INT NOT NULL ,
            `foo` VARCHAR( 200 ) NOT NULL ,
            `bar` VARCHAR( 200 ) NOT NULL
        ) ENGINE = INNODB");
        Vps_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, model1_id, foo, bar) VALUES ('1', 1, 'aaabbbccc', 'abcd')");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, model1_id, foo, bar) VALUES ('2', 1, 'bam', 'bum')");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, model1_id, foo, bar) VALUES ('3', 2, 'bam', 'bum')");
    }


    public function dropTable()
    {
        Vps_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }

}
