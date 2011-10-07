<?php
class Vps_Model_Db_DbSibling_MasterTable extends Vps_Db_Table_Abstract
{
    public $nextSelect;

    protected $_name = 'master';
    protected $_primary = array('id');
    protected $_cols = array('id', 'foo', 'bar');
//     protected $_rowClass = 'Vps_Model_Db_TestRow';
    protected $_metadata = array(
        'id' => array(
            'DATA_TYPE' => 'int'
        ),
        'foo' => array(),
        'bar' => array(),
    );
}
