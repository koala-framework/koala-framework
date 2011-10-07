<?php
class Vps_Model_Db_DbSibling_SiblingModel extends Vps_Model_Db
{
    protected $_referenceMap = array(
        'Master' => array(
            'column' => 'master_id',
            'refModelClass' => 'Vps_Model_Db_DbSibling_MasterModel'
        )
    );
    protected function _init()
    {
        $this->_table = new Vps_Model_Db_DbSibling_SiblingTable(array(
            'db'=>new Vps_Model_Db_TestAdapter()
        ));
        parent::_init();
    }
}
