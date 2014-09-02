<?php
class Kwf_Model_DbWithConnection_ExprArea_Test extends Kwf_Test_TestCase
{
    public function testExprPreload()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprArea_Model');
        $m->setUp();
        $s = $m->select();
        $s->expr('inrange');
        for($i = 1; $i < 10; $i++) {
            $s->whereId($i);
            $this->assertEquals(true, $m->getRow($s)->inrange);
        }

        for($i = 10; $i < 15; $i++) {
            $s->whereId($i);
            $this->assertEquals(false, $m->getRow($s)->inrange);
        }
        $m->dropTable();
    }
}
