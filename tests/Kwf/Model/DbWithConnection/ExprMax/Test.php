<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Expr_SumFields
 */
class Kwf_Model_DbWithConnection_ExprMax_Test extends Kwf_Model_DbWithConnection_SelectExpr_AbstractTest
{
    public function testExprMax()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprMax_Model');
        $mChild = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprMax_ChildModel');
        $m->setUp();
        $mChild->setUp();

        $this->assertEquals(400, $m->getRow(1)->max);
//         $this->assertEquals(500, $m->getRow(1)->max_child);
        $this->assertEquals(3, $m->getRow(1)->max_child_count);

        $mChild->dropTable();
        $m->dropTable();
    }

    public function testExprMaxEfficient()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprMax_Model');
        $mChild = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprMax_ChildModel');
        $m->setUp();
        $mChild->setUp();

        $s = $m->select();
        $s->expr('max');
//         $s->expr('max_child');
//         $s->expr('max_child_count');
        $s->order('id');
        $rows = $m->getRows($s)->toArray();

        $this->assertEquals(400, $rows[0]['max']);
//         $this->assertEquals(500, $rows[0]['max_child']);
//         $this->assertEquals(3, $rows[0]['max_child_count']);

        $this->assertEquals(400, $rows[1]['max']);
//         $this->assertEquals(100, $rows[1]['max_child_count']);
//         $this->assertEquals(3, $rows[1]['max_child_count']);

        $mChild->dropTable();
        $m->dropTable();
    }

    public function testExprSumFieldsWhereEquals()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprSumFields_Model');
        $m->setUp();

        $s = $m->select();
        $s->whereEquals('sum_field_int', 410);
        $s->order('id');
        $this->assertEquals(2, $m->getRow($s)->id);

        $m->dropTable();
    }
}
