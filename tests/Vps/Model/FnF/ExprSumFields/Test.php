<?php
/**
 * @group Model_FnF
 */
class Vps_Model_FnF_ExprSumFields_Test extends PHPUnit_Framework_TestCase
{
    public function testExprSumFields()
    {
        $m = new Vps_Model_FnF(array(
            'columns' => array('id', 'value1', 'value2'),
            'data' => array(
                array('id' => 1, 'value1' => 'foo', 'value2'=>100),
                array('id' => 2, 'value1' => 'foo', 'value2'=>400),
                array('id' => 3, 'value1' => 'foo', 'value2'=>400)
            ),
            'exprs' => array(
                'sum_field_int' => new Vps_Model_Select_Expr_SumFields(array('value2', 10)),
                'sum_int_int' => new Vps_Model_Select_Expr_SumFields(array(100, 10, 99)),
                'sum_field_field' => new Vps_Model_Select_Expr_SumFields(array('value2', 'id')),
            )
        ));

        $this->assertEquals(110, $m->getRow(1)->sum_field_int);
        $this->assertEquals(100+10+99, $m->getRow(1)->sum_int_int);
        $this->assertEquals(101, $m->getRow(1)->sum_field_field);
        $this->assertEquals(410, $m->getRow(2)->sum_field_int);
        $this->assertEquals(100+10+99, $m->getRow(2)->sum_int_int);
        $this->assertEquals(402, $m->getRow(2)->sum_field_field);
        $this->assertEquals(410, $m->getRow(3)->sum_field_int);
        $this->assertEquals(100+10+99, $m->getRow(3)->sum_int_int);
        $this->assertEquals(403, $m->getRow(3)->sum_field_field);
    }
}
