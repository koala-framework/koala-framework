<?php
class Vps_Model_Db implements Vps_Model_Interface
{
    protected $_rowClass = 'Vps_Model_Db_Row';
    protected $_rowsetClass = 'Vps_Model_Db_Rowset';
    protected $_table;
    protected $_tableName;
    protected $_default = array();

    public function __construct($config = array())
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

    public function fetchCount($where = array())
    {
        if ($where instanceof Zend_Db_Select) {
            $select = $where;
            $select->reset(Zend_Db_Select::COLUMNS);
            $select->from(null, 'COUNT(*)');
        } else if (is_array($where)) {
            $select = $this->_table->select();
            $select->from($this->_table, 'COUNT(*)');
            $select->where($where);
        } else {
            throw new Vps_Exception("array or Zend_Db_Select required as first argument for fetchCount");
        }

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

    public function getAdapter()
    {
        return $this->getTable()->getAdapter();
    }

    public function isEqual(Vps_Model_Interface $other) {
        if ($other instanceof Vps_Model_Db &&
            $this->getTable()->info(Zend_Db_Table_Abstract::NAME) ==
            $other->getTable()->info(Zend_Db_Table_Abstract::NAME)
        ) {
            return true;
        }
        return false;
    }

}
