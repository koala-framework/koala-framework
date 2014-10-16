<?php
class Kwf_Model_DbWithConnection_ExprInteger_Test extends Kwf_Test_TestCase
{
    public function testExprLazyLoad()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprInteger_Model');
        $m->setUp();

        $this->assertEquals(100, $m->getRow(1)->price);
        $this->assertEquals(3, $m->getRow(1)->amount);
        $this->assertEquals(300, $m->getRow(1)->total);

        $this->assertTrue($m->getRow(1)->price === 100);
        $this->assertTrue($m->getRow(1)->amount === 3);
        $m->dropTable();
    }

    public function testExprPreload()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprInteger_Model');
        $m->setUp();

        $s = $m->select();
        $s->expr('price');
        $s->expr('amount');
        $s->expr('total');

        $s->whereId(1);
        $this->assertEquals(100, $m->getRow($s)->price);
        $this->assertEquals(3, $m->getRow($s)->amount);
        $this->assertEquals(300, $m->getRow($s)->total);

        $this->assertTrue($m->getRow($s)->price === 100);
        $this->assertTrue($m->getRow($s)->amount === 3);

        $m->dropTable();
    }
}

