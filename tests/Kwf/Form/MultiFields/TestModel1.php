<?php
class Kwf_Form_MultiFields_TestModel1 extends Kwf_Model_FnF
{
    protected $_dependentModels = array(
        'Model2' => 'Kwf_Form_MultiFields_TestModel2'
    );
    protected $_data = array(array('id'=>1, 'blub'=>'blub0'));
}
