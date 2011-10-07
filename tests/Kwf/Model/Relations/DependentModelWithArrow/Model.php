<?php
class Vps_Model_Relations_DependentModelWithArrow_Model extends Vps_Model_FnF
{
    protected $_data = array(
        array('id'=>1)
    );

    protected $_dependentModels = array(
        'Child' => 'Vps_Model_Relations_DependentModelWithArrow_ChildModel'
    );
}