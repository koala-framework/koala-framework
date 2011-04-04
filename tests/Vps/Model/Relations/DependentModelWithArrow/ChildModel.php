<?php
class Vps_Model_Relations_DependentModelWithArrow_ChildModel extends Vps_Model_FnF
{
    protected $_data = array(
        array('id'=>100, 'model_id'=>1, 'parent_id'=>200)
    );

    protected $_referenceMap = array(
        'Model' => array(
            'refModelClass' => 'Vps_Model_Relations_DependentModelWithArrow_Model',
            'column' => 'model_id'
        ),
        'Parent' => array(
            'refModelClass' => 'Vps_Model_Relations_DependentModelWithArrow_ChildParentModel',
            'column' => 'parent_id'
        ),
    );
}
