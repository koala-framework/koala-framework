<?php
class Kwf_Model_Union_MergeSelect_Test extends Kwf_Test_TestCase
{
    private $_model;

    public function setUp()
    {
        parent::setUp();
        $select = new Kwf_Model_Select();
        $select->whereEquals('active', 1);
        $this->_model = new Kwf_Model_Union(array(
            'models' => array(
                'a' => 'Kwf_Model_Union_MergeSelect_Model1',
                'b' => array(
                    'model' => 'Kwf_Model_Union_MergeSelect_Model2',
                    'select' => $select
                )
            ),
            'columnMapping' => 'Kwf_Model_Union_MergeSelect_TestMapping'
        ));
    }

    public function testSelect()
    {
        $this->assertEquals($this->_model->countRows(), 2);
        $rows = $this->_model->getRows();
        $this->assertEquals(count($rows), 2);
        $this->assertEquals('b1', $rows[1]->id);
    }
}
