<?php
class Vps_Filter_Row_UniqueAsciiTest extends Vps_Test_TestCase
{
    public function testUniqueAscii()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'name'=>'Name1', 'test'=>'name1'),
            array('id'=>2, 'name'=>'Name2', 'test'=>'name2'),
            
        ), 'filters'=>array('test'=>new Vps_Filter_Row_UniqueAscii('name'))));

        $model->getRow(1)->save();
        $this->assertEquals('name1', $model->getRow(1)->test);

        $row = $model->createRow();
        $row->name = 'Name1';
        $row->save();

        $this->assertEquals('name1_1', $model->getRow(3)->test);

        $model->getRow(3)->save();
        $this->assertEquals('name1_1', $model->getRow(3)->test);
    }

    public function testUniqueAsciiGroupBy()
    {
        $f = new Vps_Filter_Row_UniqueAscii('name');
        $f->setGroupBy('group');
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'name'=>'Name1', 'group'=>1, 'test'=>'name1'),
            array('id'=>2, 'name'=>'Name2', 'group'=>2, 'test'=>'name2'),
        ), 'filters'=>array('test'=>$f)));

        $row = $model->createRow();
        $row->group = 1;
        $row->name = 'Name1';
        $row->save();
        $this->assertEquals('name1_1', $model->getRow(3)->test);

        $row = $model->createRow();
        $row->group = 2;
        $row->name = 'Name1';
        $row->save();
        $this->assertEquals('name1', $model->getRow(4)->test);
    }
}