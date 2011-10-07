<?php
/**
 * @group Model
 * @group Model_Db
 * @group Model_DbWithConnection
 * @group Model_Db_SelectExpr_Proxy1
 */
class Vps_Model_DbWithConnection_SelectExpr_WithModel1AsProxy_Test extends Vps_Model_DbWithConnection_SelectExpr_AbstractTest
{
    public function testIt()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_WithModel1AsProxy_Proxy1');
        $m2 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_WithModel1AsProxy_Model2');

        $s = $m1->select();
        $s->order('id');
        $s->expr('count_model2');
        $row = $m1->getRow($s);

        $this->assertTrue($row->hasColumn('count_model2'));
        $this->assertEquals($row->id, 1);
        $this->assertEquals($row->count_model2, 3);

        $row->foo = 'a';
        $row->save();
    }
}
