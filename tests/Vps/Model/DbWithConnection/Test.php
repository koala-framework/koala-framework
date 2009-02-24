<?php
/**
 * @group Model_Db
 */
class Vps_Model_DbWithConnection_Test extends PHPUnit_Extensions_OutputTestCase
{
    private $_tableName;
    public function setUp()
    {
        $this->_tableName = 'test'.uniqid();
        $sql = "CREATE TABLE $this->_tableName (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `test1` VARCHAR( 200 ) NOT NULL,
            `test2` VARCHAR( 200 ) NOT NULL  
        ) ENGINE = INNODB";
        Vps_Registry::get('db')->query($sql);
        $m = new Vps_Model_Db(array(
            'table' => $this->_tableName
        ));
        $r = $m->createRow();
        $r->test1 = '1x1';
        $r->test2 = '1';
        $r->save();
    }

    public function tearDown()
    {
        Vps_Registry::get('db')->query("DROP TABLE {$this->_tableName}");
    }

    public function testIt()
    {
        $model = new Vps_Model_Db(array(
            'table' => $this->_tableName
        ));
        $this->assertEquals('1x1', $model->getRow(1)->test1);
    }

    public function testEscaping()
    {
        $values = array('a\'b', '\'?', '?', 'a?b', 'a"b', 'a\\b', 'a\\\'b');
        $model = new Vps_Model_Db(array(
            'table' => $this->_tableName
        ));

        $this->expectOutputString(str_repeat("WARNING: ? and ' are used together in an sql query value. This is a problem because of an Php bug. ' is ignored.\n", 2));

        foreach ($values as $v) {
            $s = $model->select()->whereEquals('test1', $v);
            $model->getRow($s);

            $s = $model->select()->where(new Vps_Model_Select_Expr_Equals('test1', $v));
            $model->getRow($s);
        }
    }

    /**
     * @group slow
     */
    public function testEscapingBruteForce()
    {
        $model = new Vps_Model_Db(array(
            'table' => $this->_tableName
        ));
        for($i=0;$i<1000;$i++) {
            $v = '';
            for($j=0;$j<10;$j++) {
                $v .= chr(rand(0, 255));
            }

            $s = $model->select()->whereEquals('test1', $v);
            $model->getRow($s);

            $s = $model->select()->where(new Vps_Model_Select_Expr_Equals('test1', $v));
            $model->getRow($s);
        }
    }
}
