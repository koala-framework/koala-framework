<?php
class Vps_Model_Relations_ReferenceMapWithArrow_ChildModel extends Vps_Model_FnF
{
    protected $_data = array(
        array('id'=>100, 'model_id'=>1)
    );
    protected $_referenceMap = array(
        'Model' => 'model_id->Vps_Model_Relations_ReferenceMapWithArrow_Model'
    );
}
