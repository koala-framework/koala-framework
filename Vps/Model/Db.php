<?php
class Vps_Model_Db implements Vps_Model_Interface
{
    protected $_rowClass = 'Vps_Model_Db_Row';
    protected $_rowsetClass = 'Vps_Model_Db_Rowset';
    protected $_table;
    protected $_tableName;
    protected $_default = array();

    public function __construct($config)
    {
        if (isset($config['table'])) $this->_table = $config['table'];
        if (isset($config['default'])) $this->_default = $config['default'];
        $this->_init();
    }
    protected function _init()
    {
        if (!isset($this->_table) && isset($this->_tableName)) {
            $this->_table = new $this->_tableName;
        }
    }

    public function createRow(array $data=array())
    {
        $data = array_merge($this->_default, $data);
        return new $this->_rowClass(array(
            'row' => $this->_table->createRow($data),
            'model' => $this
        ));
    }

    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->find($id),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }
    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->fetchAll($where, $order, $limit, $start),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function fetchCount(array $where = array())
    {
        $select = $this->_table->select();

        $select->from($this->_table, 'COUNT(*)');

        //TODO: das gehört hier nicht her
        if ($this->_table instanceof Vps_Model_User_Users) {
            $where = $this->_table->prepareWhere($where);
            if (!is_array($where)) $where = array($where);
        }

        $select->where($where);

        return $this->_table->getAdapter()->query($select)->fetchColumn();
    }

    public function getPrimaryKey()
    {
        $info = $this->_table->info();
        $ret = $info['primary'];
        if (sizeof($ret) == 1) {
            $ret = array_values($ret);
            $ret = $ret[0];
        }
        return $ret;
    }

    public function getTable()
    {
        return $this->_table;
    }

}
