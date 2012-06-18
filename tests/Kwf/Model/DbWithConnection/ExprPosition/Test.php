<?php
class Kwf_Model_DbWithConnection_ExprPosition_Test extends Kwf_Test_TestCase
{
    public function testExprLazyLoad()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprPosition_Model');
        $m->setUp();

        $this->assertEquals(2, $m->getRow(1)->position);
        $this->assertEquals(3, $m->getRow(2)->position);
        $this->assertEquals(1, $m->getRow(3)->position);
        $this->assertEquals(2, $m->getRow(4)->position);
        $this->assertEquals(1, $m->getRow(5)->position);
        $m->dropTable();
    }

    public function testExprPreload()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprPosition_Model');
        $m->setUp();

        $s = $m->select();
        $s->expr('position');

        $s->whereId(1);
        $this->assertEquals(2, $m->getRow($s)->position);

        $s->whereId(2);
        $this->assertEquals(3, $m->getRow($s)->position);

        $s->whereId(3);
        $this->assertEquals(1, $m->getRow($s)->position);

        $s->whereId(4);
        $this->assertEquals(2, $m->getRow($s)->position);

        $s->whereId(5);
        $this->assertEquals(1, $m->getRow($s)->position);

        $m->dropTable();
    }
}
