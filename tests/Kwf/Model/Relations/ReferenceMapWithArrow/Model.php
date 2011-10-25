<?php
class Kwf_Model_Relations_ReferenceMapWithArrow_Model extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>1)
    );
    protected $_dependentModels = array(
        'Child' => 'Kwf_Model_Relations_ReferenceMapWithArrow_ChildModel'
    );
}
