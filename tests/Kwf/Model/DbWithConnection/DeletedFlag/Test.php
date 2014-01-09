<?php
class Kwf_Model_DbWithConnection_DeletedFlag_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        $this->_m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_DeletedFlag_Model');
        $this->_m->setUp();
    }
    public function tearDown()
    {
        $this->_m->dropTable();
    }

    public function testDeleteRow()
    {
        $this->_m->getRow(1)->delete();
        $s = new Kwf_Model_Select(array('ignoreDeleted'=>true));
        $s->whereEquals('deleted', 1);
        $this->assertEquals(1, $this->_m->countRows($s));
    }
    public function testCountRow()
    {
        $model = $this->_m;
        $model->getRow(1)->delete();
        $this->assertEquals(1, $model->countRows());
        $s = array('ignoreDeleted'=>true);
        $this->assertEquals(2, $model->countRows($s));
    }
    public function testGetRows()
    {
        $model = $this->_m;
        $model->getRow(1)->delete();
        $this->assertEquals(1, count($model->getRows()));
        $s = new Kwf_Model_Select(array('ignoreDeleted'=>true));
        $this->assertEquals(2, count($model->getRows($s)));
    }
    public function testGetRow()
    {
        $model = $this->_m;
        $model->getRow(1)->delete();
        $this->assertEquals(null, $model->getRow(1));
        $s = new Kwf_Model_Select(array('ignoreDeleted'=>true));
        $s->whereId(1);
        $this->assertEquals(1, count($model->getRow($s)));
    }
    public function testGetIds()
    {
        $model = $this->_m;
        $model->getRow(1)->delete();
        $this->assertEquals(1, count($model->getIds()));
        $s = array('ignoreDeleted'=>true);
        $this->assertEquals(2, count($model->getIds($s)));
    }
    public function testDeleteRows()
    {
        $model = $this->_m;
        $model->getRow(1)->delete();
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', 1);
        $model->deleteRows($s);
        $this->assertEquals(1, $model->countRows());
    }
    public function testUpdateRows()
    {
        $model = $this->_m;
        $model->getRow(1)->delete();
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equals('id', 1),
            new Kwf_Model_Select_Expr_Equals('id', 2),
        )));
        $model->updateRows(array('foo' => 'newBar'), $s);
        $this->assertEquals(null, $model->getRow(1));
    }
    public function testUpdateRowsIgnoreDeleted()
    {
        $model = $this->_m;
        $model->getRow(1)->delete();
        $s = new Kwf_Model_Select(array('ignoreDeleted'=>true));
        $s->where(new Kwf_Model_Select_Expr_Or(array(
            new Kwf_Model_Select_Expr_Equals('id', 1),
            new Kwf_Model_Select_Expr_Equals('id', 2),
        )));
        $model->updateRows(array('foo' => 'newBar'), $s);
        $this->assertEquals('newBar', $model->getRow(2)->foo);
    }
    public function testExport()
    {
        $model = $this->_m;
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
