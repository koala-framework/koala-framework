<?php
class Vps_Form_MultiFields_TestModel2 extends Vps_Model_FnF
{
    protected $_referenceMap = array(
        'Model1' => array(
            'refModelClass' => 'Vps_Form_MultiFields_TestModel1',
            'column' => 'model1_id'
        )
    );

    protected $_data = array(
        array('id'=>1, 'model1_id'=>1, 'foo'=>'foo0', 'pos'=>1),
        array('id'=>2, 'model1_id'=>1, 'foo'=>'foo1', 'pos'=>2),
        array('id'=>3, 'model1_id'=>1, 'foo'=>'foo2', 'pos'=>3),
    );

}
