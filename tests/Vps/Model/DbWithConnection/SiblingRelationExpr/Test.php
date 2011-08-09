<?php
/**
 * @group Vps_Model_Db_SiblingRelationExpr
 */
class Vps_Model_DbWithConnection_SiblingRelationExpr_Test extends Vps_Test_TestCase
{
    public function shutDown()
    {
        Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SiblingRelationExpr_TestModel')->dropTable();
        Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SiblingRelationExpr_RelationModel')->dropTable();
        parent::shutDown();
    }

    public function testLoadLazy()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SiblingRelationExpr_TestModel');

        $row = $m->getRow(1);
        $this->assertEquals($row->sum_foo, 444);

        $row = $m->getRow(2);
        $this->assertEquals($row->sum_foo, 0);
    }

    public function testLoadExpr()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SiblingRelationExpr_TestModel');

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
        $m = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SiblingRelationExpr_TestModel');

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
