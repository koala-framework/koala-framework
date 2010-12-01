<?php
class Vps_Filter_Row_AutoFillTest extends Vps_Test_TestCase
{
    public function testAutoFill()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
        ), 'filters'=>array('test'=>new Vps_Filter_Row_AutoFill('x{foo}'))));
        $row = $model->createRow();
        $row->foo = 'foo4';
        $row->save();

        $this->assertEquals('xfoo4', $model->getRow(1)->test);
    }
    public function testAutoFillId()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
        ), 'filters'=>array('test'=>new Vps_Filter_Row_AutoFill('x{id}'))));
        $row = $model->createRow();
        $row->foo = 'foo4';
        $row->save();

        $this->assertEquals('x1', $model->getRow(1)->test);
    }
}
