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

        $config['exprs'] = array();

        $config['exprs']['model1_bar'] =
            new Vps_Model_Select_Expr_Parent('Model1', 'bar');
        $config['exprs']['model1_bar_concat_foo2'] =
            new Vps_Model_Select_Expr_Concat(array(
                new Vps_Model_Select_Expr_Parent('Model1', 'bar'),
                'foo2'
            ));
        $config['exprs']['model1_bar_concat_string'] =
            new Vps_Model_Select_Expr_Concat(array(
                new Vps_Model_Select_Expr_Parent('Model1', 'bar'),
                new Vps_Model_Select_Expr_String('_string')
            ));
        $config['exprs']['model1_bar_concat_foo2_bar_string'] =
            new Vps_Model_Select_Expr_Concat(array(
                new Vps_Model_Select_Expr_Parent('Model1', 'bar'),
                'foo2',
                'bar',
                new Vps_Model_Select_Expr_String('_string')
            ));
        $config['exprs']['strpad_3_right'] =
            new Vps_Model_Select_Expr_StrPad('bar', 3, '0');
        $config['exprs']['strpad_4_right'] =
            new Vps_Model_Select_Expr_StrPad('bar', 4, '0');
        $config['exprs']['strpad_6_right'] =
            new Vps_Model_Select_Expr_StrPad('bar', 6, '0');
        $config['exprs']['strpad_3_left'] =
            new Vps_Model_Select_Expr_StrPad('bar', 3, '0', Vps_Model_Select_Expr_StrPad::LEFT);
        $config['exprs']['strpad_4_left'] =
            new Vps_Model_Select_Expr_StrPad('bar', 4, '0', Vps_Model_Select_Expr_StrPad::LEFT);
        $config['exprs']['strpad_6_left'] =
            new Vps_Model_Select_Expr_StrPad('bar', 6, '0', Vps_Model_Select_Expr_StrPad::LEFT);

        parent::__construct($config);
    }

    public function setUp()
    {
        Vps_Registry::get('db')->query("CREATE TABLE IF NOT EXISTS {$this->_tableName} (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `model1_id` INT NOT NULL ,
            `foo2` INT NULL ,
            `bar` VARCHAR( 200 ) NOT NULL
        ) ENGINE = INNODB");
        Vps_Registry::get('db')->query("TRUNCATE TABLE {$this->_tableName}");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, model1_id, foo2, bar) VALUES ('1', 1, 10, 'abcd')");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, model1_id, foo2, bar) VALUES ('2', 1, 10, 'bam')");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, model1_id, foo2, bar) VALUES ('3', 1, NULL, 'bam')");
        Vps_Registry::get('db')->query("INSERT INTO {$this->_tableName}
                        (id, model1_id, foo2, bar) VALUES ('4', 2, 10, 'bam')");
    }


    public function dropTable()
    {
        Vps_Registry::get('db')->query("DROP TABLE IF EXISTS {$this->_tableName}");
    }

}
