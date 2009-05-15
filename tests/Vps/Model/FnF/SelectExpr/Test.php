<?php
/**
 * @group Model
 * @group Model_FnF
 * @group Model_FnF_SelectExpr
 */
class Vps_Model_FnF_SelectExpr_Test extends PHPUnit_Framework_TestCase
{
    public function testExprs()
    {
        $m1 = Vps_Model_Abstract::getInstance('Vps_Model_FnF_SelectExpr_Model1');

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
}
