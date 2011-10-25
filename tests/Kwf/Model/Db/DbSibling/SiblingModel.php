<?php
class Kwf_Model_Db_DbSibling_SiblingModel extends Kwf_Model_Db
{
    protected $_referenceMap = array(
        'Master' => array(
            'column' => 'master_id',
            'refModelClass' => 'Kwf_Model_Db_DbSibling_MasterModel'
        )
    );
    protected function _init()
    {
        $this->_table = new Kwf_Model_Db_DbSibling_SiblingTable(array(
            'db'=>new Kwf_Model_Db_TestAdapter()
        ));
        parent::_init();
    }
}
