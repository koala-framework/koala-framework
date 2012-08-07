<?php
class Kwf_Model_FnF_ExprAge_Test extends Kwf_Test_TestCase
{
    public function testBirth()
    {
        $today = date('Y-m-d');
        $today = date('Y-m-d', strtotime("$today -1 year"));
        $eighteen = date("Y-m-d", strtotime("$today -17 year"));
        $tomorrow = date("Y-m-d", strtotime("$today +1 day"));
        $yesterday = date("Y-m-d", strtotime("$today - 1 day"));
        $newYearsEve = date('Y')-1 . "-12-31";
        $expectNYE = 0;
        //if it is new Year's Eve the expected age is 2
        if (date('m-d') == '12-31') {
            $expectNYE = 1;
        }
        $newYear = date('Y')-1 . "-01-01";
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'birth'),
            'data' => array(
                array('id' => 1, 'birth' => $eighteen),
                array('id' => 2, 'birth' => $today),
                array('id' => 3, 'birth' => $tomorrow),
                array('id' => 4, 'birth' => $yesterday),
                array('id' => 5, 'birth' => $newYearsEve),
                array('id' => 6, 'birth' => $newYear),
            ),
            'exprs' => array(
                'age' => new Kwf_Model_Select_Expr_Date_Age('birth'),
            )
        ));
        $this->assertEquals($model->getRow(1)->age, 18);
        $this->assertEquals($model->getRow(2)->age, 1);
        $this->assertEquals($model->getRow(3)->age, 0);
        $this->assertEquals($model->getRow(4)->age, 1);
        $this->assertEquals($model->getRow(5)->age, $expectNYE);
        $this->assertEquals($model->getRow(6)->age, 1);
    }
    public function testBirthWithReference()
    {
        $today = date('Y-m-d');
        $today = date('Y-m-d', strtotime("$today -1 year"));
        $eighteen = date("Y-m-d", strtotime("$today -17 year"));
        $tomorrow = date("Y-m-d", strtotime("$today +1 day"));
        $yesterday = date("Y-m-d", strtotime("$today - 1 day"));
        $newYearsEve = date('Y')-1 . "-12-31";
        $expectNYE = 0;
        $expectNY = 1;
        $realTomorrow = date("Y-m-d", strtotime("$tomorrow +1 year"));
        //if it is new Year's Eve the expected age for new Year = 2;
        if ($realTomorrow == date('Y').'-12-31') {
            $expectNYE = 1;
        }
        //if it is new Year's Eve the expected age for new Year = 2;
        if (date('m-d') == '12-31') {
            $expectNY = 2;
        }
        $newYear = date('Y')-1 . "-01-01";
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'birth'),
            'data' => array(
                array('id' => 1, 'birth' => $eighteen),
                array('id' => 2, 'birth' => $today),
                array('id' => 3, 'birth' => $tomorrow),
                array('id' => 4, 'birth' => $yesterday),
                array('id' => 5, 'birth' => $newYearsEve),
                array('id' => 6, 'birth' => $newYear),
            ),
            'exprs' => array(
                'age' => new Kwf_Model_Select_Expr_Date_Age('birth', new Kwf_Date($realTomorrow)),
            )
        ));
        $this->assertEquals($model->getRow(1)->age, 18);
        $this->assertEquals($model->getRow(2)->age, 1);
        $this->assertEquals($model->getRow(3)->age, 1);
        $this->assertEquals($model->getRow(4)->age, 1);
        $this->assertEquals($model->getRow(5)->age, $expectNYE);
        $this->assertEquals($model->getRow(6)->age, $expectNY);
    }
}
