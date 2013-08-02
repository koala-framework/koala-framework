<?php
/**
 * @package Model
 */
abstract class Kwf_Model_Data_Abstract extends Kwf_Model_Abstract
{
    protected $_rowClass = 'Kwf_Model_Row_Data_Abstract';

    protected $_data = null;
    protected $_autoId;
    protected $_columns = array();
    protected $_primaryKey = 'id';
    protected $_uniqueColumns;

    public function __construct(array $config = array())
    {
        if (isset($config['data'])) $this->setData($config['data']);
        if (isset($config['autoId'])) (int)$this->_autoId = $config['autoId'];
        if (isset($config['columns'])) $this->_columns = (array)$config['columns'];
        if (isset($config['primaryKey'])) $this->_primaryKey = (string)$config['primaryKey'];
        if (isset($config['uniqueColumns'])) $this->_uniqueColumns = (array)$config['uniqueColumns'];
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

    public function getIds($where=null, $order=null, $limit=null, $start=null)
    {
        $dataKeys = $this->_getDataKeys($where, $order, $limit, $start);
        $ret = array();
        foreach ($dataKeys as $key) {
            $ret[] = $this->_data[$key][$this->_primaryKey];
        }
        return $ret;
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        $dataKeys = $this->_getDataKeys($where, $order, $limit, $start);
        return new $this->_rowsetClass(array(
            'model' => $this,
            'dataKeys' => $dataKeys
        ));
    }

    public function countRows($select = array())
    {
        $dataKeys = $this->_getDataKeys($select, null, null, null);
        return count($dataKeys);
    }

    public function deleteRows($where)
    {
        foreach ($this->getRows($where) as $row) $row->delete();
        $this->_afterDeleteRows($where);
    }

    public function updateRows($data, $where)
    {
        foreach ($this->getRows($where) as $row) {
            foreach ($data as $key => $val) {
                $row->$key = $val;
            }
            $row->save();
        }
    }

    private function _getDataKeys($where, $order, $limit, $start)
    {
        if (!is_object($where) || $where instanceof Kwf_Model_Select_Expr_Interface) {
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $data = $this->getData();
        return $this->_selectDataKeys($select, $data);
    }

    public function update(Kwf_Model_Row_Interface $row, $rowData)
    {
        $this->getData();
        foreach ($this->_rows as $k=>$i) {
            if ($row === $i) {
                $this->_data[$k] = $rowData;
                $this->_afterDataUpdate();
                $this->_dataModified();
                return $rowData[$this->getPrimaryKey()];
            }
        }
        throw new Kwf_Exception("Can't find entry");
    }

    public function insert(Kwf_Model_Row_Interface $row, $rowData)
    {
        if ($row->{$this->getPrimaryKey()}) {
            if ($this->getRow($row->{$this->getPrimaryKey()})) {
                throw new Kwf_Exception("Duplicate Entry for Row ".$row->{$this->getPrimaryKey()});
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
        $this->_dataModified();
        return $rowData[$this->getPrimaryKey()];
    }

    public function delete(Kwf_Model_Row_Interface $row)
    {
        $this->getData();
        foreach ($this->_rows as $k=>$i) {
            if ($row === $i) {
                unset($this->_data[$k]);
                $this->_afterDataUpdate();
                unset($this->_rows[$k]);
                $this->_dataModified();
                return;
            }
        }
        throw new Kwf_Exception("Can't find entry");
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
    protected function _selectDataKeys(Kwf_Model_Select $select, array &$inData)
    {
        $dataKeys = array();
        foreach ($inData as $key=>$d) {
            if ($this->_matchSelect($d, $select)) {
                $dataKeys[] = $key;
            }
        }

        if ($order = $select->getPart(Kwf_Model_Select::ORDER)) {
            if (count($order) > 1) {
                //TODO: implement Multiple Order fields
            }
            $order = current($order);
            $orderData = array();
            foreach ($dataKeys as $key) {
                if ($order['field'] instanceof Zend_Db_Expr) {
                    //can't be done in FnF
                    $orderData[$inData[$key][$this->_primaryKey]] = 0;
                } else if ($order['field'] == Kwf_Model_Select::ORDER_RAND) {
                    $orderData[$inData[$key][$this->_primaryKey]] = rand();
                } else {
                   $orderData[$inData[$key][$this->_primaryKey]] = strtolower($this->_rowValue($order['field'], $inData[$key]));
                }
            }
            if ($order['direction'] == 'ASC') {
                asort($orderData);
            } else if ($order['direction'] == 'DESC') {
                arsort($orderData);
            } else {
                throw new Kwf_Exception("Invalid order direction: {$order['direction']}");
            }
            $sortedDataKeys = array();
            foreach (array_keys($orderData) as $id) {
                foreach ($dataKeys as $key) {
                    if ($inData[$key][$this->_primaryKey] == $id) {
                        $sortedDataKeys[] = $key;
                    }
                }
            }
            $dataKeys = $sortedDataKeys;
        }

        if ($select->hasPart(Kwf_Model_Select::LIMIT_OFFSET)) {
            $limitOffset = $select->getPart(Kwf_Model_Select::LIMIT_OFFSET);
            $dataKeys = array_slice($dataKeys, $limitOffset);
        }
        if ($select->hasPart(Kwf_Model_Select::LIMIT_COUNT)) {
            $limitCount = $select->getPart(Kwf_Model_Select::LIMIT_COUNT);
            $dataKeys = array_slice($dataKeys, 0, $limitCount);
        }

        if ($select->hasPart(Kwf_Model_Select::UNION)) {
            foreach ($select->getPart(Kwf_Model_Select::UNION) as $unionSel) {
                $dataKeys = array_merge($dataKeys, $this->_selectDataKeys($unionSel, $inData));
            }
        }
        return $dataKeys;
    }

    private function _matchSelect($data, $select)
    {
        foreach ($data as &$d) {
            if (!is_object($d) && !is_null($d)) $d = (string)$d;
        }
        if ($id = $select->getPart(Kwf_Model_Select::WHERE_ID)) {
            if ($data[$this->getPrimaryKey()] != (string)$id) return false;
        }
        if ($where = $select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
            foreach ($where as $f=>$v) {
                if (!is_array($v)) $v = array($v);
                foreach ($v as &$i) $i = (string)$i;
                $rv = $this->_rowValue($f, $data);
                if (is_null($rv)) return false;
                if (!in_array($rv, $v)) return false;
            }
        }
        if ($where = $select->getPart(Kwf_Model_Select::WHERE_EXPRESSION)) {
            foreach ($where as $expr) {
                if (!$this->_checkExpressions($expr, $data)) return false;
            }
        }
        if ($where = $select->getPart(Kwf_Model_Select::WHERE_NOT_EQUALS)) {
            $foundOneMatching = false;
            foreach ($where as $f=>$v) {
                if (!is_array($v)) $v = array($v);
                foreach ($v as &$i) $i = (string)$i;
                $rv = $this->_rowValue($f, $data);
                if ($rv && in_array($rv, $v)) {
                    $foundOneMatching = true;
                    break;
                }
            }
            if ($foundOneMatching) return false;
        }
        if ($where = $select->getPart(Kwf_Model_Select::WHERE_NULL)) {
            foreach ($where as $f) {
                $rv = $this->_rowValue($f, $data);
                if (!is_null($rv)) return false;
            }
        }
        return true;
    }

    private function _rowValue($field, $rowData)
    {
        $ret = null;
        if (isset($this->_exprs[$field])) {
            $ret = $this->getExprValue($rowData, $field);
        } else if (isset($rowData[$field])) {
            $ret = $rowData[$field];
        } else {
            foreach ($this->getSiblingModels() as $m) {
                if ($m->hasColumn($field)) {
                    $row = $m->getRow($rowData[$this->getPrimaryKey()]);
                    if ($row) $ret = $row->$field;
                    break;
                }
            }
        }
        return $ret;
    }

    private function _checkExpressions(Kwf_Model_Select_Expr_Interface $expr, $data)
    {
        if ($expr instanceof Kwf_Model_Select_Expr_Equal) {
            $v = $this->_rowValue($expr->getField(), $data);
            $values = $expr->getValue();
            if ($values instanceof Kwf_Model_Select_Expr_Interface) {
                $values = $this->getExprValue($data, $values);
            }
            if (!is_array($values)) $values = array($values);
            if (!in_array($v, $values)) {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_NotEquals) {
            $v = $this->_rowValue($expr->getField(), $data);
            $values = $expr->getValue();
            if (!is_array($values)) $values = array($values);
            if (in_array($v, $values)) {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_IsNull) {
            $v = $this->_rowValue($expr->getField(), $data);
            if (!is_null($v)) {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Higher) {
            $v = $this->_rowValue($expr->getField(), $data);
            $exprValue = $expr->getValue();
            if ($exprValue instanceof Kwf_Date) {
                $exprValue = $exprValue->getTimestamp();
                $v = strtotime($v);
            }
            if (!(!is_null($v) && $v > $exprValue)) {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Lower) {
            $v = $this->_rowValue($expr->getField(), $data);
            $exprValue = $expr->getValue();
            if ($exprValue instanceof Kwf_Date) {
                $exprValue = $exprValue->getTimestamp();
                $v = strtotime($v);
            }
            if (!(!is_null($v) && $v < $exprValue)) {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_HigherEqual) {
            $v = $this->_rowValue($expr->getField(), $data);
            $exprValue = $expr->getValue();
            if ($exprValue instanceof Kwf_Date) {
                $exprValue = $exprValue->getTimestamp();
                $v = strtotime($v);
            }
            if (!(!is_null($v) && $v >= $exprValue)) {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_LowerEqual) {
            $v = $this->_rowValue($expr->getField(), $data);
            $exprValue = $expr->getValue();
            if ($exprValue instanceof Kwf_Date) {
                $exprValue = $exprValue->getTimestamp();
                $v = strtotime($v);
            }
            if (!(!is_null($v) && $v <= $exprValue)) {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_HigherEqualDate) {
            $v = $this->_rowValue($expr->getField(), $data);
            if (!is_null($v)) {
                $fieldTime = strtotime($v);
                $exprTime = strtotime($expr->getValue());
                if ($fieldTime >= $exprTime) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_SmallerEqualDate) {
            $v = $this->_rowValue($expr->getField(), $data);
            if (!is_null($v)) {
                $fieldTime = strtotime($v);
                $exprTime = strtotime($expr->getValue());
                if ($fieldTime <= $exprTime) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Contains) {
            $v = $this->_rowValue($expr->getField(), $data);
            if (!($v && strpos(strtolower($v), strtolower($expr->getValue())) !== false )) {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Like) {
            $v = $this->_rowValue($expr->getField(), $data);
            if (!is_null($v)) {
                $reg = $expr->getValue();
                $partsToEscape = array('\\', '(', ')', '_', '*', '.', '^', '$');
                foreach ($partsToEscape as $part) {
                    $reg = str_replace($part, '\\' . $part, $reg);
                }
                $reg = str_replace('%', '(.*)', $reg);
                $reg = "/^$reg$/i";
                return preg_match($reg, $v);
            }
            return false;
        } else if ($expr instanceof Kwf_Model_Select_Expr_RegExp) {
            $v = $this->_rowValue($expr->getField(), $data);
            if (!is_null($v)) {
                $reg = $expr->getValue();
                return preg_match('/'.$reg.'/', $v);
            }
            return false;
        } else if ($expr instanceof Kwf_Model_Select_Expr_StartsWith) {
            $v = $this->_rowValue($expr->getField(), $data);
            if (!($v && substr($v, 0, strlen($expr->getValue()))==$expr->getValue())) {
                return false;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Area) {
            // TODO: Umkreissuche
            throw new Kwf_Exception_NotYetImplemented();
        } else if ($expr instanceof Kwf_Model_Select_Expr_Not) {
                if ($this->_checkExpressions($expr->getExpression(), $data)) {
                    return false;
                }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Or) {
            foreach ($expr->getExpressions() as $orExpr) {
                if ($this->_checkExpressions($orExpr, $data)) {
                    return true;
                }
            }
            return false;
        } else if ($expr instanceof Kwf_Model_Select_Expr_And) {
            foreach ($expr->getExpressions() as $andExpr) {
                if (!$this->_checkExpressions($andExpr, $data)) {
                    return false;
                }
            }
            return true;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Add) {
            $ret = 0;
            foreach ($expr->getExpressions() as $andExpr) {
                $ret += $this->_checkExpressions($andExpr, $data);
            }
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Subtract) {
            $ret = 0;
            foreach ($expr->getExpressions() as $andExpr) {
                $ret -= $this->_checkExpressions($andExpr, $data);
            }
            if ($expr->lowerNullAllowed && $ret < 0) $ret = 0;
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Divide) {
            foreach ($expr->getExpressions() as $e) {
                $value = $this->_checkExpressions($e, $data);
                if ($ret == null) {
                    $ret = $value;
                } else {
                    if ($value == 0) {
                        //throw new Kwf_Exception('division by 0 not possible, check you expressions');
                    } else {
                        $ret = $ret / $value;
                    }
                }
            }
            if (!$ret) $ret = 0;
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Multiply) {
            $ret = null;
            foreach ($expr->getExpressions() as $e) {
                $value = $this->getExprValue($row, $e);
                if ($ret == null) {
                    $ret = $value;
                } else {
                    $ret *= $value;
                }
            }
            if (!$ret) $ret = 0;
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_SearchLike) {
            $e = $expr->getQueryExpr($this);
            if (!$e) return true;
            return $this->_checkExpressions($e, $data);
        } else {
            return (bool)$this->getExprValue($data, $expr);
        }
        return true;
    }
    public function getPrimaryKey()
    {
        return $this->_primaryKey;
    }

    protected function _getOwnColumns()
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

    public function getUniqueIdentifier()
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    private function _updateModelObserver($options)
    {
        if (isset($options['skipModelObserver']) && $options['skipModelObserver']) return;

        if (Kwf_Component_Data_Root::getComponentClass()) {
            if ($this->_proxyContainerModels) {
                foreach ($this->_proxyContainerModels as $m) {
                    Kwf_Component_ModelObserver::getInstance()->add('update', $m);
                }
            } else {
                Kwf_Component_ModelObserver::getInstance()->add('update', $this);
            }
        }
    }

    public function import($format, $data, $options = array())
    {
        if ($format == self::FORMAT_ARRAY) {
            if (isset($options['replace']) && $options['replace'] && !isset($this->_uniqueColumns)) {
                throw new Kwf_Exception('You must set uniqueColumns for this model if you use replace');
            }
            Kwf_Component_ModelObserver::getInstance()->disable();
            foreach ($data as $k => $v) {
                if (isset($options['replace']) && $options['replace']) {
                    $s = $this->select();
                    foreach ($this->_uniqueColumns as $c) {
                        if (is_null($v[$c])) {
                            $s->whereNull($c);
                        } else {
                            $s->whereEquals($c, $v[$c]);
                        }
                    }
                    $row = $this->getRow($s);
                    if (!$row) {
                        $row = $this->createRow();
                    }
                } else {
                    $row = $this->createRow();
                }
                foreach ($v as $k=>$i) {
                    $row->$k = $i;
                }
                $row->save();
            }
            Kwf_Component_ModelObserver::getInstance()->enable();
            $this->_updateModelObserver($options);
            $this->_afterImport($format, $data, $options);
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
    }

    protected function _dataModified()
    {
    }
}
