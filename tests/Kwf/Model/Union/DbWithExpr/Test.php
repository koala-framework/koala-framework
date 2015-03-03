<?php
/**
 * @group slow
 */
class Kwf_Model_Union_DbWithExpr_Test extends Kwf_Model_Union_Abstract_Test
{
    public function setUp()
    {
        parent::setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DbWithExpr_Model1')->setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DbWithExpr_Model2')->setUp();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DbWithExpr_ModelSibling')->setUp();
        $this->_m = Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DbWithExpr_TestModel');
    }

    public function tearDown()
    {
        parent::tearDown();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DbWithExpr_Model1')->dropTable();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DbWithExpr_Model2')->dropTable();
        Kwf_Model_Abstract::getInstance('Kwf_Model_Union_DbWithExpr_ModelSibling')->dropTable();
    }

    public function testGetRowsWithExprOrder()
    {
        $s = new Kwf_Model_Select();
        $s->order('baz');
        $rows = $this->_m->getRows($s);
        $this->assertEquals(6, count($rows));
        $this->assertEquals('2', $rows[0]->baz);
        $this->assertEquals('cc', $rows[1]->baz);
        $this->assertEquals('cc3', $rows[2]->baz);
        $this->assertEquals('foobar', $rows[3]->baz);
        $this->assertEquals('foobar', $rows[4]->baz);
        $this->assertEquals('foobar', $rows[5]->baz);
    }

    public function testToArray()
    {
        $row = $this->_m->getRow('1m2');
        $a = $row->toArray();
        $this->assertEquals(array('id' => '1m2', 'foo' => '2', 'bar' => '2', 'baz' => '2', 'sib' => 'ss2'), $a);

        $row = $this->_m->getRow('2m2');
        $a = $row->toArray();
        $this->assertEquals(array('id' => '2m2', 'foo' => '333', 'bar' => 'yy1', 'baz' => 'foobar', 'sib' => 'sss3'), $a);
    }
}

