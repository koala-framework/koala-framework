<?php
class Kwf_Model_FnF_SiblingModel extends Kwf_Model_FnF
{
    protected $_primaryKey = 'master_id';
    protected $_columns = array('master_id', 'bar');
    protected $_referenceMap = array(
        'Master' => array(
            'column' => 'master_id',
            'refModelClass' => 'Kwf_Model_FnF_SiblingMasterModel'
        )
    );
}
