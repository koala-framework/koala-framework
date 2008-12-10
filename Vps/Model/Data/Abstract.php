<?php
abstract class Vps_Model_Data_Abstract extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_Row_Data_Abstract';

    protected $_data = null;
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

    public function getData()
    {
        if (!$this->_data) $this->_data = array();
        return $this->_data;
    }

    protected function _afterDataUpdate()
    {
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where) || $where instanceof Vps_Model_Select_Expr_Interface) {
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $dataKeys = $this->_selectDataKeys($select, $this->getData());
        return new $this->_rowsetClass(array(
            'model' => $this,
            'dataKeys' => $dataKeys
        ));
    }

    public function update(Vps_Model_Row_Interface $row, $rowData)
    {
        $this->getData();
        foreach ($this->_rows as $k=>$i) {
            if ($row === $i) {
                $this->_data[$k] = $rowData;
                $this->_afterDataUpdate();
                return $rowData[$this->getPrimaryKey()];
            }
        }
        throw new Vps_Exception("Can't find entry");
    }

    public function insert(Vps_Model_Row_Interface $row, $rowData)
    {
        if ($row->{$this->getPrimaryKey()}) {
            if ($this->getRow($row->{$this->getPrimaryKey()})) {
                throw new Vps_Exception("Duplicate Entry for Row ".$row->{$this->getPrimaryKey()});
            }
        }
        $this->getData();
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
        $this->_afterDataUpdate();
        $key = end(array_keys($this->_data));
        $this->_rows[$key] = $row;
        return $rowData[$this->getPrimaryKey()];
    }

    public function delete(Vps_Model_Row_Interface $row)
    {
        $this->getData();
        foreach ($this->_rows as $k=>$i) {
            if ($row === $i) {
                unset($this->_data[$k]);
                $this->_afterDataUpdate();
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
            //TODO: implement Multiple Order fields
            $order = current($order);
            $orderData = array();
            foreach ($dataKeys as $key) {
                if ($order['field'] instanceof Zend_Db_Expr) {
                    //NOT IMPLEMENTED!
                    $orderData[$inData[$key]['id']] = '';
                } else if ($order['field'] == Vps_Model_Select::ORDER_RAND) {
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

        if ($select->hasPart(Vps_Model_Select::LIMIT_OFFSET)) {
            $limitOffset = $select->getPart(Vps_Model_Select::LIMIT_OFFSET);
            $dataKeys = array_slice($dataKeys, $limitOffset);
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
            if ($data[$this->getPrimaryKey()] != (string)$id) return false;
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
            foreach ($where as $f=>$v) {
                if (!is_array($v)) $v = array($v);
                foreach ($v as &$i) $i = (string)$i;
                if (!isset($data[$f])) return false;
                if (!in_array($data[$f], $v)) return false;
            }
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE_EXPRESSION)) {
            foreach ($where as $expr) {
                if (!$this->_checkExpressions($expr, $data)) return false;
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

    private function _checkExpressions(Vps_Model_Select_Expr_Interface $expr, $data) {
        if ($expr instanceof Vps_Model_Select_Expr_Equals) {
            if (!($data[$expr->getField()] && $data[$expr->getField()] == $expr->getValue())) {
                return false;
            }
        } else if ($expr instanceof Vps_Model_Select_Expr_Higher) {
            if (!($data[$expr->getField()] && $data[$expr->getField()] > $expr->getValue())) {
                return false;
            }
        } else if ($expr instanceof Vps_Model_Select_Expr_Smaller) {
            if (!($data[$expr->getField()] && $data[$expr->getField()] < $expr->getValue())) {
                return false;
            }
        } else if ($expr instanceof Vps_Model_Select_Expr_Contains) {
            if (!(isset($data[$expr->getField()]) && $data[$expr->getField()] && is_numeric(strpos($data[$expr->getField()], $expr->getValue())))) {
                return false;
            }
        } else if ($expr instanceof Vps_Model_Select_Expr_Not) {
                if ($this->_checkExpressions($expr->getExpression(), $data)) {
                    return false;
                }
        } else if ($expr instanceof Vps_Model_Select_Expr_Or) {
            foreach ($expr->getExpressions() as $orExpr) {
                if ($this->_checkExpressions($orExpr, $data)) {
                    return true;
                }
            }
            return false;
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

    public function getUniqueIdentifier() {
        throw new Vps_Exception('Not implemented');
    }
}
