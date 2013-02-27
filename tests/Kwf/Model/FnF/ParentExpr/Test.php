<?php
class Kwf_Model_FnF_ParentExpr_Test extends Kwf_Test_TestCase
{
    public function testParentExprHigher()
    {
        $modelChild = Kwf_Model_Abstract::getInstance('Kwf_Model_FnF_ParentExpr_ChildModel');

        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Parent('Parent',
                        new Kwf_Model_Select_Expr_Higher('foo', 5)));
        $count = $modelChild->countRows($select);
        $this->assertEquals(1, $count);
    }

    public function testParentChildExprHigher()
    {
        $modelChild = Kwf_Model_Abstract::getInstance('Kwf_Model_FnF_ParentExpr_ChildModel');

        $select = new Kwf_Model_Select();
        $select->where(new Kwf_Model_Select_Expr_Higher('bar', 2));
        $selectParent = new Kwf_Model_Select();
        $selectParent->where(new Kwf_Model_Select_Expr_Parent('Parent',
                        new Kwf_Model_Select_Expr_Child_Contains('Childs', $select)));

        $rows = $modelChild->getRows($selectParent);
        $this->assertEquals(2, count($rows));
        foreach ($rows as $row) {
            $this->assertEquals(1, $row->getParentRow('Parent')->id);
        }
    }

    public function testParentOrExpr()
    {
        $modelChild = Kwf_Model_Abstract::getInstance('Kwf_Model_FnF_ParentExpr_ChildModel');

        $select = new Kwf_Model_Select();
        $parentExpr = new Kwf_Model_Select_Expr_Parent('Parent',
                        new Kwf_Model_Select_Expr_Or(array (
                            new Kwf_Model_Select_Expr_Higher('foo', 5),
                            new Kwf_Model_Select_Expr_Lower('foo', 2)
                    )));
        $select->where($parentExpr);
        $rows = $modelChild->getRows($select);
        foreach ($rows as $row) {
            $parent = $row->getParentRow('Parent');
            $this->assertTrue($parent->foo > 5 || $parent->foo < 2);
        }

        $rows = $modelChild->getRows();
        foreach ($rows as $row) {
            $parent = $row->getParentRow('Parent');
            $value = $parent->foo > 5 || $parent->foo < 2;
            $exprValue = $modelChild->getExprValue($row, $parentExpr);
            $this->assertTrue($value == $exprValue);
        }
    }
}
