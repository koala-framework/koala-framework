<?php
class Kwf_Model_DbWithConnection_ExprSql_Test extends Kwf_Test_TestCase
{
    public function testExpr()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprSql_Model1');
        $m->setUp();

        $s = $m->select();
        $s->expr('age');
        $s->whereId(1);
        $this->assertEquals(36, $m->getRow($s)->age2);
        $s->whereId(2);
        $this->assertEquals(44, $m->getRow($s)->age2);
        $s->whereId(3);
        $this->assertEquals(20, $m->getRow($s)->age2);
        $m->dropTable();
    }
}
