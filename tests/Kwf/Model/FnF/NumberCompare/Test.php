<?php
class Kwf_Model_FnF_NumberCompare_Test extends Kwf_Test_TestCase
{
    public function testLower()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'value'),
            'data' => array(
                array('id' => 1, 'value' => -10000000),
                array('id' => 2, 'value' => -1),
                array('id' => 3, 'value' => 0),
                array('id' => 4, 'value' => 1),
                array('id' => 5, 'value' => 10000000),
            ),
            'exprs' => array(
                'lower1' => new Kwf_Model_Select_Expr_Lower('value', -10000000),
                'lower2' => new Kwf_Model_Select_Expr_Lower('value', -1),
                'lower3' => new Kwf_Model_Select_Expr_Lower('value', 0),
                'lower4' => new Kwf_Model_Select_Expr_Lower('value', 1),
                'lower5' => new Kwf_Model_Select_Expr_Lower('value', 10000000)
            )
        ));
        $this->assertEquals($model->getRow(1)->lower1, false);
        $this->assertEquals($model->getRow(1)->lower2, true);
        $this->assertEquals($model->getRow(1)->lower3, true);
        $this->assertEquals($model->getRow(1)->lower4, true);
        $this->assertEquals($model->getRow(1)->lower5, true);

        $this->assertEquals($model->getRow(2)->lower1, false);
        $this->assertEquals($model->getRow(2)->lower2, false);
        $this->assertEquals($model->getRow(2)->lower3, true);
        $this->assertEquals($model->getRow(2)->lower4, true);
        $this->assertEquals($model->getRow(2)->lower5, true);

        $this->assertEquals($model->getRow(3)->lower1, false);
        $this->assertEquals($model->getRow(3)->lower2, false);
        $this->assertEquals($model->getRow(3)->lower3, false);
        $this->assertEquals($model->getRow(3)->lower4, true);
        $this->assertEquals($model->getRow(3)->lower5, true);

        $this->assertEquals($model->getRow(4)->lower1, false);
        $this->assertEquals($model->getRow(4)->lower2, false);
        $this->assertEquals($model->getRow(4)->lower3, false);
        $this->assertEquals($model->getRow(4)->lower4, false);
        $this->assertEquals($model->getRow(4)->lower5, true);

        $this->assertEquals($model->getRow(5)->lower1, false);
        $this->assertEquals($model->getRow(5)->lower2, false);
        $this->assertEquals($model->getRow(5)->lower3, false);
        $this->assertEquals($model->getRow(5)->lower4, false);
        $this->assertEquals($model->getRow(5)->lower5, false);
    }

    public function testLowerEqual()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'value'),
            'data' => array(
                array('id' => 1, 'value' => -10000000),
                array('id' => 2, 'value' => -1),
                array('id' => 3, 'value' => 0),
                array('id' => 4, 'value' => 1),
                array('id' => 5, 'value' => 10000000),
            ),
            'exprs' => array(
                'lower1' => new Kwf_Model_Select_Expr_LowerEqual('value', -10000000),
                'lower2' => new Kwf_Model_Select_Expr_LowerEqual('value', -1),
                'lower3' => new Kwf_Model_Select_Expr_LowerEqual('value', 0),
                'lower4' => new Kwf_Model_Select_Expr_LowerEqual('value', 1),
                'lower5' => new Kwf_Model_Select_Expr_LowerEqual('value', 10000000)
            )
        ));
        $this->assertEquals($model->getRow(1)->lower1, true);
        $this->assertEquals($model->getRow(1)->lower2, true);
        $this->assertEquals($model->getRow(1)->lower3, true);
        $this->assertEquals($model->getRow(1)->lower4, true);
        $this->assertEquals($model->getRow(1)->lower5, true);

        $this->assertEquals($model->getRow(2)->lower1, false);
        $this->assertEquals($model->getRow(2)->lower2, true);
        $this->assertEquals($model->getRow(2)->lower3, true);
        $this->assertEquals($model->getRow(2)->lower4, true);
        $this->assertEquals($model->getRow(2)->lower5, true);

        $this->assertEquals($model->getRow(3)->lower1, false);
        $this->assertEquals($model->getRow(3)->lower2, false);
        $this->assertEquals($model->getRow(3)->lower3, true);
        $this->assertEquals($model->getRow(3)->lower4, true);
        $this->assertEquals($model->getRow(3)->lower5, true);

        $this->assertEquals($model->getRow(4)->lower1, false);
        $this->assertEquals($model->getRow(4)->lower2, false);
        $this->assertEquals($model->getRow(4)->lower3, false);
        $this->assertEquals($model->getRow(4)->lower4, true);
        $this->assertEquals($model->getRow(4)->lower5, true);

        $this->assertEquals($model->getRow(5)->lower1, false);
        $this->assertEquals($model->getRow(5)->lower2, false);
        $this->assertEquals($model->getRow(5)->lower3, false);
        $this->assertEquals($model->getRow(5)->lower4, false);
        $this->assertEquals($model->getRow(5)->lower5, true);
    }

    public function testHigher()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'value'),
            'data' => array(
                array('id' => 1, 'value' => -10000000),
                array('id' => 2, 'value' => -1),
                array('id' => 3, 'value' => 0),
                array('id' => 4, 'value' => 1),
                array('id' => 5, 'value' => 10000000),
            ),
            'exprs' => array(
                'higher1' => new Kwf_Model_Select_Expr_Higher('value', -10000000),
                'higher2' => new Kwf_Model_Select_Expr_Higher('value', -1),
                'higher3' => new Kwf_Model_Select_Expr_Higher('value', 0),
                'higher4' => new Kwf_Model_Select_Expr_Higher('value', 1),
                'higher5' => new Kwf_Model_Select_Expr_Higher('value', 10000000)
            )
        ));
        $this->assertEquals($model->getRow(1)->higher1, false);
        $this->assertEquals($model->getRow(1)->higher2, false);
        $this->assertEquals($model->getRow(1)->higher3, false);
        $this->assertEquals($model->getRow(1)->higher4, false);
        $this->assertEquals($model->getRow(1)->higher5, false);

        $this->assertEquals($model->getRow(2)->higher1, true);
        $this->assertEquals($model->getRow(2)->higher2, false);
        $this->assertEquals($model->getRow(2)->higher3, false);
        $this->assertEquals($model->getRow(2)->higher4, false);
        $this->assertEquals($model->getRow(2)->higher5, false);

        $this->assertEquals($model->getRow(3)->higher1, true);
        $this->assertEquals($model->getRow(3)->higher2, true);
        $this->assertEquals($model->getRow(3)->higher3, false);
        $this->assertEquals($model->getRow(3)->higher4, false);
        $this->assertEquals($model->getRow(3)->higher5, false);

        $this->assertEquals($model->getRow(4)->higher1, true);
        $this->assertEquals($model->getRow(4)->higher2, true);
        $this->assertEquals($model->getRow(4)->higher3, true);
        $this->assertEquals($model->getRow(4)->higher4, false);
        $this->assertEquals($model->getRow(4)->higher5, false);

        $this->assertEquals($model->getRow(5)->higher1, true);
        $this->assertEquals($model->getRow(5)->higher2, true);
        $this->assertEquals($model->getRow(5)->higher3, true);
        $this->assertEquals($model->getRow(5)->higher4, true);
        $this->assertEquals($model->getRow(5)->higher5, false);
    }

    public function testHigherEqual()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'value'),
            'data' => array(
                array('id' => 1, 'value' => -10000000),
                array('id' => 2, 'value' => -1),
                array('id' => 3, 'value' => 0),
                array('id' => 4, 'value' => 1),
                array('id' => 5, 'value' => 10000000),
            ),
            'exprs' => array(
                'higher1' => new Kwf_Model_Select_Expr_HigherEqual('value', -10000000),
                'higher2' => new Kwf_Model_Select_Expr_HigherEqual('value', -1),
                'higher3' => new Kwf_Model_Select_Expr_HigherEqual('value', 0),
                'higher4' => new Kwf_Model_Select_Expr_HigherEqual('value', 1),
                'higher5' => new Kwf_Model_Select_Expr_HigherEqual('value', 10000000)
            )
        ));
        $this->assertEquals($model->getRow(1)->higher1, true);
        $this->assertEquals($model->getRow(1)->higher2, false);
        $this->assertEquals($model->getRow(1)->higher3, false);
        $this->assertEquals($model->getRow(1)->higher4, false);
        $this->assertEquals($model->getRow(1)->higher5, false);

        $this->assertEquals($model->getRow(2)->higher1, true);
        $this->assertEquals($model->getRow(2)->higher2, true);
        $this->assertEquals($model->getRow(2)->higher3, false);
        $this->assertEquals($model->getRow(2)->higher4, false);
        $this->assertEquals($model->getRow(2)->higher5, false);

        $this->assertEquals($model->getRow(3)->higher1, true);
        $this->assertEquals($model->getRow(3)->higher2, true);
        $this->assertEquals($model->getRow(3)->higher3, true);
        $this->assertEquals($model->getRow(3)->higher4, false);
        $this->assertEquals($model->getRow(3)->higher5, false);

        $this->assertEquals($model->getRow(4)->higher1, true);
        $this->assertEquals($model->getRow(4)->higher2, true);
        $this->assertEquals($model->getRow(4)->higher3, true);
        $this->assertEquals($model->getRow(4)->higher4, true);
        $this->assertEquals($model->getRow(4)->higher5, false);

        $this->assertEquals($model->getRow(5)->higher1, true);
        $this->assertEquals($model->getRow(5)->higher2, true);
        $this->assertEquals($model->getRow(5)->higher3, true);
        $this->assertEquals($model->getRow(5)->higher4, true);
        $this->assertEquals($model->getRow(5)->higher5, true);
    }
}
