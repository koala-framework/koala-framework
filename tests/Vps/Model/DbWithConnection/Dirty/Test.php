<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Db_Dirty
 */
class Vps_Model_DbWithConnection_Dirty_Test extends Vps_Test_TestCase
{
    private $_tableName;
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
        Vps_Registry::get('db')->query($sql);
        $m = new Vps_Model_Db(array(
            'table' => $this->_tableName
        ));
        $r = $m->createRow();
        $r->test1 = 'foo';
        $r->test2 = 'bar';
        $r->test3 = null;
        $r->save();
    }

    public function tearDown()
    {
        Vps_Registry::get('db')->query("DROP TABLE {$this->_tableName}");
        parent::tearDown();
    }

    public function testDontSaveNotDirtyRow()
    {
        Vps_Model_DbWithConnection_Dirty_Row::resetMock();

        $table = new Vps_Db_Table(array(
            'name' => $this->_tableName,
            'rowClass' => 'Vps_Model_DbWithConnection_Dirty_Row'
        ));
        $model = new Vps_Model_Db(array(
            'table' => $table
        ));

        $row = $model->getRow(1);
        $row->save();

        $this->assertEquals(0, Vps_Model_DbWithConnection_Dirty_Row::$saveCount);

        $row = $model->getRow(1);
        $row->test1 = 'foo';
        $row->save();

        $this->assertEquals(0, Vps_Model_DbWithConnection_Dirty_Row::$saveCount);
    }

    public function testNotDirtyForceSave()
    {
        Vps_Model_DbWithConnection_Dirty_Row::resetMock();

        $table = new Vps_Db_Table(array(
            'name' => $this->_tableName,
            'rowClass' => 'Vps_Model_DbWithConnection_Dirty_Row'
        ));
        $model = new Vps_Model_Db(array(
            'table' => $table
        ));

        $row = $model->getRow(1);
        $row->forceSave();

        $this->assertEquals(1, Vps_Model_DbWithConnection_Dirty_Row::$saveCount);

        $row = $model->getRow(1);
        $row->test1 = 'foo';
        $row->forceSave();

        $this->assertEquals(2, Vps_Model_DbWithConnection_Dirty_Row::$saveCount);
    }

    public function testSaveNewRowNotDirty()
    {
        Vps_Model_DbWithConnection_Dirty_Row::resetMock();

        $table = new Vps_Db_Table(array(
            'name' => $this->_tableName,
            'rowClass' => 'Vps_Model_DbWithConnection_Dirty_Row'
        ));
        $model = new Vps_Model_Db(array(
            'table' => $table
        ));

        $row = $model->createRow();
        $row->save();
        $this->assertEquals(1, Vps_Model_DbWithConnection_Dirty_Row::$saveCount);
    }

    public function testSaveDirtyRow()
    {
        Vps_Model_DbWithConnection_Dirty_Row::resetMock();

        $table = new Vps_Db_Table(array(
            'name' => $this->_tableName,
            'rowClass' => 'Vps_Model_DbWithConnection_Dirty_Row'
        ));
        $model = new Vps_Model_Db(array(
            'table' => $table
        ));

        $row = $model->getRow(1);
        $row->test1 = 'blubb';
        $row->save();

        $this->assertEquals(1, Vps_Model_DbWithConnection_Dirty_Row::$saveCount);

        Vps_Model_DbWithConnection_Dirty_Row::resetMock();
        $row = $model->getRow(1);
        $row->test3 = '77';
        $row->save();

        $this->assertEquals(1, Vps_Model_DbWithConnection_Dirty_Row::$saveCount);

        Vps_Model_DbWithConnection_Dirty_Row::resetMock();
        $row = $model->createRow();
        $row->test1 = 'xx';
        $row->test2 = 'yy';
        $row->save();

        $this->assertEquals(1, Vps_Model_DbWithConnection_Dirty_Row::$saveCount);
    }

    public function testDirtyColumns()
    {
        $model = new Vps_Model_Db(array(
            'table' => $this->_tableName
        ));

        $row = $model->getRow(1);
        $this->assertEquals($row->getDirtyColumns(), array());
        $this->assertEquals($row->isDirty(), false);
        $this->assertEquals($row->getCleanValue('test1'), 'foo');
        $row->test1 = 'blubb';
        $this->assertEquals($row->getDirtyColumns(), array('test1'));
        $this->assertEquals($row->isDirty(), true);
        $this->assertEquals($row->getCleanValue('test1'), 'foo');
    }

    public function testDirtyColumnsWithProxy()
    {
        $model = new Vps_Model_Proxy(array(
            'proxyModel' => new Vps_Model_Db(array(
                'table' => $this->_tableName
            )
        )));

        $row = $model->getRow(1);
        $this->assertEquals($row->getDirtyColumns(), array());
        $this->assertEquals($row->isDirty(), false);
        $this->assertEquals($row->getCleanValue('test1'), 'foo');
        $row->test1 = 'blubb';
        $this->assertEquals($row->getDirtyColumns(), array('test1'));
        $this->assertEquals($row->isDirty(), true);
        $this->assertEquals($row->getCleanValue('test1'), 'foo');
    }
}
