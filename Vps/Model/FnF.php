<?php
class Vps_Model_FnF extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_FnF_Row';
    protected $_rowsetClass = 'Vps_Model_FnF_Rowset';
    protected $_data = array();

    public function __construct(array $config = array())
    {
        if (isset($config['data'])) $this->setData($config['data']);
        parent::__construct($config);
    }

    public function find($id)
    {
        $data = array();
        foreach ($this->_data as $d) {
            if (isset($d['id']) && $d['id'] == $id) {
                $data = array($d);
                break;
            }
        }
        return new $this->_rowsetClass(array(
            'model' => $this,
            'rowClass' => $this->_rowClass,
            'data' => $data
        ));
    }
    
    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            $select = $this->select();
            if ($where) $select->where($where);
            if ($order) $select->order($order);
            if ($limit || $start) $select->order($limit, $start);
        } else {
            $select = $where;
        }

        if ($select->getCheckProcessed()) {
            $select->resetProcessed();
        }
        $data = array();
        foreach ($this->_data as $d) {
            if ($this->_matchSelect($d, $select)) {
                $data[] = $d;
            }
        }
        $select->processed(Vps_Model_Select::WHERE_EQUALS);
        $select->processed(Vps_Model_Select::WHERE_ID);

        if ($order = $select->getPart(Vps_Model_Select::ORDER)) {
            $orderData = array();
            foreach ($data as $d) {
                $orderData[$d['id']] = $d[$order];
            }
            asort($orderData);
            $sortedData = array();
            foreach (array_keys($orderData) as $id) {
                foreach ($data as $d) {
                    if ($d['id'] == $id) {
                        $sortedData[] = $d;
                    }
                }
            }
            $data = $sortedData;
            $select->processed(Vps_Model_Select::ORDER);
        }

        $select->checkAndResetProcessed();
        return new $this->_rowsetClass(array(
            'model' => $this,
            'rowClass' => $this->_rowClass,
            'data' => $data
        ));
    }

    public function fetchCount($where = array())
    {
        return count($this->fetchAll($where));
    }

    private function _matchSelect($data, $select)
    {
        if ($id = $select->getPart(Vps_Model_Select::WHERE_ID)) {
            if ($data['id'] != $id) return false;
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
            foreach ($where as $f=>$v) {
                if (!isset($data[$f])) return false;
                if (!is_array($v)) $v = array($v);
                if (!in_array($data[$f], $v)) return false;
            }
        }
        return true;
    }

    public function setData(array $data)
    {
        $this->_data = $data;
    }
}
