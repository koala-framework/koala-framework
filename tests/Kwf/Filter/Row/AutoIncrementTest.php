<?php
class Kwf_Filter_Row_AutoIncrementTest extends Kwf_Test_TestCase
{
    public function testNumberize()
    {
        $model = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'number'=>1, 'foo'=>'foo1'),
            array('id'=>2, 'number'=>3, 'foo'=>'foo2'),
            array('id'=>3, 'number'=>5, 'foo'=>'foo3')
        ), 'filters'=>array('number' => new Kwf_Filter_Row_AutoIncrement())));
        $row = $model->createRow();
        $row->foo = 'foo4';
        $row->save();

        $this->assertEquals('foo4', $model->getRow(4)->foo);
        $this->assertEquals(6, $model->getRow(4)->number);
        $this->assertEquals(1, $model->getRow(1)->number);
        $this->assertEquals(3, $model->getRow(2)->number);
        $this->assertEquals(5, $model->getRow(3)->number);

        $row = $model->createRow();
        $row->foo = 'foo5';
        $row->save();
        $this->assertEquals('foo5', $model->getRow(5)->foo);
        $this->assertEquals(7, $model->getRow(5)->number);
        $this->assertEquals(6, $model->getRow(4)->number);

        $model->getRow(2)->delete();
        $this->assertEquals(null, $model->getRow(2));
        $this->assertEquals(6, $model->getRow(4)->number);
        $this->assertEquals(1, $model->getRow(1)->number);
        $this->assertEquals(5, $model->getRow(3)->number);
        $this->assertEquals(7, $model->getRow(5)->number);
    }

    public function testNumberizeGroupBy()
    {
        $f = new Kwf_Filter_Row_AutoIncrement();
        $f->setGroupBy('group');
        $model = new Kwf_Model_FnF(array('data'=>array(
            array('id'=>1, 'number'=>1, 'group'=>1, 'foo'=>'foo1'),
            array('id'=>2, 'number'=>1, 'group'=>2, 'foo'=>'foo2'),
            array('id'=>3, 'number'=>2, 'group'=>1, 'foo'=>'foo3')
        ), 'filters'=>array('number'=>$f)));
        $row = $model->createRow();
        $row->group = 2;
        $row->foo = 'foo4';
        $row->save();
        $this->assertEquals(2, $model->getRow(4)->number);
        $this->assertEquals(1, $model->getRow(2)->number);
        $this->assertEquals(1, $model->getRow(1)->number);
        $this->assertEquals(1, $model->getRow(2)->number);

        $model->getRow(2)->delete();
        $this->assertEquals(1, $model->getRow(1)->number);
        $this->assertEquals(2, $model->getRow(3)->number);
        $this->assertEquals(2, $model->getRow(4)->number);
    }
}
