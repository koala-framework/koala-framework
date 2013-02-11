<?php
class Kwf_Model_DbWithConnection_ParentExpr_Test extends Kwf_Test_TestCase
{
    private $_modelParent;
    private $_modelChild;
    public function setUp()
    {
        $this->_modelParent = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ParentExpr_ParentModel');
        $this->_modelParent->setUp();
        $this->_modelChild = Kwf_Model_Abstract::
            getInstance('Kwf_Model_DbWithConnection_ParentExpr_ChildModel');
        $this->_modelChild->setUp();
    }

    public function tearDown()
    {
        $this->_modelParent->dropTable();
        $this->_modelChild->dropTable();
    }

    public function testParentExprHigher()
    {
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Parent('Parent',
                        new Kwf_Model_Select_Expr_Higher('foo', 5)));
        $count = $this->_modelChild->countRows($select);
        $this->assertEquals(1, $count);
    }

    public function testParentChildExprHigher()
    {
        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Higher('bar', 2));
        $selectParent = new Kwf_Model_Select();
        $selectParent->where(new Kwf_Model_Select_Expr_Parent('Parent',
                        new Kwf_Model_Select_Expr_Child_Contains('Childs', $select)));

        $rows = $this->_modelChild->getRows($selectParent);
        foreach ($rows as $row) {
            $this->assertGreaterThan(1, $row->bar);
        }
    }

    public function testParentOrExpr()
    {
        $select = new Kwf_Model_Select();
        $parentExpr = new Kwf_Model_Select_Expr_Parent('Parent',
                        new Kwf_Model_Select_Expr_Or(array (
                            new Kwf_Model_Select_Expr_Higher('foo', 5),
                            new Kwf_Model_Select_Expr_Lower('foo', 2)
                    )));
        $select->where($parentExpr);
        $rows = $this->_modelChild->getRows($select);
        foreach ($rows as $row) {
            $parent = $row->getParentRow('Parent');
            $this->assertTrue($parent->foo > 5 || $parent->foo < 2);
        }

        $rows = $this->_modelChild->getRows();
        foreach ($rows as $row) {
            $parent = $row->getParentRow('Parent');
            $value = $parent->foo > 5 || $parent->foo < 2;
            $exprValue = $this->_modelChild->getExprValue($row, $parentExpr);
            $this->assertTrue($value == $exprValue);
        }
    }
}
