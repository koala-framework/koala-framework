<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Db_SelectExpr
 */
class Vps_Model_DbWithConnection_SelectExpr_Test extends Vps_Model_DbWithConnection_SelectExpr_AbstractTest
{
    public function testWithExpr()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1');

        $s = $m1->select();
        $s->order('id');
        $s->expr('count_model2');
        $row = $m1->getRow($s);
        $this->assertEquals($row->id, 1);
        $this->assertEquals($row->count_model2, 2);

        $row->foo = 'a';
        $row->save();
    }

    public function testWithOrder()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1');

        $s = $m1->select();
        $s->order('count_model2');
        $row = $m1->getRow($s);
        $this->assertEquals($row->id, 2);
        $this->assertEquals($row->count_model2, 1);
    }

    public function testWithOrderAndExpr()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1');

        $s = $m1->select();
        $s->expr('count_model2');
        $s->order('count_model2');
        $row = $m1->getRow($s);
        $this->assertEquals($row->id, 2);
        $this->assertEquals($row->count_model2, 1);
    }
}
