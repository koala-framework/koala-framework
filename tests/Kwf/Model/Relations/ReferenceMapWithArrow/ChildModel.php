<?php
class Kwf_Model_Relations_ReferenceMapWithArrow_ChildModel extends Kwf_Model_FnF
{
    protected $_data = array(
        array('id'=>100, 'model_id'=>1)
    );
    protected $_referenceMap = array(
        'Model' => 'model_id->Kwf_Model_Relations_ReferenceMapWithArrow_Model'
    );
}
