<?php
class Kwf_Model_FnF_DeletedFlag_Test extends Kwf_Test_TestCase
{
    public function testCompleteDelete()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'foo'),
            'data'=>array(
                array('id'=>1, 'foo'=>'bar'),
                array('id'=>2, 'foo'=>'bar2')
        )));
        $model->getRow(1)->delete();
        $this->assertEquals(1, count($model->getRows()));
    }
    public function testDeleteWithFlag()
    {
        $model = new Kwf_Model_FnF_DeletedFlag_Model(array(
            'columns' => array('id', 'foo', 'deleted'),
            'data'=>array(
                array('id'=>1, 'foo'=>'bar', 'deleted' => 0),
                array('id'=>2, 'foo'=>'bar2', 'deleted' => 0)
        )));
        $model->deleteRows(1);
        $s = new Kwf_Model_Select(array('ignoreDeleted'=>true));
        $s->whereEquals('deleted', 1);
        $this->assertEquals(1, count($model->getRows($s)));
    }
    public function testCountRow()
    {
        $model = new Kwf_Model_FnF_DeletedFlag_Model(array(
            'columns' => array('id', 'foo', 'deleted'),
            'data'=>array(
                array('id'=>1, 'foo'=>'bar', 'deleted' => 0),
                array('id'=>2, 'foo'=>'bar2', 'deleted' => 0)
        )));
        $model->getRow(1)->delete();
        $this->assertEquals(1, $model->countRows());
        $s = array('ignoreDeleted'=>true);
        $this->assertEquals(2, $model->countRows($s));
    }
    public function testGetRows()
    {
        $model = new Kwf_Model_FnF_DeletedFlag_Model(array(
            'columns' => array('id', 'foo', 'deleted'),
            'data'=>array(
                array('id'=>1, 'foo'=>'bar', 'deleted' => 0),
                array('id'=>2, 'foo'=>'bar2', 'deleted' => 0)
        )));
        $model->getRow(1)->delete();
        $this->assertEquals(1, count($model->getRows()));
        $s = new Kwf_Model_Select(array('ignoreDeleted'=>true));
        $this->assertEquals(2, count($model->getRows($s)));
    }
    public function testGetRow()
    {
        $model = new Kwf_Model_FnF_DeletedFlag_Model(array(
            'columns' => array('id', 'foo', 'deleted'),
            'data'=>array(
                array('id'=>1, 'foo'=>'bar', 'deleted' => 0),
                array('id'=>2, 'foo'=>'bar2', 'deleted' => 0)
        )));
        $model->getRow(1)->delete();
        $this->assertEquals(null, $model->getRow(1));
        $s = new Kwf_Model_Select(array('ignoreDeleted'=>true));
        $s->whereId(1);
        $this->assertEquals(1, count($model->getRow($s)));
    }
    public function testGetIds()
    {
        $model = new Kwf_Model_FnF_DeletedFlag_Model(array(
            'columns' => array('id', 'foo', 'deleted'),
            'data'=>array(
                array('id'=>1, 'foo'=>'bar', 'deleted' => 0),
                array('id'=>2, 'foo'=>'bar2', 'deleted' => 0)
        )));
        $model->getRow(1)->delete();
        $this->assertEquals(1, count($model->getIds()));
        $s = array('ignoreDeleted'=>true);
        $this->assertEquals(2, count($model->getIds($s)));
    }
    public function testDeleteRows()
    {
        $model = new Kwf_Model_FnF_DeletedFlag_Model(array(
            'columns' => array('id', 'foo', 'deleted'),
            'data'=>array(
                array('id'=>1, 'foo'=>'bar', 'deleted' => 0),
                array('id'=>2, 'foo'=>'bar2', 'deleted' => 0)
        )));
        $model->getRow(1)->delete();
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', 1);
        $model->deleteRows($s);
        $this->assertEquals(1, $model->countRows());
    }
    public function testUpdateRows()
    {
        $model = new Kwf_Model_FnF_DeletedFlag_Model(array(
            'columns' => array('id', 'foo', 'deleted'),
            'data'=>array(
                array('id'=>1, 'foo'=>'bar', 'deleted' => 0),
                array('id'=>2, 'foo'=>'bar2', 'deleted' => 0)
        )));
        $model->getRow(1)->delete();
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equals('id', 1),
            new Kwf_Model_Select_Expr_Equals('id', 2),
        )));
        $model->updateRows(array('foo' => 'newBar'), $s);
        $this->assertEquals(null, $model->getRow(1));
        $s = new Kwf_Model_Select(array('ignoreDeleted'=>true));
        $s->where(new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equals('id', 1),
            new Kwf_Model_Select_Expr_Equals('id', 2),
        )));
        $model->updateRows(array('foo' => 'newBar'), $s);
        $this->assertEquals('newBar', $model->getRow(2)->foo);
        $row = $model->getRow($s);
    }
    public function testExport()
    {
        $model = new Kwf_Model_FnF_DeletedFlag_Model(array(
            'columns' => array('id', 'foo', 'deleted'),
            'data'=>array(
                array('id'=>1, 'foo'=>'bar', 'deleted' => 0),
                array('id'=>2, 'foo'=>'bar2', 'deleted' => 0)
        )));
        $model->getRow(1)->delete();
        //export
        $s = new Kwf_Model_Select();
        $export = $model->export('array', $s);
        $this->assertEquals(1, count($export));

        $s = array('ignoreDeleted' => true);
        $export = $model->export('array', $s);
        $this->assertEquals(2, count($export));
    }
}
