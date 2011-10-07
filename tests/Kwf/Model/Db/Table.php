<?php
class Vps_Model_Db_Table extends Vps_Db_Table_Abstract
{
    protected $_name = 'testtable';
    protected $_primary = array('id');
    protected $_cols = array('id', 'foo', 'bar', 'orderKey');
    protected $_rowClass = 'Vps_Model_Db_TestRow';
    protected $_metadata = array(
        'id' => array(
            'DATA_TYPE' => 'int'
        ),
        'foo' => array(),
        'bar' => array(),
        'orderKey' => array()
    );
}
