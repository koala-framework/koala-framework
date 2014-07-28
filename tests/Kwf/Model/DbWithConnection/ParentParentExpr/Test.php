<?php
class Kwf_Model_DbWithConnection_ParentParentExpr_Test extends Kwf_Test_TestCase
{
    private $_modelParent;
    private $_modelChild;
    private $_modelMiddle;
    public function setUp()
    {
        $this->_modelParent = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ParentParentExpr_ParentModel');
        $this->_modelParent->setUp();
        $this->_modelChild = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ParentParentExpr_ChildModel');
        $this->_modelChild->setUp();
        $this->_modelMiddle = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ParentParentExpr_MiddleModel');
        $this->_modelMiddle->setUp();
    }

    public function tearDown()
    {
        if ($this->_modelParent) $this->_modelParent->dropTable();
        if ($this->_modelChild) $this->_modelChild->dropTable();
        if ($this->_modelMiddle) $this->_modelMiddle->dropTable();
    }

    public function testWhere()
    {
        $select = new Kwf_Model_Select();
        $select->whereEquals('parent_foo', 5);
        $count = $this->_modelChild->countRows($select);
        $this->assertEquals(1, $count);
    }

    public function testLazy()
    {
        $this->assertEquals(5, $this->_modelChild->getRow(1)->parent_foo);
    }

    public function testEager()
    {
        $s = new Kwf_Model_Select();
        $s->whereId(1);
        $s->expr('parent_foo');
        $this->assertEquals(5, $this->_modelChild->getRow($s)->parent_foo);
    }
}
