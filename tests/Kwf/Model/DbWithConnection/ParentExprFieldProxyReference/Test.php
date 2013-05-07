<?php
class Kwf_Model_DbWithConnection_ParentExprFieldProxyReference_Test extends Kwf_Test_TestCase
{
    private $_modelParent;
    private $_modelChild;
    private $_siblingModels;
    public function setUp()
    {
        $this->_modelParent = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ParentExprFieldProxyReference_ParentModel');
        $this->_modelParent->setUp();
        $this->_modelChild = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ParentExprFieldProxyReference_ProxyModel');
        $this->_modelChild->getProxyModel()->setUp();
        $this->_siblingModels = $this->_modelChild->getSiblingModels();
        $this->_siblingModels['sibling']->setUp();
    }

    public function tearDown()
    {
        $this->_modelParent->dropTable();
        $this->_modelChild->getProxyModel()->dropTable();
        $this->_siblingModels['sibling']->dropTable();
    }

    public function testWithExpr()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', 1);
        $s->expr('foo');
        $this->markTestIncomplete();
        $row = $this->_modelChild->getRow($s);
        $this->assertEquals($row->foo, 777);
    }

    public function testLazyLoaded()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', 1);
        $row = $this->_modelChild->getRow($s);
        $this->assertEquals($row->foo, 777);
    }

    public function testOrder()
    {
        $s = new Kwf_Model_Select();
        $s->whereEquals('id', 1);
        $s->order('foo');
        $this->markTestIncomplete();
        $row = $this->_modelChild->getRow($s);
        $this->assertEquals($row->foo, 555);
    }
}
