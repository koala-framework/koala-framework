<?php
/**
 * @group Model_Db
 */
class Vps_Model_DbWithConnection_Test extends PHPUnit_Framework_TestCase
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
}
