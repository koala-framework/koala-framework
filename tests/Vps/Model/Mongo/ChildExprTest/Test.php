<?php
/**
 * @group Model
 * @group Mongo
 * @group Mongo_ChildExpr
 * @group slow
 */
class Vps_Model_Mongo_ChildExprTest_Test extends Vps_Test_TestCase
{
    private $_model;
    public function setUp()
    {
        $this->markTestIncomplete(); // TODO: deaktiviert, waren Fehler drinnen
        parent::setUp();

        $this->_model = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ChildExprTest_MongoModel');
        $this->_model->getCollection()->insert(
            array('id'=>1, 'a'=>'a', 'child_count'=>2) //TODO id sollte nicht nötig sein
        , array('safe'=>true));
    }

    public function tearDown()
    {
        if (isset($this->_model)) $this->_model->cleanUp();
        parent::tearDown();
    }

    public function testInitial()
    {
        $row = $this->_model->getRow(array());
        $this->assertEquals(2, $row->child_count);
        $this->assertEquals(2, $row->getChildRows('Child')->count());
    }

    public function testAddedChild()
    {
        $this->markTestIncomplete(); // TODO: deaktiviert, führte zu Endlosschleife, gehört eigentlich hergerichtet
        $childModel = Vps_Model_Abstract::getInstance('Vps_Model_Mongo_ChildExprTest_ChildModel');
        $childRow = $childModel->createRow();
        $childRow->mongo_id = 1;
        $childRow->name = 'four';
        $childRow->save();

        $row = $this->_model->getRow(array());
        $this->assertEquals(3, $row->child_count);
        $this->assertEquals(3, $row->getChildRows('Child')->count());
        $r = $this->_model->getCollection()->findOne();
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
}
