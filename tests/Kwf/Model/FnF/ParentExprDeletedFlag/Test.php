<?php
class Kwf_Model_FnF_ParentExprDeletedFlag_Test extends Kwf_Test_TestCase
{
    public function testParentExpr()
    {
        $modelChild = Kwf_Model_Abstract::getInstance('Kwf_Model_FnF_ParentExprDeletedFlag_ChildModel');

        $s = new Kwf_Model_Select();
        $s->whereId(1);
        $row = $modelChild->getRow($s);
        $this->assertEquals($row->parent_foo, 5);

        $s2 = clone $s;
        $s2->expr('parent_foo');
        $row = $modelChild->getRow($s2);
        $this->assertEquals($row->parent_foo, 5);
    }
}
