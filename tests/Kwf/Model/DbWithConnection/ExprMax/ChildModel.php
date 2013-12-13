<?php
class Kwf_Model_DbWithConnection_ExprMax_ChildModel extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtestchild');
        $config['table'] = $this->_tableName;

        $this->_referenceMap = array(
            'Parent'=>array(
                'column' => 'parent_id',
                'refModelClass' => 'Kwf_Model_DbWithConnection_ExprMax_Model'
            )
        );

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            parent_id INT NOT NULL ,
            value2 INT NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, value2) VALUES (1, 1, 100)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, value2) VALUES (2, 1, 500)");

        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, value2) VALUES (3, 2, 100)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, value2) VALUES (4, 2, 100)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, value2) VALUES (5, 2, 100)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
