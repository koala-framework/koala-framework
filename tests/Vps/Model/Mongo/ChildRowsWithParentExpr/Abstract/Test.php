<?php
abstract class Vps_Model_Mongo_ChildRowsWithParentExpr_Abstract_Test extends Vps_Test_TestCase
{
    protected $_modelClass;
    protected $_parentModelClass;

    private $_model;
    public function setUp()
    {
        $this->markTestIncomplete(); // TODO: deaktiviert, waren Fehler drinnen
        parent::setUp();

        $this->_model = Vps_Model_Abstract::getInstance($this->_modelClass);
        $m = $this->_model;
        while($m instanceof Vps_Model_Proxy) $m = $m->getProxyModel();
        $m->getCollection()->insert(
            array('id'=>1, 'name'=>'a', 'foo'=>array(array('x'=>1, 'parent_id'=>1, 'parent_name' => 'one'), array('x'=>2))) //TODO id sollte nicht nötig sein
        , array('safe'=>true));
    }

    public function testParentRowFromSubModel()
    {
        $row = $this->_model->getRow(1);
        $rows = $row->getChildRows('Foo');
        $this->assertEquals(2, count($rows));
        $this->assertEquals(1, $rows->current()->x);
        $this->assertSame($rows->current()->getParentRow('Mongo'), $row);
    }

    public function testParentMongoExpr()
    {
        $row = $this->_model->getRow(1);
        $rows = $row->getChildRows('Foo');
        $cRow = $rows->current();
        $this->assertEquals('a', $cRow->mongo_name);

        $row->name = 'b';
        $row->save();

        $this->_model->clearRows();

        $row = $this->_model->getRow(1);
        $rows = $row->getChildRows('Foo');
        $this->assertSame($rows->current()->getParentRow('Mongo'), $row);
        $this->assertEquals('b', $rows->current()->getParentRow('Mongo')->name);
        $this->assertEquals('b', $rows->current()->mongo_name);
    }

    public function testParentExpr()
    {
        $row = $this->_model->getRow(1);
        $rows = $row->getChildRows('Foo');
        $this->assertEquals('one', $rows->current()->parent_name);
        $this->assertEquals('one', $rows->current()->getParentRow('Parent')->name);

        $rows->current()->getParentRow('Parent')->name = 'onex';
        $rows->current()->getParentRow('Parent')->save();
    }

    public function testParentExprParentChanged()
    {
        $pRow = Vps_Model_Abstract::getInstance($this->_parentModelClass)->getRow(1);
        $pRow->name = 'onex';
        $pRow->save();

        $row = $this->_model->getRow(1);
        $rows = $row->getChildRows('Foo');
        $this->assertEquals('onex', $rows->current()->parent_name);
    }
}
