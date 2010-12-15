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
        $this->assertEquals(1, $row->id);
        $this->assertEquals(3, $row->count_model2);

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
        $s->where('id!=3');
        $s->order('count_model2');
        $row = $m1->getRow($s);
        $this->assertEquals(2, $row->id);
        $this->assertEquals(1, $row->count_model2);
    }

    public function testWithOrderAndExpr()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1');

        $s = $m1->select();
        $s->where('id!=3');
        $s->expr('count_model2');
        $s->order('count_model2');
        $row = $m1->getRow($s);
        $this->assertEquals(2, $row->id);
        $this->assertEquals(1, $row->count_model2);
    }

    public function testExprsLazy()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1');

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
        $this->assertEquals(1, $row->id);
        $this->assertEquals(3, $row->count_model2);
        $this->assertEquals(2, $row->count_model2_field);
        $this->assertEquals(1, $row->count_model2_distinct);
        $this->assertEquals(20, $row->sum_model2);
        $this->assertEquals(2, $row->count_model2_bam);
        $this->assertEquals(1, $row->count_model2_bam_distinct);
        $this->assertEquals(10, $row->sum_model2_bam);
    }

    public function testParentExprLazy()
    {
        $m2 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model2');

        $s = $m2->select();
        $s->order('id');
        $row = $m2->getRow($s);

        $this->assertEquals(1, $row->id);
        $this->assertEquals('abcd', $row->model1_bar);
        $this->assertEquals('abcd10', $row->model1_bar_concat_foo2);
        $this->assertEquals('abcd_string', $row->model1_bar_concat_string);
        $this->assertEquals('abcd10abcd_string', $row->model1_bar_concat_foo2_bar_string);

        $this->assertEquals('abc', $row->strpad_3_right);
        $this->assertEquals('abcd', $row->strpad_4_right);
        $this->assertEquals('abcd00', $row->strpad_6_right);

        $this->assertEquals('abc', $row->strpad_3_left);
        $this->assertEquals('abcd', $row->strpad_4_left);
        $this->assertEquals('00abcd', $row->strpad_6_left);
    }

    public function testParentExprWithSelect()
    {
        $m2 = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model2');

        $s = $m2->select();
        $s->expr('model1_bar');
        $s->expr('model1_bar_concat_foo2');
        $s->expr('model1_bar_concat_string');
        $s->expr('model1_bar_concat_foo2_bar_string');
        $s->expr('strpad_3_right');
        $s->expr('strpad_4_right');
        $s->expr('strpad_6_right');
        $s->expr('strpad_3_left');
        $s->expr('strpad_4_left');
        $s->expr('strpad_6_left');
        $s->order('id');
        $row = $m2->getRow($s);

        $this->assertEquals(1, $row->id);
        $this->assertEquals('abcd', $row->model1_bar);
        $this->assertEquals('abcd10', $row->model1_bar_concat_foo2);
        $this->assertEquals('abcd_string', $row->model1_bar_concat_string);
        $this->assertEquals('abcd10abcd_string', $row->model1_bar_concat_foo2_bar_string);

        $this->assertEquals('abc', $row->strpad_3_right);
        $this->assertEquals('abcd', $row->strpad_4_right);
        $this->assertEquals('abcd00', $row->strpad_6_right);

        $this->assertEquals('abc', $row->strpad_3_left);
        $this->assertEquals('abcd', $row->strpad_4_left);
        $this->assertEquals('00abcd', $row->strpad_6_left);
    }

    public function testExprContains()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_DbWithConnection_SelectExpr_Model1');
        $s = $m->select();
        $s->where(new Vps_Model_Select_Expr_Child_Contains('Model2'));
        $this->assertEquals(2, $m->countRows($s));

        $s = $m->select();
        $s->where(new Vps_Model_Select_Expr_Not(new Vps_Model_Select_Expr_Child_Contains('Model2')));
        $this->assertEquals(1, $m->countRows($s));
    }
}
