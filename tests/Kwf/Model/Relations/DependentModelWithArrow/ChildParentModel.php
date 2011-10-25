<?php
class Kwf_Model_Relations_DependentModelWithArrow_ChildParentModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>200)
    );

    protected $_dependentModels = array(
        'Child' => 'Kwf_Model_Relations_DependentModelWithArrow_Model->Child'
    );
}
