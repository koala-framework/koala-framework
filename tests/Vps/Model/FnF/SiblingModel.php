<?php
class Vps_Model_FnF_SiblingModel extends Vps_Model_FnF
{
    protected $_primaryKey = 'master_id';
    protected $_columns = array('master_id', 'bar');
    protected $_data = array(
        array('master_id'=>1, 'bar'=>'bar1'),
        array('master_id'=>2, 'bar'=>'bar2'),
        array('master_id'=>3, 'bar'=>'bar3')
    );
    protected $_referenceMap = array(
        'Master' => array(
            'column' => 'master_id',
            'refModelClass' => 'Vps_Model_FnF_SiblingMasterModel'
        )
    );
}
