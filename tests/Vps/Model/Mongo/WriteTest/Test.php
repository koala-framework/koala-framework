<?php
/**
 * @group Model
 * @group Mongo
 */
class Vps_Model_Mongo_WriteTest_Test extends PHPUnit_Framework_TestCase
{
    /**
     * @var Vps_Model_Mongo
     */
    private $_model;
    public function setUp()
    {
        Vps_Model_Abstract::clearInstances();

        $this->_model = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_WriteTest_MongoModel');
    }

    protected function tearDown()
    {
        if ($this->_model) $this->_model->cleanUp();
        Vps_Model_Abstract::clearInstances();
    }

    public function testInsert()
    {
        $row = $this->_model->createRow();
        $row->foo = 'foo';
        $row->bar = 123;
        $row->save();

        $r = $this->_model->getCollection()->findOne();
        $this->assertEquals('foo', $r['foo']);
        $this->assertEquals(123, $r['bar']);
    }

    public function testUpdate()
    {
        $this->_model->getCollection()->insert(
            array('id'=>100, 'a'=>'a') //TODO id sollte nicht nötig sein
        , array('safe'=>true));

        $row = $this->_model->getRow(array());
        $row->foo = 'foo';
        $row->bar = 123;
        $row->save();

        $r = $this->_model->getCollection()->findOne();
        $this->assertEquals('foo', $r['foo']);
        $this->assertEquals(123, $r['bar']);

    }
    public function testDelete()
    {
        $this->_model->getCollection()->insert(
            array('id'=>100, 'a'=>'a') //TODO id sollte nicht nötig sein
        , array('safe'=>true));

        $row = $this->_model->getRow(100);
        $row->delete();

        $this->assertEquals(0, $this->_model->getCollection()->find()->count());
    }
}
