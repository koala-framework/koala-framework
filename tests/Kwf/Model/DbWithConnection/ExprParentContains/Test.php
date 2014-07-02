<?php
class Kwf_Model_DbWithConnection_ExprParentContains_Test extends Kwf_Test_TestCase
{
    public function setUp()
    {
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprParentContains_ParentModel')->setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprParentContains_Model')->setUp();
    }

    public function tearDown()
    {
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprParentContains_ParentModel')->dropTable();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprParentContains_Model')->dropTable();
    }

    public function testIt()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprParentContains_Model');
        $ps = $m->select();
        $ps->whereEquals('foo', 5);
        $s = $m->select();
        $s->where(
            new Kwf_Model_Select_Expr_Parent_Contains('Parent', $ps)
        );
        $rows = $m->getRows($s);
        $this->assertEquals(2, count($rows));
        $this->assertEquals(1, $rows[0]->id);
        $this->assertEquals(3, $rows[1]->id);
    }
}
