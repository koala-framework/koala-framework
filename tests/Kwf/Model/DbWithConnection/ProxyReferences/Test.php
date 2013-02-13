<?php
class Kwf_Model_DbWithConnection_ProxyReferences_Test extends Kwf_Test_TestCase
{
    private $_modelParent;
    private $_modelParent2;
    private $_modelChild;
    public function setUp()
    {
        $this->_modelParent = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ProxyReferences_ParentModel');
        $this->_modelParent->setUp();

        $this->_modelParent2 = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ProxyReferences_Parent2Model');
        $this->_modelParent2->setUp();

        $this->_modelChild = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ProxyReferences_ChildModel');
        $this->_modelChild->setUp();
    }

    public function tearDown()
    {
        $this->_modelParent->dropTable();
        $this->_modelParent2->dropTable();
        $this->_modelChild->dropTable();
    }

    public function testChildContainsReference()
    {
        $childSelect = new Kwf_Model_Select();
        $childSelect->where(new Kwf_Model_Select_Expr_Parent('Parent2',
                            new Kwf_Model_Select_Expr_Equal('foo2', 3)));
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Parent('Parent',
                            new Kwf_Model_Select_Expr_Child_Contains('Childs'), $childSelect));
        $rows = $this->_modelChild->getRows($select);
        $this->markTestIncomplete();
        $this->assertEquals(count($rows), 3);
        foreach ($rows as $row) {
            $this->assertEquals($row->getParentRow('Parent2')->foo2, 3);
        }
    }

    public function testChildReference()
    {
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Parent('Parent',
                            new Kwf_Model_Select_Expr_Equal('foo', 5)));
        $rows = $this->_modelChild->getRows($select);
        $this->assertEquals(count($rows), 3);
        foreach ($rows as $row) {
            $this->assertEquals($row->getParentRow('Parent')->foo, 5);
        }
    }
}
