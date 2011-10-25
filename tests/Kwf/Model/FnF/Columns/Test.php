<?php
/**
 * @group Model
 */
class Kwf_Model_FnF_Columns_Test extends Kwf_Test_TestCase
{
    private $_model;

    public function setUp()
    {
        parent::setUp();
        $this->_model = new Kwf_Model_FnF(array(
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
     * @expectedException Kwf_Exception
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
