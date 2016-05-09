<?php
class Kwf_Model_DbWithConnection_ExprGroupConcat_Test extends Kwf_Test_TestCase
{

    public function setUp()
    {
        parent::setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprGroupConcat_Model')->setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprGroupConcat_ChildModel')->setUp();
    }

    public function tearDown()
    {
        parent::tearDown();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprGroupConcat_Model')->dropTable();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprGroupConcat_ChildModel')->dropTable();
    }

    public function testNoExpr()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprGroupConcat_Model');

        $row = $m->getRow(1);
        $this->assertEquals($row->foo1, '1,2');
        $this->assertEquals($row->foo2, '1, 2');

        $row = $m->getRow(2);
        $this->assertEquals($row->foo1, '3');
        $this->assertEquals($row->foo2, '3');

        $row = $m->getRow(3);
        $this->assertEquals($row->foo1, '');
        $this->assertEquals($row->foo2, '');
    }

    public function testExpr()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprGroupConcat_Model');
        $s = $m->select();
        $s->expr('foo1');
        $s->expr('foo2');

        $s->whereId(1);
        $row = $m->getRow($s);
        $this->assertEquals($row->foo1, '1,2');
        $this->assertEquals($row->foo2, '1, 2');

        $s->whereId(2);
        $row = $m->getRow($s);
        $this->assertEquals($row->foo1, '3');
        $this->assertEquals($row->foo2, '3');

        $s->whereId(3);
        $row = $m->getRow($s);
        $this->assertEquals($row->foo1, '');
        $this->assertEquals($row->foo2, '');
    }

    public function testSortExpr()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprGroupConcat_Model');
        $s = $m->select();
        $s->expr('foo3');

        $s->whereId(1);
        $row = $m->getRow($s);
        $this->assertEquals($row->foo3, '2, 1');
    }

    public function testSortArraySyntax()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_ExprGroupConcat_Model');
        $s = $m->select();
        $s->expr('foo4');

        $s->whereId(1);
        $row = $m->getRow($s);
        $this->assertEquals($row->foo4, '1, 2');
    }
}
