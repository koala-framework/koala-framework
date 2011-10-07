<?php
class Kwf_Model_Relations_DependentModelWithArrow_ChildModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>100, 'model_id'=>1, 'parent_id'=>200)
    );

    protected $_referenceMap = array(
        'Model' => array(
            'refModelClass' => 'Kwf_Model_Relations_DependentModelWithArrow_Model',
            'column' => 'model_id'
        ),
        'Parent' => array(
            'refModelClass' => 'Kwf_Model_Relations_DependentModelWithArrow_ChildParentModel',
            'column' => 'parent_id'
        ),
    );
}
