<?php
class Vps_Form_MultiFields_TestModel1 extends Vps_Model_FnF
{
    protected $_dependentModels = array(
        'Model2' => 'Vps_Form_MultiFields_TestModel2'
    );
    protected $_data = array(array('id'=>1, 'blub'=>'blub0'));
}
