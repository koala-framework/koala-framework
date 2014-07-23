<?php
class Kwf_Model_DbWithConnection_ReferencesWithinModel_Model extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_references_within');
        $config['table'] = $this->_tableName;

        $this->_referenceMap = array(
            'Parent' => 'parent_id->Kwf_Model_DbWithConnection_ReferencesWithinModel_Model'
        );
        $this->_dependentModels['Childs'] = 'Kwf_Model_DbWithConnection_ReferencesWithinModel_Model';

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `parent_id` INT UNSIGNED NULL,
            `bar` INT
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar) VALUES (1, null, 5)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar) VALUES (2, 1, 1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar) VALUES (3, 1, 4)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }

    protected function _init()
    {
        parent::_init();
        $this->_exprs['parent_bar'] = new Kwf_Model_Select_Expr_Parent('Parent', 'bar');
    }
}
