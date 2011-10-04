<?php
/**
 * @group Model_FnF
 * @group Model_Expr_SumFields
 */
class Vps_Model_FnF_ExprSumFields_Test extends Vps_Test_TestCase
{
    private $_model;
    public function setUp()
    {
        parent::setUp();
        $this->_model = new Vps_Model_FnF(array(
            'columns' => array('id', 'value1', 'value2'),
            'data' => array(
                array('id' => 1, 'value1' => 'foo', 'value2'=>100),
                array('id' => 2, 'value1' => 'foo', 'value2'=>400),
                array('id' => 3, 'value1' => 'foo', 'value2'=>400),
                array('id' => 4, 'value1' => 'foo', 'value2'=>0),
                array('id' => 5, 'value1' => 'foo', 'value2'=>500),
            ),
            'exprs' => array(
                'sum_field_int' => new Vps_Model_Select_Expr_SumFields(array('value2', 10)),
                'sum_int_int' => new Vps_Model_Select_Expr_SumFields(array(100, 10, 99)),
                'sum_field_field' => new Vps_Model_Select_Expr_SumFields(array('value2', 'id')),
            )
        ));
    }

    public function testExprSumFields()
    {
        $m = $this->_model;
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

    public function testExprSumFieldsOrder()
    {
        $m = $this->_model;
        $s = $m->select();
        $s->order('sum_field_int');
        $this->assertEquals(4, $m->getRow($s)->id);

        $s = $m->select();
        $s->order('sum_field_int', 'DESC');
        $this->assertEquals(5, $m->getRow($s)->id);
    }

    public function testExprSumFieldsWhereEquals()
    {
        $m = $this->_model;
        $s = $m->select();
        $s->order('id', 'ASC');
        $s->whereEquals('sum_field_int', 410);
        $this->assertEquals(2, $m->getRow($s)->id);
    }
}
