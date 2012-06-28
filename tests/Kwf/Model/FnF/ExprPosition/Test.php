<?php
class Kwf_Model_FnF_ExprPosition_Test extends Kwf_Test_TestCase
{
    public function testPos()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'points'),
            'data' => array(
                array('id' => 1, 'points' => 1000),   //2
                array('id' => 2, 'points' => 500),    //3
                array('id' => 3, 'points' => -50),    //5
                array('id' => 4, 'points' => 0),      //4
                array('id' => 5, 'points' => 10000),  //1
            ),
            'exprs' => array(
                'position' => new Kwf_Model_Select_Expr_Position('points'),
            )
        ));

        $this->assertEquals($model->getRow(1)->position, 2);
        $this->assertEquals($model->getRow(2)->position, 3);
        $this->assertEquals($model->getRow(3)->position, 5);
        $this->assertEquals($model->getRow(4)->position, 4);
        $this->assertEquals($model->getRow(5)->position, 1);
    }
    public function testPosDir()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'points'),
            'data' => array(
                array('id' => 1, 'points' => 1000),   //4
                array('id' => 2, 'points' => 500),    //3
                array('id' => 3, 'points' => -50),    //1
                array('id' => 4, 'points' => 0),      //2
                array('id' => 5, 'points' => 10000),  //5
            ),
            'exprs' => array(
                'position' => new Kwf_Model_Select_Expr_Position('points', array(), 'asc'),
            )
        ));

        $this->assertEquals($model->getRow(1)->position, 4);
        $this->assertEquals($model->getRow(2)->position, 3);
        $this->assertEquals($model->getRow(3)->position, 1);
        $this->assertEquals($model->getRow(4)->position, 2);
        $this->assertEquals($model->getRow(5)->position, 5);
    }

    public function testWithGroup()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'points', 'gr'),
            'data' => array(
                array('id' => 1, 'points' => 1000, 'gr'=>1),   //2
                array('id' => 2, 'points' => 500, 'gr'=>2),    //1
                array('id' => 3, 'points' => -50, 'gr'=>1),    //3
                array('id' => 4, 'points' => 0, 'gr'=>2),      //2
                array('id' => 5, 'points' => 10000, 'gr'=>1),  //1
            ),
            'exprs' => array(
                'position' => new Kwf_Model_Select_Expr_Position('points', array('gr')),
            )
        ));
        $this->assertEquals($model->getRow(1)->position, 2);
        $this->assertEquals($model->getRow(2)->position, 1);
        $this->assertEquals($model->getRow(3)->position, 3);
        $this->assertEquals($model->getRow(4)->position, 2);
        $this->assertEquals($model->getRow(5)->position, 1);
    }
    public function testDirWithGroup()
    {
        $model = new Kwf_Model_FnF(array(
            'columns' => array('id', 'points', 'gr'),
            'data' => array(
                array('id' => 1, 'points' => 1000, 'gr'=>1),   //2
                array('id' => 2, 'points' => 500, 'gr'=>2),    //2
                array('id' => 3, 'points' => -50, 'gr'=>1),    //1
                array('id' => 4, 'points' => 0, 'gr'=>2),      //1
                array('id' => 5, 'points' => 10000, 'gr'=>1),  //3
            ),
            'exprs' => array(
                'position' => new Kwf_Model_Select_Expr_Position('points', array('gr'), 'asc'),
            )
        ));
        $this->assertEquals($model->getRow(1)->position, 2);
        $this->assertEquals($model->getRow(2)->position, 2);
        $this->assertEquals($model->getRow(3)->position, 1);
        $this->assertEquals($model->getRow(4)->position, 1);
        $this->assertEquals($model->getRow(5)->position, 3);
    }

}
