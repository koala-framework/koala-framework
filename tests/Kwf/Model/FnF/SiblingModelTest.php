<?php
/**
 * @group Model
 * @group SiblingModel
 */
class Vps_Model_FnF_SiblingModelTest extends Vps_Test_TestCase
{
    private $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_model = new Vps_Model_FnF_SiblingMasterModel();
        Vps_Model_Abstract::getInstance('Vps_Model_FnF_SiblingModel')->setData(
            array(
                array('master_id'=>1, 'bar'=>'bar1'),
                array('master_id'=>2, 'bar'=>'bar2'),
                array('master_id'=>3, 'bar'=>'bar3'),
                array('master_id'=>100, 'bar'=>'bar3')
            )
        );
    }
    public function testData()
    {
        $row = $this->_model->getRow(1);
        $this->assertEquals($row->foo, 'foo1');
        $this->assertEquals($row->bar, 'bar1');

        $row = $this->_model->getRows()->current();
        $this->assertEquals($row->foo, 'foo1');
        $this->assertEquals($row->bar, 'bar1');

        $row = $this->_model->getRow(3);
        $this->assertEquals($row->foo, 'foo3');
        $this->assertEquals($row->bar, 'bar3');
    }

    public function testChangeId()
    {
        $row = $this->_model->getRow(1);
        $row->id = 10;
        $this->assertEquals($row->master_id, 10);
    }

    public function testAddEntryWithManualId()
    {
        $row = $this->_model->createRow();
        $row->id = 11;
        $row->save();
        $this->assertEquals(11, $row->master_id);
        $data = Vps_Model_Abstract::getInstance('Vps_Model_FnF_SiblingModel')->getData();
        $this->assertEquals(11, $data[4]['master_id']);
    }

    public function testAddEntryWithAutoIncrementId()
    {
        $row = $this->_model->createRow();
        $row->save();
        $this->assertEquals(4, $row->id);
        $this->assertEquals(4, $row->master_id);
        $data = Vps_Model_Abstract::getInstance('Vps_Model_FnF_SiblingModel')->getData();
        $this->assertEquals(4, $data[4]['master_id']);
    }

    public function testDuplicateSiblingRow()
    {
        $newRow = $this->_model->getRow(1)->duplicate();

        $this->assertEquals(4, $newRow->id);
        $this->assertEquals(4, $newRow->master_id);
        $this->assertEquals('bar1', $newRow->bar);
        $this->assertEquals('foo1', $newRow->foo);
    }
}
