<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Expr_Date
 */
class Vps_Model_DbWithConnection_ExprDate_Test extends Vps_Model_DbWithConnection_SelectExpr_AbstractTest
{
    public function testExpr()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_ExprDate_Model');
        $m->setUp();

        $this->assertEquals(1983, $m->getRow(1)->date_year);
        $this->assertEquals(2003, $m->getRow(2)->date_year);
        $m->dropTable();
    }

    public function testExprEfficient()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_ExprDate_Model');
        $m->setUp();

        $s = $m->select();
        $s->expr('date_year');
        $s->order('id');
        $rows = $m->getRows($s)->toArray();

        $this->assertEquals(1983, $rows[0]['date_year']);
        $this->assertEquals(2003, $rows[1]['date_year']);

        $m->dropTable();
    }
}
