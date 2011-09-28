<?php
/**
 * @group Model
 */
class Vps_Model_FnF_Columns_Test extends Vps_Test_TestCase
{
    private $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_model = new Vps_Model_FnF(array(
            'columns' => array('id', 'value1', 'value2'),
            'data' => array(
                array('id' => 1, 'value1' => 'foo')
            )
        ));
    }

    public function testColumns()
    {
        $this->assertEquals($this->_model->getRow(1)->value1, 'foo');
        $this->assertEquals($this->_model->getRow(1)->value2, null);
    }

    /**
     * @expectedException Vps_Exception
     */
    public function testException()
    {
        $this->_model->getRow(1)->notexistent = 'x';
    }

    public function testNoColumnsSet()
    {
        $this->_model->setColumns(array());
        $this->_model->getRow(1)->notexistent = 'x';
    }
}
