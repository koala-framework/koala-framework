<?php
/**
 * @group Model
 * @group Mongo
 * @group Mongo_ChildRows
 * @group slow
 */
class Vps_Model_Mongo_ChildRowsTest_Test extends Vps_Test_TestCase
{
    private $_model;
    public function setUp()
    {
        parent::setUp();

        $this->_model = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ChildRowsTest_MongoModel');
        $this->_model->getCollection()->insert(
            array('id'=>1, 'a'=>'a', 'foo'=>array(array('x'=>1), array('x'=>2))) //TODO id sollte nicht nötig sein
        , array('safe'=>true));
    }

    public function tearDown()
    {
        if (isset($this->_model)) $this->_model->cleanUp();
        parent::tearDown();
    }

    public function testRead()
    {
        $row = $this->_model->getRow(1);
        $rows = $row->getChildRows('Foo');
        $this->assertEquals(2, count($rows));
        $this->assertEquals(1, $rows->current()->x);
    }

    public function testReadAutomaticInternalId()
    {
        $row = $this->_model->getRow(1);
        $rows = $row->getChildRows('Foo');
        $this->assertEquals(2, count($rows));

        $pk = $rows->current()->getModel()->getPrimaryKey();
        $this->assertEquals('intern_id', $pk);
        $this->assertEquals(1, $rows->current()->$pk);
    }

    public function testCreateChild()
    {
        $row = $this->_model->getRow(1);
        $crow = $row->createChildRow('Foo');
        $crow->x = 3;
        $row->save();
        $this->assertEquals(3, count($row->getChildRows('Foo')));

        $row = $this->_model->getCollection()->findOne(array('a'=>'a'));
        $this->assertEquals(3, count($row['foo']));
        $this->assertEquals(3, $row['foo'][2]['x']);
    }

    public function testDeleteChild()
    {
        $row = $this->_model->getRow(1);
        $crows = $row->getChildRows('Foo');
        $crows->current()->delete();
        $row->save();
        $this->assertEquals(1, count($row->getChildRows('Foo')));

        $row = $this->_model->getCollection()->findOne(array('a'=>'a'));
        $this->assertEquals(1, count($row['foo']));
    }

    public function testUpdateChild()
    {
        $row = $this->_model->getRow(1);
        $crows = $row->getChildRows('Foo');
        $crows->current()->x = 1234;
        $row->save();
        $this->assertEquals(2, count($row->getChildRows('Foo')));

        $row = $this->_model->getCollection()->findOne(array('a'=>'a'));
        $this->assertEquals(2, count($row['foo']));
        $this->assertEquals(1234, $row['foo'][0]['x']);
    }

}
