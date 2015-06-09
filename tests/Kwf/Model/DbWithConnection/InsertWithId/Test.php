<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Db_Dirty
 */
class Kwf_Model_DbWithConnection_InsertWithId_Test extends Kwf_Test_TestCase
{
    private $_tableName;
    private $_model;
    public function setUp()
    {
        parent::setUp();
        $this->_tableName = 'test'.uniqid();
        $sql = "CREATE TABLE $this->_tableName (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `test1` VARCHAR( 200 ) character set utf8 NOT NULL,
            `test2` VARCHAR( 200 ) character set utf8 NOT NULL,
            `test3` INT(10) UNSIGNED NULL
        ) ENGINE = INNODB DEFAULT CHARSET=utf8";
        Kwf_Registry::get('db')->query($sql);
        Kwf_Registry::get('db')->query("INSERT INTO $this->_tableName SET id=1, test1='a', test2='b', test3=10");
        $this->_model = new Kwf_Model_DbWithConnection_InsertWithId_Model(array(
            'table' => $this->_tableName,
        ));

        Kwf_Model_DbWithConnection_InsertWithId_Row::$updateCount = 0;
        Kwf_Model_DbWithConnection_InsertWithId_Row::$saveCount = 0;
        Kwf_Model_DbWithConnection_InsertWithId_Row::$insertCount = 0;

    }

    public function tearDown()
    {
        Kwf_Registry::get('db')->query("DROP TABLE {$this->_tableName}");
        parent::tearDown();
    }

    public function testSave()
    {
        $r = $this->_model->getRow(1);
        $r->test1 = 'x';
        $r->save();
        $this->assertEquals(1, Kwf_Model_DbWithConnection_InsertWithId_Row::$updateCount);
        $this->assertEquals(1, Kwf_Model_DbWithConnection_InsertWithId_Row::$saveCount);
        $this->assertEquals(0, Kwf_Model_DbWithConnection_InsertWithId_Row::$insertCount);
    }

    public function testInsert()
    {
        $r = $this->_model->createRow();
        $r->test1 = 'x';
        $r->save();
        $this->assertEquals(0, Kwf_Model_DbWithConnection_InsertWithId_Row::$updateCount);
        $this->assertEquals(1, Kwf_Model_DbWithConnection_InsertWithId_Row::$saveCount);
        $this->assertEquals(1, Kwf_Model_DbWithConnection_InsertWithId_Row::$insertCount);
    }

    public function testInsertWithId()
    {
        $r = $this->_model->createRow();
        $r->id = 2;
        $r->test1 = 'x';
        $r->save();
        $this->assertEquals(0, Kwf_Model_DbWithConnection_InsertWithId_Row::$updateCount);
        $this->assertEquals(1, Kwf_Model_DbWithConnection_InsertWithId_Row::$saveCount);
        $this->assertEquals(1, Kwf_Model_DbWithConnection_InsertWithId_Row::$insertCount);
    }
}
