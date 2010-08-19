<?php
/**
 * @group Mongo
 * @group slow
 */
class Vps_Model_Mongo_ExprTest_Test extends PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        Vps_Model_Abstract::clearInstances();

        $mongoModel = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ExprTest_MongoModel');
        $mongoModel->getCollection()->insert(array('a'=>'a', 'parent_id'=>1), array('safe'=>true));
    }

    protected function tearDown()
    {
        Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ExprTest_MongoModel')->cleanUp();
        Vps_Model_Abstract::clearInstances();
    }

    public function testInitial()
    {
        $mongoModel = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ExprTest_MongoModel');
        $row = $mongoModel->getRow(array());
        $this->assertEquals('one', $row->parent_name);
        $this->assertEquals('one', $row->getParentRow('Parent')->name);
    }

    public function testChangedName()
    {
        $parentModel = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ExprTest_ParentModel');
        $parentRow = $parentModel->getRow(1);
        $parentRow->name = 'onex';

        $mongoModel = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ExprTest_MongoModel');
        $row = $mongoModel->getRow(array());
        $this->assertEquals('onex', $row->parent_name);
        $this->assertEquals('onex', $row->getParentRow('Parent')->name);
    }
}
