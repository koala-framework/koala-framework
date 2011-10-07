<?php
class Vps_Model_Db_DbSibling_SiblingTable extends Vps_Db_Table_Abstract
{
    public $nextSelect;

    protected $_name = 'sibling';
    protected $_primary = array('id');
    protected $_cols = array('master_id', 'baz');
//     protected $_rowClass = 'Vps_Model_Db_TestRow';
    protected $_metadata = array(
        'master_id' => array(
            'DATA_TYPE' => 'int'
        ),
        'baz' => array(),
    );

    protected function _setupMetadata()
    {}
    protected function _setupPrimaryKey()
    {}

    public function select()
    {
        return $this->nextSelect;
    }
    
    public function fetchAll()
    {
    }
}
