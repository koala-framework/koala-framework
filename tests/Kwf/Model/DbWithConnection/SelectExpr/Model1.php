<?php
class Kwf_Model_DbWithConnection_SelectExpr_Model1 extends Kwf_Model_Db
{
    private $_tableName;
    protected $_dependentModels = array(
        'Model2' => 'Kwf_Model_DbWithConnection_SelectExpr_Model2'
    );
    public function __construct($config = array())
    {
        $this->_tableName = uniqid('dbtest');
        $config['table'] = $this->_tableName;

        $config['exprs'] = array();

        $config['exprs']['count_model2']
            = new Kwf_Model_Select_Expr_Child_Count('Model2');

        $config['exprs']['count_model2_field']
            = new Kwf_Model_Select_Expr_Child('Model2',
                    new Kwf_Model_Select_Expr_Count('foo2'));

        $config['exprs']['count_model2_distinct']
            = new Kwf_Model_Select_Expr_Child('Model2',
                    new Kwf_Model_Select_Expr_Count('foo2', true));

        $config['exprs']['sum_model2']
            = new Kwf_Model_Select_Expr_Child('Model2',
                    new Kwf_Model_Select_Expr_Sum('foo2'));

        $select = new Kwf_Model_Select();
        $select->whereEquals('bar', 'bam');
        $config['exprs']['count_model2_bam']
            = new Kwf_Model_Select_Expr_Child('Model2',
                    new Kwf_Model_Select_Expr_Count(),
                    $select);

        $config['exprs']['count_model2_bam_distinct']
            = new Kwf_Model_Select_Expr_Child('Model2',
                    new Kwf_Model_Select_Expr_Count('foo2', true),
                    $select);

        $config['exprs']['sum_model2_bam']
            = new Kwf_Model_Select_Expr_Child('Model2',
                    new Kwf_Model_Select_Expr_Sum('foo2'),
                    $select);

        parent::__construct($config);
    }

    public function setUp()
    {
        Kwf_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `foo` VARCHAR( 200 ) NOT NULL ,
            `bar` VARCHAR( 200 ) NOT NULL
        ) ENGINE = INNODB");
        Kwf_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, bar) VALUES ('1', 'aaabbbccc', 'abcd')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, bar) VALUES ('2', 'bam', 'bum')");
        Kwf_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, foo, bar) VALUES ('3', 'bam2', 'bum2')");
    }

    public function dropTable()
    {
        Kwf_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }
}
