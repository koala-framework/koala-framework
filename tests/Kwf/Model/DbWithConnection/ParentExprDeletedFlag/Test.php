<?php
class Kwf_Model_DbWithConnection_ParentExprDeletedFlag_Test extends Kwf_Test_TestCase
{
    private $_modelParent;
    private $_modelChild;
    public function setUp()
    {
        $this->_modelParent = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ParentExprDeletedFlag_ParentModel');
        $this->_modelParent->setUp();
        $this->_modelChild = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ParentExprDeletedFlag_ChildModel');
        $this->_modelChild->setUp();
    }

    public function tearDown()
    {
        if ($this->_modelParent) $this->_modelParent->dropTable();
        if ($this->_modelChild) $this->_modelChild->dropTable();
    }

    public function testParentExpr()
    {
        $s = new Kwf_Model_Select();
        $s->whereId(1);
        $row = $this->_modelChild->getRow($s);
        $this->assertEquals($row->parent_foo, 5);

        $s2 = clone $s;
        $s2->expr('parent_foo');
        $row = $this->_modelChild->getRow($s2);
        $this->assertEquals($row->parent_foo, 5);
    }
}
