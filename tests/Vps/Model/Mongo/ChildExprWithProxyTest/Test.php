<?php
/**
 * @group Model
 * @group Mongo
 * @group Mongo_ChildExprWithProxy
 * @group slow
 */
class Vps_Model_Mongo_ChildExprWithProxyTest_Test extends PHPUnit_Framework_TestCase
{
    private $_model;
    public function setUp()
    {
        Vps_Model_Abstract::clearInstances();

        $this->_model = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ChildExprWithProxyTest_MongoModel');
        $this->_model->getProxyModel()->getCollection()->insert(
            array('id'=>1, 'a'=>'a', 'child_count'=>2) //TODO id sollte nicht nötig sein
        , array('safe'=>true));
    }

    protected function tearDown()
    {
        if (isset($this->_model)) $this->_model->getProxyModel()->cleanUp();
        Vps_Model_Abstract::clearInstances();
    }

    public function testAddedChild()
    {
        $childModel = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ChildExprWithProxyTest_ChildModel');
        $childRow = $childModel->createRow();
        $childRow->mongo_id = 1;
        $childRow->name = 'four';
        $childRow->save();

        $row = $this->_model->getRow(array());
        $this->assertEquals(3, $row->child_count);
        $this->assertEquals(3, $row->getChildRows('Child')->count());
        $r = $this->_model->getProxyModel()->getCollection()->findOne();
        $this->assertEquals(3, $r['child_count']);
    }


    public function testDeletedChild()
    {
        $this->markTestIncomplete();
    }

    public function testChangedChild()
    {
        $this->markTestIncomplete();
    }

    public function testNewRow()
    {
        $this->markTestIncomplete();
    }

    public function testWithProxies()
    {
        $this->markTestIncomplete();
    }

    public function testWithMultipleRelations()
    {
        $this->markTestIncomplete();
    }
}
