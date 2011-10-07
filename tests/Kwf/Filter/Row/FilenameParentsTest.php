<?php
class Vps_Filter_Row_AutoFillTestModel extends Vps_Model_FnF
{
    protected $_rowClass = 'Vps_Filter_Row_AutoFillTestRow';
    protected $_data = array(
        array('id'=>1, 'parent_id'=>null, 'name'=>'foo1'),
        array('id'=>2, 'parent_id'=>1, 'name'=>'foo2')
    );
    protected $_referenceMap = array(
        'Parent' => array(
            'column' => 'parent_id',
            'refModelClass' => 'Vps_Filter_Row_AutoFillTestModel'
        )
    );
}

class Vps_Filter_Row_AutoFillTestRow extends Vps_Model_Row_Data_Abstract
{
    public function __toString()
    {
        return $this->name;
    }
}

class Vps_Filter_Row_FilenameParentsTest extends Vps_Test_TestCase
{
    public function testFilenameParents()
    {
        $model = new Vps_Filter_Row_AutoFillTestModel(array('filters'=>array(
            'path'=>new Vps_Filter_Row_FilenameParents()
        )));
        $row = $model->getRow(2);
        $row->save();
        
        $this->assertEquals('foo1/foo2', $model->getRow(2)->path);
    }
}
