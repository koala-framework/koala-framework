<?php
class Vps_Model_FnF extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_FnF_Row';
    protected $_rowsetClass = 'Vps_Model_FnF_Rowset';
    protected $_data = array();
    protected $_columns = array();

    public function __construct(array $config = array())
    {
        if (isset($config['data'])) $this->setData($config['data']);
        if (isset($config['columns'])) $this->_columns = $config['columns'];
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
            if ($limit || $start) $select->limit($limit, $start);
        } else {
            $select = $where;
        }

        $data = array();
        foreach ($this->_data as $d) {
            if ($this->_matchSelect($d, $select)) {
                $data[] = $d;
            }
        }

        if ($order = $select->getPart(Vps_Model_Select::ORDER)) {
            if (count($order) > 1) throw new Vps_Exception("Multiple Order fields not yet implemented");
            $order = current($order);
            $orderData = array();
            foreach ($data as $d) {
                if ($order['field'] == Vps_Model_Select::ORDER_RAND) {
                    $orderData[$d['id']] = rand();
                } else {
                    $orderData[$d['id']] = strtolower($d[$order['field']]);
                }
            }
            if ($order['direction'] == 'ASC') {
                asort($orderData);
            } else if ($order['direction'] == 'DESC') {
                arsort($orderData);
            } else {
                throw new Vps_Exception("Invalid order direction: {$order['direction']}");
            }
            $sortedData = array();
            foreach (array_keys($orderData) as $id) {
                foreach ($data as $d) {
                    if ($d['id'] == $id) {
                        $sortedData[] = $d;
                    }
                }
            }
            $data = $sortedData;
        }

        if ($select->hasPart(Vps_Model_Select::LIMIT_COUNT)) {
            $limitCount = $select->getPart(Vps_Model_Select::LIMIT_COUNT);
            $data = array_slice($data, 0, $limitCount);
        }


        return new $this->_rowsetClass(array(
            'model' => $this,
            'rowClass' => $this->_rowClass,
            'data' => $data
        ));
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
        if ($where = $select->getPart(Vps_Model_Select::WHERE_NULL)) {
            foreach ($where as $f) {
                if (!is_null($data[$f])) return false;
            }
        }
        return true;
    }

    public function setData(array $data)
    {
        $this->_data = $data;
    }

    /**
     * mostly useless (but needed for some tests)
     **/
    public function getColumns()
    {
        return $this->_columns;
    }
}
