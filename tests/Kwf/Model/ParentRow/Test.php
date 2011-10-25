<?php
class Kwf_Model_ParentRow_TestModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1, 'parent_id'=>null, 'name'=>'foo1'),
        array('id'=>2, 'parent_id'=>1, 'name'=>'foo2')
    );
    protected $_referenceMap = array(
        'Parent' => array(
            'column' => 'parent_id',
            'refModelClass' => 'Kwf_Filter_Row_AutoFillTestModel'
        )
    );
}

/**
 * @group Model
 */
class Kwf_Model_ParentRow_Test extends Kwf_Test_TestCase
{
    public function testParentRow()
    {
        $model = new Kwf_Model_ParentRow_TestModel();
        $row = $model->getRow(2);
        $row = $row->getParentRow('Parent');
        $this->assertEquals(1, $row->id);
        $row = $row->getParentRow('Parent');
        $this->assertNull($row);
    }
}
