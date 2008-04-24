<?php
abstract class Vps_Model_Abstract implements Vps_Model_Interface
{
    protected $_rowClass = 'Vps_Model_Row_Abstract';
    protected $_rowsetClass = 'Vps_Model_Rowset_Abstract';
    protected $_default = array();

    public function __construct(array $config = array())
    {
        if (isset($config['default'])) $this->_default = $config['default'];
        $this->_init();
    }

    protected function _init()
    {
    }

    public function createRow(array $data=array())
    {
        if (!isset($data['id'])) $data['id'] = null;
        $data = array_merge($this->_default, $data);
        return new $this->_rowClass(array(
            'data' => $data,
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

    public function getDefault()
    {
        return $this->_default;
    }
}
