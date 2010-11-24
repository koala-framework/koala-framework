<?php
class Vps_Filter_Row_NumberizeTest extends Vps_Test_TestCase
{
    public function testNumberize()
    {
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'foo'=>'foo1'),
            array('id'=>2, 'pos'=>2, 'foo'=>'foo2'),
            array('id'=>3, 'pos'=>3, 'foo'=>'foo3')
        ), 'filters'=>array('pos')));
        $row = $model->createRow();
        $row->pos = 1;
        $row->foo = 'foo4';
        $row->save();

        $this->assertEquals('foo4', $model->getRow(4)->foo);
        $this->assertEquals(1, $model->getRow(4)->pos);
        $this->assertEquals(2, $model->getRow(1)->pos);
        $this->assertEquals(3, $model->getRow(2)->pos);
        $this->assertEquals(4, $model->getRow(3)->pos);

        $row = $model->createRow();
        $row->foo = 'foo5';
        $row->save();
        $this->assertEquals('foo5', $model->getRow(5)->foo);
        $this->assertEquals(5, $model->getRow(5)->pos);
        $this->assertEquals(1, $model->getRow(4)->pos);

        $model->getRow(2)->delete();
        $this->assertEquals(null, $model->getRow(2));
        $this->assertEquals(1, $model->getRow(4)->pos);
        $this->assertEquals(2, $model->getRow(1)->pos);
        $this->assertEquals(3, $model->getRow(3)->pos);
        $this->assertEquals(4, $model->getRow(5)->pos);
    }

    public function testNumberizeGroupBy()
    {
        $f = new Vps_Filter_Row_Numberize();
        $f->setGroupBy('group');
        $model = new Vps_Model_FnF(array('data'=>array(
            array('id'=>1, 'pos'=>1, 'group'=>1, 'foo'=>'foo1'),
            array('id'=>2, 'pos'=>1, 'group'=>2, 'foo'=>'foo2'),
            array('id'=>3, 'pos'=>2, 'group'=>1, 'foo'=>'foo3')
        ), 'filters'=>array('pos'=>$f)));
        $row = $model->createRow();
        $row->pos = 1;
        $row->group = 2;
        $row->foo = 'foo4';
        $row->save();
        $this->assertEquals(1, $model->getRow(4)->pos);
        $this->assertEquals(2, $model->getRow(2)->pos);
        $this->assertEquals(1, $model->getRow(1)->pos);
        $this->assertEquals(2, $model->getRow(2)->pos);
    }
}
