<?php
/**
 * @group Kwf_Model_Db_SiblingRelationExpr
 */
class Kwf_Model_DbWithConnection_SiblingRelationExpr_Test extends Kwf_Test_TestCase
{
    public function tearDown()
    {
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SiblingRelationExpr_TestModel')->dropTable();
        Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SiblingRelationExpr_RelationModel')->dropTable();
        parent::tearDown();
    }

    public function testLoadLazy()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SiblingRelationExpr_TestModel');

        $row = $m->getRow(1);
        $this->assertEquals($row->sum_foo, 444);

        $row = $m->getRow(2);
        $this->assertEquals($row->sum_foo, 0);
    }

    public function testLoadExpr()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SiblingRelationExpr_TestModel');

        $s = $m->select();
        $s->expr('sum_foo');
        $s->whereEquals('id', 1);
        $row = $m->getRow($s);
        $this->assertEquals($row->sum_foo, 444);

        $s->whereEquals('id', 2);
        $row = $m->getRow($s);
        $this->assertEquals((int)$row->sum_foo, 0);
    }

    //um sicherzustellen dass es nicht lazy nochmal ausgeführt wird (per php) - was dann bei der sql expr natürlich fehlschlägt
    public function testLoadExprWithSql()
    {
        $m = Kwf_Model_Abstract::getInstance('Kwf_Model_DbWithConnection_SiblingRelationExpr_TestModel');

        $s = $m->select();
        $s->expr('sum_fooplusone');
        $s->whereEquals('id', 1);
        $row = $m->getRow($s);
        $this->assertEquals($row->sum_fooplusone, 446);

        $s->whereEquals('id', 2);
        $row = $m->getRow($s);
        $this->assertEquals((int)$row->sum_fooplusone, 0);
    }


}
