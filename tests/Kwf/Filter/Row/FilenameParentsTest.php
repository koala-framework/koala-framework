<?php
class Kwf_Filter_Row_AutoFillTestModel extends Kwf_Model_FnF
{
    protected $_rowClass = 'Kwf_Filter_Row_AutoFillTestRow';
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

class Kwf_Filter_Row_AutoFillTestRow extends Kwf_Model_Row_Data_Abstract
{
    public function __toString()
    {
        return $this->name;
    }
}

class Kwf_Filter_Row_FilenameParentsTest extends Kwf_Test_TestCase
{
    public function testFilenameParents()
    {
        $model = new Kwf_Filter_Row_AutoFillTestModel(array('filters'=>array(
            'path'=>new Kwf_Filter_Row_FilenameParents()
        )));
        $row = $model->getRow(2);
        $row->save();
        
        $this->assertEquals('foo1/foo2', $model->getRow(2)->path);
    }
}
