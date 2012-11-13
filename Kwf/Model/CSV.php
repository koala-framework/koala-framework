<?php
class Kwf_Model_CSV extends Kwf_Model_Abstract
{
    protected $_rowClass = 'Kwf_Model_CSV_Row';
    protected $_data = array();

    protected $_filename;
    protected $_delimiter;
    protected $_headRow = false;
    public function __construct(array $config = array())
    {
        if (!isset($config['filename'])) throw new Kwf_Exception('No CSV file found');
        if (isset($config['headRow'])) $this->_headRow = $config['headRow'];
        if (!isset($config['delimiter'])) throw new Kwf_Exception('Please set a delimiter');
        $this->_filename = $config['filename'];
        $this->_delimiter = $config['delimiter'];
        parent::__construct($config);
    }

    protected function _getColumnsCharacter()
    {
        $columns = array();
        for($i=65; $i<91; $i++) {
            $columns[] = chr($i);
            if ($i == 90) {
                for ($j=65; $j<91; $j++) {
                    for($k=65; $k<91; $k++) {
                        $columns[] = chr($j).chr($k);
                    }
                }
            }
        }
        return $columns;
    }

    protected function _getIndexFromColumn($column) {
        $columns = $this->_getColumnsCharacter();
        foreach ($columns as $key => $value) {
            if ($value == $column) {
                return $key;
            }
        }
    }

    public function getHeadRow()
    {
        $headRow = array();
        $columns = $this->_getColumnsCharacter();
        if (($handle = fopen($this->_filename, "r")) !== FALSE) {
            while (($rows = fgetcsv($handle, 0, $this->_delimiter)) !== FALSE) {
                $num = count($rows);
                for ($c=0; $c < $num; $c++) {
                    if ($this->_headRow) {
                        $headRow[] = array($columns[$c], $rows[$c]);
                    } else {
                        $headRow[] = array($columns[$c], $columns[$c]);
                    }
                }
                break;
            }
        }
        return $headRow;
    }

    public function export($format, $select = array(), $options = array())
    {
        if (!is_object($select)) {
            if (is_string($select)) $select = array($select);
            $select = $this->select($select);
        }
        $data = array();

        $i = 0;
        $columns = $this->_getColumnsCharacter();
        if (($handle = fopen($this->_filename, "r")) !== FALSE) {
            while (($rows = fgetcsv($handle, 0, $this->_delimiter)) !== FALSE) {
                $num = count($rows);
                $array = array();
                if ($this->_headRow && $i == 0){ $i++; continue; }
                for ($c=0; $c < $num; $c++) {
                    $array[$columns[$c]] = $rows[$c];
                }
                if (!$this->_selectData($select, $array)){ $i++; continue; }
                $data[$i]['id'] = $i;
                if ($options['columns']) {
                    foreach($options['columns'] as $column) {
                        $data[$i][$column] = $array[$column];
                    }
                }
                $i++;
            }
        }

        if ($order = $select->getPart(Kwf_Model_Select::ORDER)) {
            $sortParams = array();
            $sortArray = array();
            $direction = array();
            $i = 0;
            foreach ($order as $o) {
                foreach ($data as $key => $value) {
                    $sortArray[$i][$key] = $value[$o['field']];
                }
                if ($o['direction'] == 'ASC') {
                    $direction[$i] = SORT_ASC;
                } else {
                    $direction[$i] = SORT_DESC;
                }
                $sortParams[] = &$sortArray[$i];
                $sortParams[] = &$direction[$i];
                $i++;
            }
            $sortParams[] = &$data;
            call_user_func_array('array_multisort', $sortParams);
        }
        if ($select->hasPart(Kwf_Model_Select::LIMIT_OFFSET)) {
            $limitOffset = $select->getPart(Kwf_Model_Select::LIMIT_OFFSET);
            $data = array_slice($data, $limitOffset);
        }
        if ($select->hasPart(Kwf_Model_Select::LIMIT_COUNT)) {
            $limitCount = $select->getPart(Kwf_Model_Select::LIMIT_COUNT);
            $data = array_slice($data, 0, $limitCount);
        }


        if ($format == self::FORMAT_ARRAY) {
            return $data;
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
    }

    public function getRows($where = null, $order = null, $limit = null, $start = null)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }

        $data = array();

        $i = 0;
        $columns = $this->_getColumnsCharacter();
        if (($handle = fopen($this->_filename, "r")) !== FALSE) {
            while (($rows = fgetcsv($handle, 0, $this->_delimiter)) !== FALSE) {
                $num = count($rows);
                if ($this->_headRow && $i == 0){ $i++; continue; }
                for ($c=0; $c < $num; $c++) {
                    $data[$i][$columns[$c]] = $rows[$c];
                }
                if (!$this->_selectData($select, $data[$i])) unset($data[$i]);
                $i++;
            }
        }

        if ($order = $select->getPart(Kwf_Model_Select::ORDER)) {
            $sortParams = array();
            $sortArray = array();
            $direction = array();
            $i = 0;
            foreach ($order as $o) {
                foreach ($data as $key => $value) {
                    $sortArray[$i][$key] = $value[$o['field']];
                }
                if ($o['direction'] == 'ASC') {
                    $direction[$i] = SORT_ASC;
                } else {
                    $direction[$i] = SORT_DESC;
                }
                $sortParams[] = &$sortArray[$i];
                $sortParams[] = &$direction[$i];
                $i++;
            }
            $sortParams[] = &$data;
            call_user_func_array('array_multisort', $sortParams);
        }
        if ($select->hasPart(Kwf_Model_Select::LIMIT_OFFSET)) {
            $limitOffset = $select->getPart(Kwf_Model_Select::LIMIT_OFFSET);
            $data = array_slice($data, $limitOffset);
        }
        if ($select->hasPart(Kwf_Model_Select::LIMIT_COUNT)) {
            $limitCount = $select->getPart(Kwf_Model_Select::LIMIT_COUNT);
            $data = array_slice($data, 0, $limitCount);
        }

        $this->_data = $data;
        unset($data);
        $keys = array_keys($this->_data);
        $ret =  new $this->_rowsetClass(array(
            'dataKeys' => $keys,
            'model' => $this
        ));
        return $ret;
    }

    protected function _selectData(Kwf_Model_Select $select, array $row)
    {
        if ($where = $select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
            foreach ($where as $f=>$v) {
                if (!is_array($v)) $v = array($v);
                $rv = $this->_rowValue($f, $row);
                if (is_null($rv)) return false;
                if (!in_array($rv, $v)) return false;
            }
        }
        if ($where = $select->getPart(Kwf_Model_Select::WHERE_EXPRESSION)) {
            foreach ($where as $expr) {
                if (!$this->_checkExpressions($expr, $row)) return false;
            }
        }
        if ($where = $select->getPart(Kwf_Model_Select::WHERE_NOT_EQUALS)) {
            $foundOneMatching = false;
            foreach ($where as $f=>$v) {
                if (!is_array($v)) $v = array($v);
                $rv = $this->_rowValue($f, $row);
                if ($rv && in_array($rv, $v)) {
                    $foundOneMatching = true;
                    break;
                }
            }
            if ($foundOneMatching) return false;
        }
        if ($where = $select->getPart(Kwf_Model_Select::WHERE_NULL)) {
            foreach ($where as $f) {
                $rv = $this->_rowValue($f, $row);
                if (!is_null($rv)) return false;
            }
        }
        return true;
    }

    private function _rowValue($field, $rowData)
    {
        $ret = $rowData[$field];
        return $ret;
    }

    /* TODO: Copy from Kwf_Model_Data_Abstract */
    private function _checkExpressions(Kwf_Model_Select_Expr_Interface $expr, $data)
    {
        if ($expr instanceof Kwf_Model_Select_Expr_Equal) {
            $v = $this->_rowValue($expr->getField(), $data);
            $values = $expr->getValue();
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
        } else {
            return (bool)$this->getExprValue($data, $expr);
        }
        return true;
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

    protected function _getOwnColumns()
    {
        return array();
    }


    public function getPrimaryKey()
    {

    }
}
