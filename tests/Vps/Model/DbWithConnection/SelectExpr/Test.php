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
        $this->assertEquals($row->count_model2, 3);

        $row->foo = 'a';
        $row->save();
    }

    public function testEvaluateExpr()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model2');

        $this->assertEquals(30, $m->evaluateExpr(new Vps_Model_Select_Expr_Sum('foo2')));
        $this->assertEquals(4, $m->evaluateExpr(new Vps_Model_Select_Expr_Count()));
        $this->assertEquals(3, $m->evaluateExpr(new Vps_Model_Select_Expr_Count('foo2')));
        $this->assertEquals(1, $m->evaluateExpr(new Vps_Model_Select_Expr_Count('foo2', true)));
        $this->assertEquals(2, $m->evaluateExpr(new Vps_Model_Select_Expr_Count('model1_id', true)));

        $s = $m->select();
        $s->whereEquals('model1_id', 1);

        $this->assertEquals(20, $m->evaluateExpr(new Vps_Model_Select_Expr_Sum('foo2'), $s));
        $this->assertEquals(3, $m->evaluateExpr(new Vps_Model_Select_Expr_Count(), $s));
        $this->assertEquals(2, $m->evaluateExpr(new Vps_Model_Select_Expr_Count('foo2'), $s));
        $this->assertEquals(1, $m->evaluateExpr(new Vps_Model_Select_Expr_Count('foo2', true), $s));
        $this->assertEquals(1, $m->evaluateExpr(new Vps_Model_Select_Expr_Count('model1_id', true), $s));
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

    public function testExprsLazy()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1');

        $s = $m1->select();
        $s->order('id');
        $row = $m1->getRow($s);
        $this->assertEquals($row->id, 1);
        $this->assertEquals($row->count_model2, 3);
        $this->assertEquals($row->count_model2_field, 2);
        $this->assertEquals($row->count_model2_distinct, 1);
        $this->assertEquals($row->sum_model2, 20);
        $this->assertEquals($row->count_model2_bam, 2);
        $this->assertEquals($row->count_model2_bam_distinct, 1);
        $this->assertEquals($row->sum_model2_bam, 10);

    }

    public function testExprsWithSelect()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1');

        $s = $m1->select();
        $s->expr('count_model2');
        $s->expr('count_model2_field');
        $s->expr('count_model2_distinct');
        $s->expr('sum_model2');
        $s->expr('count_model2_bam');
        $s->expr('count_model2_bam_distinct');
        $s->expr('sum_model2_bam');
        $s->order('id');
        $row = $m1->getRow($s);
        $this->assertEquals($row->id, 1);
        $this->assertEquals($row->count_model2, 3);
        $this->assertEquals($row->count_model2_field, 2);
        $this->assertEquals($row->count_model2_distinct, 1);
        $this->assertEquals($row->sum_model2, 20);
        $this->assertEquals($row->count_model2_bam, 2);
        $this->assertEquals($row->count_model2_bam_distinct, 1);
        $this->assertEquals($row->sum_model2_bam, 10);

    }
}
