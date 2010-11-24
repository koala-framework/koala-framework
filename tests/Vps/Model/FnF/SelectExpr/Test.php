<?php
/**
 * @group Model
 * @group Model_FnF
 * @group Model_FnF_SelectExpr
 */
class Vps_Model_FnF_SelectExpr_Test extends Vps_Test_TestCase
{
    public function testExprs()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_FnF_SelectExpr_Model1');

        $s = $m1->select();
        $s->order('id');
        $row = $m1->getRow($s);
        $this->assertEquals(1, $row->id);
        $this->assertEquals(3, $row->count_model2);
        $this->assertEquals(2, $row->count_model2_field);
        $this->assertEquals(1, $row->count_model2_distinct);
        $this->assertEquals(20, $row->sum_model2);
        $this->assertEquals(2, $row->count_model2_bam);
        $this->assertEquals(1, $row->count_model2_bam_distinct);
        $this->assertEquals(10, $row->sum_model2_bam);
    }

    public function testEvaluateExpr()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_FnF_SelectExpr_Model2');

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
}
