<?php
abstract class Vps_Model_Abstract implements Vps_Model_Interface
{
    protected $_rowClass = 'Vps_Model_Row_Abstract';
    protected $_rowsetClass = 'Vps_Model_Rowset_Abstract';

    public function __construct(array $config = array())
    {
        $this->_init();
    }

    protected function _init()
    {
    }

    public function createRow(array $data=array())
    {
        if (!isset($data['id'])) $data['id'] = null;
        return new $this->_rowClass(array(
            'data' => $id,
            'model' => $this
        ));
    }

    public function find($id)
    {
    }

    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
    }

    public function fetchCount(array $where = array())
    {
    }

    public function getPrimaryKey()
    {
        return 'id';
    }
}
