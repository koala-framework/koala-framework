<?php
class Kwf_Model_DbWithConnection_MultipleReferences_Test extends Kwf_Test_TestCase
{
    private $_modelFoo;
    private $_modelFooToFoo;
    public function setUp()
    {
        $this->_modelFoo = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_MultipleReferences_FooModel');
        $this->_modelFoo->setUp();
        $this->_modelFooToFoo = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_MultipleReferences_ToFooModel');
        $this->_modelFooToFoo->setUp();
    }

    public function tearDown()
    {
        $this->_modelFoo->dropTable();
        $this->_modelFooToFoo->dropTable();
    }

    public function testReferences()
    {
        $select = new Kwf_Model_Select();
        $foo = $this->_modelFoo->getRow(1);
        $fooToFoo = $foo->getChildRows('Reference1', array('id' => 1))->current();
        $this->assertEquals('5', $fooToFoo->bar);
        $foo1 = $fooToFoo->getParentRow('Foo1');
        $this->assertEquals('5', $foo1->foo);
        $foo2 = $fooToFoo->getParentRow('Foo2');
        $this->assertEquals('7', $foo2->foo);
    }
    public function testChildContains()
    {
        $select = new Kwf_Model_Select();
        $childSelect = new Kwf_Model_Select();
        $childSelect->whereEquals('bar', 5);
        $s = new Kwf_Model_Select();
        $s->where(new Kwf_Model_Select_Expr_Child_Contains('Reference1', $childSelect));
        $foo = $this->_modelFoo->getRow($s);
        $this->assertEquals('5', $foo->foo);
    }

    public function testChildCountExpressionLazy()
    {
        $this->assertEquals(2, $this->_modelFoo->getRow(1)->child_count);
    }

    public function testChildCountExpression()
    {
        $this->markTestIncomplete();
        $s = new Kwf_Model_Select();
        $s->expr('child_count');
        $s->whereId(1);
        $row = $this->_modelFoo->getRow($s);
        $this->assertEquals(2, $row->child_count);
    }

}
