<?php
class Vps_Model_FnF_SiblingModel extends Vps_Model_FnF
{
    protected $_primaryKey = 'master_id';
    protected $_columns = array('master_id', 'bar');
    protected $_referenceMap = array(
        'Master' => array(
            'column' => 'master_id',
            'refModelClass' => 'Vps_Model_FnF_SiblingMasterModel'
        )
    );
}
