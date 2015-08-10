<?php
class Kwf_Model_Union_Db_ModelSibling extends Kwf_Model_Db
{
    protected $_referenceMap = array(
        'Master' => array(
            'column' => 'id',
            'refModelClass' => 'Kwf_Model_Union_Db_TestModel'
        )
    );

    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;
        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id`  VARCHAR( 100 ) NOT NULL PRIMARY KEY ,
            `sib` VARCHAR( 255 ) NULL,
            `sib_bool` tinyint(1) NOT NULL default '0'
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, sib, sib_bool) VALUES ('1m1', 's1', 1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, sib, sib_bool) VALUES ('1m2', 'ss2', 0)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, sib, sib_bool) VALUES ('2m2', 'sss3', 1)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
