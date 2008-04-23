<?php
class Vps_Model_FieldRows extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_FieldRows_Row';
    protected $_rowsetClass = 'Vps_Model_FieldRows_Rowset';
    protected $_fieldName;
    protected $_parentRow;
    protected $_data;
    protected $_autoId;

    public function __construct(array $config = array())
    {
        if (isset($config['fieldName'])) {
            $this->_fieldName = $config['fieldName'];
        }
        if (isset($config['parentRow'])) {
            $this->setParentRow($config['parentRow']);
        }
        parent::__construct($config);
    }

    public function setParentRow(Vps_Model_Row_Interface $row)
    {
        $this->_parentRow = $row;
        $v = unserialize($this->_parentRow->{$this->_fieldName});
        if (isset($v['data'])) {
            $this->_data = $v['data'];
        } else {
            $this->_data = array();
        }
        if (isset($v['autoId'])) {
            $this->_autoId = $v['autoId'];
        } else {
            $this->_autoId = 0;
        }
    }

    public function createRow(array $data=array())
    {
        $data[$this->getPrimaryKey()] = null;
        return new $this->_rowClass(array(
            'model' => $this,
            'data' => $data,
        ));
    }
    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'model' => $this,
            'data' => array($this->_data[$id]),
            'rowClass' => $this->_rowClass
        ));
    }

    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        if ($where) throw new Vps_Exception('where is not yet implmented');
        if ($order) throw new Vps_Exception('order is not yet implmented');
        if ($limit) throw new Vps_Exception('limit is not yet implmented');
        if ($start) throw new Vps_Exception('start is not yet implmented');
        return new $this->_rowsetClass(array(
            'model' => $this,
            'data' => array_values($this->_data),
            'rowClass' => $this->_rowClass
        ));
    }
    public function fetchCount(array $where = array())
    {
        if ($where) throw new Vps_Exception('where is not yet implmented');
        return count($this->_data);
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function insert($data)
    {
        $this->_autoId++;
        $data[$this->getPrimaryKey()] = $this->_autoId;
        $this->_data[$this->_autoId] = $data;
        $this->_updateRow();
        return $this->_autoId;
    }
    public function update($id, $data)
    {
        $this->_data[$id] = $data;
        $this->_updateRow();
    }
    public function delete($id)
    {
        unset($this->_data[$this->_autoId]);
        $this->_updateRow();
    }

    private function _updateRow()
    {
        $v = serialize(array(
            'data' => $this->_data,
            'autoId' => $this->_autoId
        ));
        $this->_parentRow->{$this->_fieldName} = $v;
        $this->_parentRow->save();
    }

    public function fetchByParentRow($parentRow)
    {
        $this->setParentRow($parentRow);
        return $this->fetchAll();
    }
}
