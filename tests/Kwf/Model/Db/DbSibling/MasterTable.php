<?php
class Kwf_Model_Db_DbSibling_MasterTable extends Kwf_Db_Table
{
    public $nextSelect;

    protected $_name = 'master';
    protected $_primary = array('id');
    protected $_cols = array('id', 'foo', 'bar');
//     protected $_rowClass = 'Kwf_Model_Db_TestRow';
    protected $_metadata = array(
        'id' => array(
            'DATA_TYPE' => 'int'
        ),
        'foo' => array(
            'DATA_TYPE' => 'string'
        ),
        'bar' => array(
            'DATA_TYPE' => 'string'
        ),
    );
}
