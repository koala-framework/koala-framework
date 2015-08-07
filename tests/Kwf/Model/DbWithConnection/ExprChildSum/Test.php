<?php
class Kwf_Model_DbWithConnection_ExprChildSum_Test extends Kwf_Test_TestCase
{
    private $_modelFoo;
    private $_modelFooToBar;
    private $_modelBar;

    public function setUp()
    {
        $this->_modelFoo = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ExprChildSum_FooModel');
        $this->_modelFoo->setUp();
        $this->_modelBar = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ExprChildSum_BarModel');
        $this->_modelBar->setUp();
        $this->_modelFooToBar = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ExprChildSum_FooToBarModel');
        $this->_modelFooToBar->setUp();
    }

    public function tearDown()
    {
        if ($this->_modelFoo) {
            $this->_modelFoo->dropTable();
        }
        if ($this->_modelBar) {
            $this->_modelBar->dropTable();
        }
        if ($this->_modelFooToBar) {
            $this->_modelFooToBar->dropTable();
        }
        Kwf_Model_Abstract::clearAllRows();
    }

    public function testChildSumWithExpression()
    {
        $select = new Kwf_Model_Select();
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', 1);
        $s->expr('foo_value_sum');
        $row = $this->_modelBar->getRow($s);
        $sum = $row->foo_value_sum;
        $this->assertEquals(6, $sum);
    }

    public function testChildSumWithExpressionLazy()
    {
        $select = new Kwf_Model_Select();
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', 1);
        $row = $this->_modelBar->getRow($s);
        $sum = $row->foo_value_sum;
        $this->assertEquals(6, $sum);
    }

    public function testChildSumWithExpressionOrder()
    {
        $select = new Kwf_Model_Select();
        $s = new Kwf_Model_Select();
        $s->order('foo_value_sum', 'DESC');
        $s->expr('foo_value_sum');
        $row = $this->_modelBar->getRow($s);
        $sum = $row->foo_value_sum;
        $this->assertEquals(6, $sum);
    }

    public function testChildSumWithExpressionOrderLazy()
    {
        $select = new Kwf_Model_Select();
        $s = new Kwf_Model_Select();
        $s->order('foo_value_sum', 'DESC');
        $row = $this->_modelBar->getRow($s);
        $sum = $row->foo_value_sum;
        $this->assertEquals(6, $sum);
    }

    public function testOrExpression()
    {
        $select = new Kwf_Model_Select();
        $select->whereEquals('or_expr', 0);
        $this->assertEquals(1, $this->_modelBar->countRows($select));
    }
}
