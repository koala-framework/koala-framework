<?php
class Kwf_Model_DbWithConnection_ExprGroupConcat_ChildModel extends Kwf_Model_Db
{
    protected $_referenceMap = array(
        'Parent' => 'parent_id->Kwf_Model_DbWithConnection_ExprGroupConcat_Model'
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
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            parent_id INT NOT NULL,
            sort_field INT NULL,
            sort_field_2 VARCHAR(50) NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, sort_field, sort_field_2) VALUES (1, 1, 3, 'bbb')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, sort_field, sort_field_2) VALUES (2, 1, 1, 'aaa')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, sort_field, sort_field_2) VALUES (3, 2, 2, 'ccc')");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
