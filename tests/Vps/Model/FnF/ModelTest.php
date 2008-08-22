<?php
class Vps_Model_FnF_ModelTest extends PHPUnit_Framework_TestCase
{
    public function testData()
    {
        $model = new Vps_Model_FnF();
        $model->setData(array(
            array('id' => 1, 'value' => 'foo'),
            array('id' => 2, 'value' => 'bar'),
        ));
        $this->assertEquals(count($model->fetchAll()), 2);
        $this->assertEquals(count($model->find(1)), 1);
        $this->assertEquals($model->find(2)->current()->value, 'bar');
        $this->assertEquals($model->fetchAll()->current()->value, 'foo');
        $this->assertEquals(count($model->find(3)), 0);
    }
}
