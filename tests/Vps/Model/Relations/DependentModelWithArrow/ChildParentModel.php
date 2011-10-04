<?php
class Vps_Model_Relations_DependentModelWithArrow_ChildParentModel extends Vps_Model_FnF
{
    protected $_data = array(
        array('id'=>200)
    );

    protected $_dependentModels = array(
        'Child' => 'Vps_Model_Relations_DependentModelWithArrow_Model->Child'
    );
}
