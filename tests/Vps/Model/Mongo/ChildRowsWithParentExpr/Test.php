<?php
/**
 * @group Mongo
 * @group Mongo_ChildRowsWithParentExpr
 * @group slow
 */
class Vps_Model_Mongo_ChildRowsWithParentExpr_Test extends PHPUnit_Framework_TestCase
{
    private $_model;
    public function setUp()
    {
        Vps_Model_Abstract::clearInstances();

        $this->_model = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ChildRowsWithParentExpr_MongoModel');
        $this->_model->getCollection()->insert(
            array('id'=>1, 'name'=>'a', 'foo'=>array(array('x'=>1), array('x'=>2))) //TODO id sollte nicht nötig sein
        , array('safe'=>true));
    }

    protected function tearDown()
    {
        if (isset($this->_model)) $this->_model->cleanUp();
        Vps_Model_Abstract::clearInstances();
    }

    public function testParentRowFromSubModel()
    {
        $row = $this->_model->getRow(1);
        $rows = $row->getChildRows('Foo');
        $this->assertEquals(2, count($rows));
        $this->assertEquals(1, $rows->current()->x);
        $this->assertSame($rows->current()->getParentRow('Mongo'), $row);
    }

    public function testParentExpr()
    {
        $row = $this->_model->getRow(1);
        $rows = $row->getChildRows('Foo');
        $this->assertEquals('a', $rows->current()->mongo_name);

        $row->name = 'b';
        $row->save();
        $this->_model->cleanRows();

        $row = $this->_model->getRow(1);
        $rows = $row->getChildRows('Foo');
        $this->assertSame($rows->current()->getParentRow('Mongo'), $row);
        $this->assertEquals('b', $rows->current()->getParentRow('Mongo')->name);
        $this->assertEquals('b', $rows->current()->mongo_name);
    }
}
