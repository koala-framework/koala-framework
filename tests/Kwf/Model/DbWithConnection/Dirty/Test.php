<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Db_Dirty
 */
class Kwf_Model_DbWithConnection_Dirty_Test extends Kwf_Test_TestCase
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
        Kwf_Registry::get('db')->query($sql);
        $m = new Kwf_Model_Db(array(
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
        Kwf_Registry::get('db')->query("DROP TABLE {$this->_tableName}");
        parent::tearDown();
    }

    public function testDontSaveNotDirtyRow()
    {
        Kwf_Model_DbWithConnection_Dirty_Row::resetMock();

        $table = new Kwf_Model_DbWithConnection_Dirty_Table(array(
            'name' => $this->_tableName,
        ));
        $model = new Kwf_Model_Db(array(
            'table' => $table
        ));

        $row = $model->getRow(1);
        $row->save();

        $this->assertEquals(0, Kwf_Model_DbWithConnection_Dirty_Row::$saveCount);

        $row = $model->getRow(1);
        $row->test1 = 'foo';
        $row->save();

        $this->assertEquals(0, Kwf_Model_DbWithConnection_Dirty_Row::$saveCount);
    }

    public function testNotDirtyForceSave()
    {
        Kwf_Model_DbWithConnection_Dirty_Row::resetMock();

        $table = new Kwf_Model_DbWithConnection_Dirty_Table(array(
            'name' => $this->_tableName,
        ));
        $model = new Kwf_Model_Db(array(
            'table' => $table
        ));

        $row = $model->getRow(1);
        $row->forceSave();

        $this->assertEquals(1, Kwf_Model_DbWithConnection_Dirty_Row::$saveCount);

        $row = $model->getRow(1);
        $row->test1 = 'foo';
        $row->forceSave();

        $this->assertEquals(2, Kwf_Model_DbWithConnection_Dirty_Row::$saveCount);
    }

    public function testSaveNewRowNotDirty()
    {
        Kwf_Model_DbWithConnection_Dirty_Row::resetMock();

        $table = new Kwf_Model_DbWithConnection_Dirty_Table(array(
            'name' => $this->_tableName,
        ));
        $model = new Kwf_Model_Db(array(
            'table' => $table
        ));

        $row = $model->createRow();
        $row->save();
        $this->assertEquals(1, Kwf_Model_DbWithConnection_Dirty_Row::$saveCount);
    }

    public function testSaveDirtyRow()
    {
        Kwf_Model_DbWithConnection_Dirty_Row::resetMock();

        $table = new Kwf_Model_DbWithConnection_Dirty_Table(array(
            'name' => $this->_tableName,
        ));
        $model = new Kwf_Model_Db(array(
            'table' => $table
        ));

        $row = $model->getRow(1);
        $row->test1 = 'blubb';
        $row->save();

        $this->assertEquals(1, Kwf_Model_DbWithConnection_Dirty_Row::$saveCount);

        Kwf_Model_DbWithConnection_Dirty_Row::resetMock();
        $row = $model->getRow(1);
        $row->test3 = '77';
        $row->save();

        $this->assertEquals(1, Kwf_Model_DbWithConnection_Dirty_Row::$saveCount);

        Kwf_Model_DbWithConnection_Dirty_Row::resetMock();
        $row = $model->createRow();
        $row->test1 = 'xx';
        $row->test2 = 'yy';
        $row->save();

        $this->assertEquals(1, Kwf_Model_DbWithConnection_Dirty_Row::$saveCount);
    }

    public function testDirtyColumns()
    {
        $model = new Kwf_Model_Db(array(
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
        $model = new Kwf_Model_Proxy(array(
            'proxyModel' => new Kwf_Model_Db(array(
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
