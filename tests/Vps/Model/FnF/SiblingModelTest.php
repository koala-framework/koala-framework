<?php
class Vps_Model_FnF_SiblingModelTest extends PHPUnit_Framework_TestCase
{
    private $_model;

    public function setUp()
    {
        $this->_model = new Vps_Model_FnF_SiblingMasterModel();
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
    // TODO: id einfügen bei insert
    // _postInsert
}
