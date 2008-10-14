<?php
abstract class Vps_Model_Data_Abstract extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_Row_Data_Abstract';

    protected $_data = array();
    protected $_autoId;

    public function __construct(array $config = array())
    {
        if (isset($config['data'])) $this->setData($config['data']);
        if (isset($config['autoId'])) (int)$this->_autoId = $config['autoId'];
        parent::__construct($config);
    }

    public function setData(array $data)
    {
        $this->_data = $data;
    }

    public function getData()
    {
        return $this->_data;
    }

    public function update($id, Vps_Model_Row_Interface $row, $rowData)
    {
        foreach ($this->_data as $k=>$i) {
            if (isset($i[$this->getPrimaryKey()]) && $i[$this->getPrimaryKey()] == $id) {
                $this->_data[$k] = $rowData;
                return $rowData[$this->getPrimaryKey()];
            }
        }
        throw new Vps_Exception("Can't find entry with id '$id'");
    }

    public function insert(Vps_Model_Row_Interface $row, $rowData)
    {
        if (!isset($rowData[$this->getPrimaryKey()])) {
            if (!isset($this->_autoId)) {
                $this->_autoId = 0;
                foreach ($this->_data as $k=>$i) {
                    if (isset($i[$this->getPrimaryKey()])) {
                        $this->_autoId = max($i[$this->getPrimaryKey()], $this->_autoId);
                    }
                }
            }
            $this->_autoId++;
            $rowData[$this->getPrimaryKey()] = $this->_autoId;
        }
        $this->_data[] = $rowData;
        return $rowData[$this->getPrimaryKey()];
    }

    public function delete($id, Vps_Model_Row_Interface $row)
    {
        foreach ($this->_data as $k=>$i) {
            if (isset($i[$this->getPrimaryKey()]) && $i[$this->getPrimaryKey()] == $id) {
                unset($this->_data[$k]);
                return;
            }
        }
        throw new Vps_Exception("Can't find entry with id '$id'");
    }

                                                                 //& für performance
    protected function _selectData(Vps_Model_Select $select, array &$inData)
    {
        $data = array();
        foreach ($inData as $d) {
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
        return $data;
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
        if ($where = $select->getPart(Vps_Model_Select::WHERE_NOT_EQUALS)) {
            $ret = false;
            foreach ($where as $f=>$v) {
                if (!isset($data[$f])) { $ret = true; break; }
                if (!is_array($v)) $v = array($v);
                if (!in_array($data[$f], $v)) { $ret = true; break; }
            }
            if (!$ret) return false;
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE_NULL)) {
            foreach ($where as $f) {
                if (!is_null($data[$f])) return false;
            }
        }
        return true;
    }
}
