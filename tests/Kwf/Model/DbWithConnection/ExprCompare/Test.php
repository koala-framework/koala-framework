<?php
class Kwf_Model_DbWithConnection_ExprCompare_Test extends Kwf_Test_TestCase
{
    private $_modelParent;
    private $_modelChild;
    public function setUp()
    {
        $this->_modelParent = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ExprCompare_ParentModel');
        $this->_modelParent->setUp();
        $this->_modelChild = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ExprCompare_ChildModel');
        $this->_modelChild->setUp();
    }

    public function tearDown()
    {
        $this->_modelParent->dropTable();
        $this->_modelChild->dropTable();
    }

    public function testChildCountWithExprCompare()
    {
        $select = new Kwf_Model_Select();
        $select->whereEquals('expr_bar_compare', true);
        $row = $this->_modelParent->getRow(1);
        $count = $row->countChildRows('Childs', $select);
        $this->assertEquals(2, $count);
    }

    public function testStoredExprCompareInSelect()
    {
        $select = new Kwf_Model_Select();
        $select->whereEquals('expr_bar_compare', true);
        $rows = $this->_modelChild->getRows($select);
        $this->assertEquals(3, count($rows));
    }

    public function testStoredExprCompareInRow()
    {
        $rows = $this->_modelChild->getRows();
        foreach ($rows as $row) {
            $this->assertTrue($row->expr_bar_compare);
        }
    }

    public function testExprCompareHigherInSelect()
    {
        $select = new Kwf_Model_Select();
        $select->whereEquals('expr_foo_bar_higher', true);
        $count = $this->_modelChild->countRows($select);
        $this->assertEquals(2, $count);
    }

    public function testExprCompareHigherInRow()
    {
        $rows = $this->_modelChild->getRows();
        foreach ($rows as $row) {
            $this->assertEquals($row->bar >= $row->foo, $row->expr_foo_bar_higher);
        }
    }
}
