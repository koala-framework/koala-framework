<?php
class Kwf_Model_Union_Dependent_Model2 extends Kwf_Model_Db
{
    protected $_columnMappings = array(
        'Kwf_Model_Union_Dependent_TestMapping' => array(
            'parent_id' => 'parent_id',
            'foo' => 'aa',
            'bar' => 'bb',
            'baz' => null,
        )
    );
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;
        parent::__construct($config);
    }

    protected function _init()
    {
        $this->_referenceMap['Parent'] = 'parent_id->Kwf_Model_Union_Dependent_Parent';
        parent::_init();
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `parent_id` INT NOT NULL,
            `aa` VARCHAR( 255 ) NULL,
            `bb` VARCHAR( 255 ) NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, aa, bb) VALUES (1, 1, 'xx', 'xx1')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, aa, bb) VALUES (2, 1, '333', 'yy1')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, aa, bb) VALUES (3, 1, 'zz', 'zz1')");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
