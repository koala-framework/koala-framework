<?php
class Kwf_Model_FnF_ExprInteger_Test extends Kwf_Test_TestCase
{
    public function testExpr()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id'),
            'data' => array(
                array('id' => 1)
            ),
            'exprs' => array(
                'price' => new Kwf_Model_Select_Expr_Integer(100),
                'amount' => new Kwf_Model_Select_Expr_Integer('3'),
                'total' => new Kwf_Model_Select_Expr_Multiply(array(
                    new Kwf_Model_Select_Expr_Field('price'),
                    new Kwf_Model_Select_Expr_Field('amount')
                ))
            )
        ));
        $this->assertEquals($model->getRow(1)->price, 100);
        $this->assertEquals($model->getRow(1)->amount, 3);
        $this->assertEquals($model->getRow(1)->total, 300);

        $this->assertTrue($model->getRow(1)->price === 100);
        $this->assertTrue($model->getRow(1)->amount === 3);
    }
}

