<?php
abstract class Vps_Model_Data_Abstract extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_Row_Data_Abstract';

    protected $_data = array();
    protected $_autoId;
    protected $_columns = array();
    protected $_primaryKey = 'id';

    public function __construct(array $config = array())
    {
        if (isset($config['data'])) $this->setData($config['data']);
        if (isset($config['autoId'])) (int)$this->_autoId = $config['autoId'];
        if (isset($config['columns'])) $this->_columns = (array)$config['columns'];
        if (isset($config['primaryKey'])) $this->_primaryKey = (string)$config['primaryKey'];
        parent::__construct($config);
    }

    public function setData(array $data)
    {
        $this->_data = $data;
        $this->_rows = array();
    }

    public function getData()
    {
        return $this->_data;
    }

    public function update(Vps_Model_Row_Interface $row, $rowData)
    {
        foreach ($this->_rows as $k=>$i) {
            if ($row === $i) {
                $this->_data[$k] = $rowData;
                return $rowData[$this->getPrimaryKey()];
            }
        }
        throw new Vps_Exception("Can't find entry");
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
            $row->{$this->getPrimaryKey()} = $this->_autoId;
        }
        $this->_data[] = $rowData;
        $key = end(array_keys($this->_data));
        $this->_rows[$key] = $row;
        return $rowData[$this->getPrimaryKey()];
    }

    public function delete(Vps_Model_Row_Interface $row)
    {
        foreach ($this->_rows as $k=>$i) {
            if ($row === $i) {
                unset($this->_data[$k]);
                unset($this->_rows[$k]);
                return;
            }
        }
        throw new Vps_Exception("Can't find entry");
    }

    public function getRowByDataKey($key)
    {
        if (!isset($this->_rows[$key])) {
            $this->_rows[$key] = new $this->_rowClass(array(
                'data' => $this->_data[$key],
                'model' => $this
            ));
        }
        return $this->_rows[$key];
    }

                                                                     //& für performance
    protected function _selectDataKeys(Vps_Model_Select $select, array &$inData)
    {
        $dataKeys = array();
        foreach ($inData as $key=>$d) {
            if ($this->_matchSelect($d, $select)) {
                $dataKeys[] = $key;
            }
        }

        if ($order = $select->getPart(Vps_Model_Select::ORDER)) {
            if (count($order) > 1) throw new Vps_Exception("Multiple Order fields not yet implemented");
            $order = current($order);
            $orderData = array();
            foreach ($dataKeys as $key) {
                if ($order['field'] == Vps_Model_Select::ORDER_RAND) {
                    $orderData[$inData[$key]['id']] = rand();
                } else {
                    $orderData[$inData[$key]['id']] = strtolower($inData[$key][$order['field']]);
                }
            }
            if ($order['direction'] == 'ASC') {
                asort($orderData);
            } else if ($order['direction'] == 'DESC') {
                arsort($orderData);
            } else {
                throw new Vps_Exception("Invalid order direction: {$order['direction']}");
            }
            $sortedDataKeys = array();
            foreach (array_keys($orderData) as $id) {
                foreach ($dataKeys as $key) {
                    if ($inData[$key]['id'] == $id) {
                        $sortedDataKeys[] = $key;
                    }
                }
            }
            $dataKeys = $sortedDataKeys;
        }

        if ($select->hasPart(Vps_Model_Select::LIMIT_COUNT)) {
            $limitCount = $select->getPart(Vps_Model_Select::LIMIT_COUNT);
            $dataKeys = array_slice($dataKeys, 0, $limitCount);
        }
        return $dataKeys;
    }

    private function _matchSelect($data, $select)
    {
        foreach ($data as &$d) {
            $d = (string)$d;
        }
        if ($id = $select->getPart(Vps_Model_Select::WHERE_ID)) {
            if ($data['id'] != (string)$id) return false;
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
            foreach ($where as $f=>$v) {
                if (!is_array($v)) $v = array($v);
                foreach ($v as &$i) $i = (string)$i;
                if (!isset($data[$f])) return false;
                if (!in_array($data[$f], $v)) return false;
            }
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE_NOT_EQUALS)) {
            $foundOneMatching = false;
            foreach ($where as $f=>$v) {
                if (!is_array($v)) $v = array($v);
                foreach ($v as &$i) $i = (string)$i;
                if (isset($data[$f]) && in_array($data[$f], $v)) {
                    $foundOneMatching = true;
                    break;
                }
            }
            if ($foundOneMatching) return false;
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE_NULL)) {
            foreach ($where as $f) {
                if (isset($data[$f]) && !is_null($data[$f])) return false;
            }
        }
        return true;
    }

    public function getPrimaryKey()
    {
        return $this->_primaryKey;
    }

    public function getColumns()
    {
        return $this->_columns;
    }

    public function setColumns(array $columns)
    {
        $this->_columns = $columns;
        return $this;
    }

    protected function _idExists ($id)
    {
        if ($id && $this->getRow($id)) {
            return true;
        }
        return false;
    }
}
