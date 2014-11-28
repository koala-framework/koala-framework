<?php
class Kwf_Model_DbWithConnection_ParentExprIf_ChildModel extends Kwf_Model_Db
{
    private $_tableName;
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest_child');
        $config['table'] = $this->_tableName;

        $this->_referenceMap = array(
            'Parent' => 'parent_id->Kwf_Model_DbWithConnection_ParentExprIf_ParentModel'
        );

        $this->_exprs['bar_is_null'] = new Kwf_Model_Select_Expr_IsNull('bar');
        $this->_exprs['if_field'] = new Kwf_Model_Select_Expr_If(
            new Kwf_Model_Select_Expr_Field('bar_is_null'),
            new Kwf_Model_Select_Expr_Parent('Parent', 'id'),
            new Kwf_Model_Select_Expr_Parent('Parent', 'parent_value')
        );
        $this->_exprs['if_field_sibling'] = new Kwf_Model_Select_Expr_If(
            new Kwf_Model_Select_Expr_Field('bar_is_null'),
            new Kwf_Model_Select_Expr_Parent('Parent', 'id'),
            new Kwf_Model_Select_Expr_Parent('Parent', 'sibling_value')
        );

        $this->_exprs['parent_value'] = new Kwf_Model_Select_Expr_Parent('Parent', 'parent_value');
        $this->_exprs['sibling_value'] = new Kwf_Model_Select_Expr_Parent('Parent', 'sibling_value');

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `parent_id` INT UNSIGNED NOT NULL,
            `bar` INT
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar) VALUES (1, 1, 5)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar) VALUES (2, 2, 1)");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, parent_id, bar) VALUES (3, 1, 4)");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
