<?php
class Kwf_Model_FnF_ExprDate_Test extends Kwf_Test_TestCase
{
    public function testExpr()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'date'),
            'data' => array(
                array('id' => 1, 'date' => '2013-12-04'),
                array('id' => 2, 'date' => '2003-12-05'),
            ),
            'exprs' => array(
                'year1' => new Kwf_Model_Select_Expr_Date_Year('date'),
                'year2' => new Kwf_Model_Select_Expr_Date_Year('date', Kwf_Model_Select_Expr_Date_Year::FORMAT_DIGITS_TWO),
                'format1' => new Kwf_Model_Select_Expr_Date_Format('date'),
                'format2' => new Kwf_Model_Select_Expr_Date_Format('date', 'd.m.Y'),
            )
        ));
        $this->assertEquals($model->getRow(1)->year1, 2013);
        $this->assertEquals($model->getRow(2)->year1, 2003);
        $this->assertEquals($model->getRow(1)->year2, 13);
        $this->assertEquals($model->getRow(2)->year2, 03);
        $this->assertEquals($model->getRow(1)->format1, '2013-12-04');
        $this->assertEquals($model->getRow(2)->format1, '2003-12-05');
        $this->assertEquals($model->getRow(1)->format2, '04.12.2013');
        $this->assertEquals($model->getRow(2)->format2, '05.12.2003');
    }
}
