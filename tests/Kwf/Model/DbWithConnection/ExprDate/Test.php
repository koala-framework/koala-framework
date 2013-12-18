<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Expr_Date
 */
class Kwf_Model_DbWithConnection_ExprDate_Test extends Kwf_Model_DbWithConnection_SelectExpr_AbstractTest
{
    public function testExpr()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprDate_Model');
        $m->setUp();

        $this->assertEquals(1983, $m->getRow(1)->date_year);
        $this->assertEquals(2003, $m->getRow(2)->date_year);
        $m->dropTable();
    }

    public function testExprEfficient()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprDate_Model');
        $m->setUp();

        $s = $m->select();
        $s->expr('date_year');
        $s->order('id');
        $rows = $m->getRows($s)->toArray();

        $this->assertEquals(1983, $rows[0]['date_year']);
        $this->assertEquals(2003, $rows[1]['date_year']);

        $m->dropTable();
    }

    public function testTwoDigits()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprDate_Model');
        $m->setUp();

        $this->assertEquals(83, $m->getRow(1)->date_year_two_digits);
        $this->assertEquals(03, $m->getRow(2)->date_year_two_digits);
        $m->dropTable();
    }

    public function testFormat()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprDate_Model');
        $m->setUp();

        $this->assertEquals('1983-06-09', $m->getRow(1)->date_format);
        $this->assertEquals('2003-06-20', $m->getRow(2)->date_format);
        $this->assertEquals('09.06.1983', $m->getRow(1)->date_format2);
        $this->assertEquals('20.06.2003', $m->getRow(2)->date_format2);
        $m->dropTable();
    }

    public function testFormatEfficient()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprDate_Model');
        $m->setUp();

        $s = $m->select();
        $s->expr('date_format');
        $s->expr('date_format2');
        $s->order('id');
        $rows = $m->getRows($s)->toArray();

        $this->assertEquals('1983-06-09', $rows[0]['date_format']);
        $this->assertEquals('2003-06-20', $rows[1]['date_format']);
        $this->assertEquals('09.06.1983', $rows[0]['date_format2']);
        $this->assertEquals('20.06.2003', $rows[1]['date_format2']);

        $m->dropTable();
    }
}
