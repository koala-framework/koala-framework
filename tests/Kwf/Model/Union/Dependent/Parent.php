<?php
class Kwf_Model_Union_Dependent_Parent extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;
        parent::__construct($config);
    }

    protected function _init()
    {
        $this->_dependentModels['TestModel'] = 'Kwf_Model_Union_Dependent_TestModel';
        parent::_init();
    }


    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `nm` VARCHAR( 255 ) NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, nm) VALUES (1, 'asdfasdf')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, nm) VALUES (2, 'fdsafdsa')");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
