<?php
class Kwf_Model_Union_FnF_ModelSibling extends Kwf_Model_FnF
{
    protected $_referenceMap = array(
        'Master' => array(
            'column' => 'id',
            'refModelClass' => 'Kwf_Model_Union_FnF_TestModel'
        )
    );
    protected $_data = array(
        array('id' => '1m1', 'sib' => 's1', 'sib_bool' => 1),
        array('id' => '1m2', 'sib' => 'ss2', 'sib_bool' => 0),
        array('id' => '2m2', 'sib' => 'sss3', 'sib_bool' => 1),
    );
}
