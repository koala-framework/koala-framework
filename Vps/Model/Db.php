<?php
class Vps_Model_Db extends Vps_Model_Abstract
{
    protected $_rowClass = 'Vps_Model_Db_Row';
    protected $_rowsetClass = 'Vps_Model_Db_Rowset';
    protected $_table;

    public function __construct($config = array())
    {
        if (isset($config['tableName'])) {
            $this->_table = new $config['tableName']();
        }
        if (isset($config['table'])) {
            $this->_table = $config['table'];
        }
        parent::__construct($config);
    }

    protected function _init()
    {
        parent::_init();
        if (is_string($this->_table)) {
            $this->_table = new Vps_Db_Table(array(
                'name' => $this->_table
            ));
        }
        if (!$this->_table) {
            if (isset($this->_name)) {
                throw new Vps_Exception("You must rename _name to _table in '".get_class($this)."'");
            }
            throw new Vps_Exception("No table set");
        }
        if (!$this->_table instanceof Zend_Db_Table_Abstract) {
            throw new Vps_Exception("'".get_class($this->_table)."' is not a Zend_Db_Table");
        }
    }

    public function getOwnColumns()
    {
        return $this->_table->info(Zend_Db_Table_Abstract::COLS);
    }

    public function createRow(array $data=array())
    {
        $data = array_merge($this->_default, $data);
        return new $this->_rowClass(array(
            'row' => $this->_table->createRow($data),
            'model' => $this
        ));
    }

    public function afterInsert($row)
    {
        $id = $this->_getUniqueId($row);
        $this->_rows[$id] = $row;
    }

    protected function _getUniqueId($row)
    {
        $keys = $this->getPrimaryKey();
        if (!is_array($keys)) $keys = array($keys);
        $ids = array();
        foreach ($keys as $key) {
            $ids[] = $row->$key;
        }
        return implode('_', $ids);
    }

    public function getRowByProxiedRow($proxiedRow)
    {
        $id = $this->_getUniqueId($proxiedRow);
        if (!isset($this->_rows[$id])) {
            $this->_rows[$id] = new $this->_rowClass(array(
                'row' => $proxiedRow,
                'model' => $this
            ));
        }
        return $this->_rows[$id];
    }

    private function _formatField($field, $select)
    {
        if (in_array($field, $this->getOwnColumns())) {
            return $this->_table->info('name').'.'.$field;
        }
        $ret = $this->_formatFieldInternal($field, $select);
        if (!$ret) {
            throw new Vps_Exception("Can't find field '$field'");
        }

        return $ret;
    }
    private function _formatFieldInternal($field, $dbSelect)
    {
        foreach ($this->_siblingModels as $k=>$m) {
            if ($m instanceof Vps_Model_Proxy) {
                $m = $m->getProxyModel();
            }
            if ($m instanceof Vps_Model_Db) {
                if (in_array($field, $m->getOwnColumns())) {
                    $ref = $m->getReferenceByModelClass(get_class($this), $k);
                    $siblingTableName = $m->_table->info(Zend_Db_Table_Abstract::NAME);
                    $dbSelect->joinLeft($siblingTableName,
                            $this->_table->info('name').'.'.$this->getPrimaryKey()
                            .' = '.$siblingTableName.'.'.$ref['column'], array());
                    return $m->_table->info('name').'.'.$field;
                }
                $ret = $m->_formatFieldInternal($field, $dbSelect);
                if ($ret) return $ret;
            }
        }
        return false;
    }

    public function createDbSelect($select)
    {
        if (!$select) return null;
        $tablename = $this->_table->info('name');
        $dbSelect = $this->_table->select();
        $dbSelect->from($tablename);
        if ($whereEquals = $select->getPart(Vps_Model_Select::WHERE_EQUALS)) {
            foreach ($whereEquals as $field=>$value) {
                if (is_array($value)) {
                    foreach ($value as &$v) {
                        $v = $this->getAdapter()->quote($v);
                    }
                    $value = implode(', ', $value);
                    $dbSelect->where($this->_formatField($field, $select)." IN ($value)");
                } else {
                    $dbSelect->where($this->_formatField($field, $select)." = ?", $value);
                }
            }
        }
        if ($whereNotEquals = $select->getPart(Vps_Model_Select::WHERE_NOT_EQUALS)) {
            foreach ($whereNotEquals as $field=>$value) {
                if (is_array($value)) {
                    foreach ($value as &$v) {
                        $v = $this->getAdapter()->quote($v);
                    }
                    $value = implode(', ', $value);
                    $dbSelect->where($this->_formatField($field, $select)." NOT IN ($value)");
                } else {
                    $dbSelect->where($this->_formatField($field, $select)." != ?", $value);
                }
            }
        }
        if ($where = $select->getPart(Vps_Model_Select::WHERE)) {
            foreach ($where as $w) {
                $dbSelect->where($w[0], $w[1], $w[2]);
            }
        }

        if ($whereId = $select->getPart(Vps_Model_Select::WHERE_ID)) {
            $dbSelect->where($this->_formatField($this->getPrimaryKey(), $select)." = ?", $whereId);
        }

        if ($whereNull = $select->getPart(Vps_Model_Select::WHERE_NULL)) {
            foreach ($whereNull as $field) {
                $dbSelect->where("ISNULL(".$this->_formatField($field, $select).")");
            }
        }

        if ($other = $select->getPart(Vps_Model_Select::OTHER)) {
            foreach ($other as $i) {
                call_user_func_array(array($dbSelect, $i['method']), $i['arguments']);
            }
        }
        if ($whereExpression = $select->getPart(Vps_Model_Select::WHERE_EXPRESSION)) {
            foreach ($whereExpression as $expr) {
                $expr->validate();
                $dbSelect->where($this->_createDbSelectExpression($expr));
            }
        }
        return $dbSelect;
    }

    private function _createDbSelectExpression($expr)
    {
        if ($expr instanceof Vps_Model_Select_Expr_CompareField_Abstract) {

            $quotedValue = $expr->getValue();
            $quotedValue = $this->_table->getAdapter()->quote($quotedValue);

        }
        if ($expr instanceof Vps_Model_Select_Expr_Equals) {
            return $expr->getField()." = ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_Smaller
                || $expr instanceof Vps_Model_Select_Expr_SmallerDate) {
            return $expr->getField()." < ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_Higher
                || $expr instanceof Vps_Model_Select_Expr_HigherDate) {
            return $expr->getField()." > ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_Contains) {
            $v = $expr->getValue();
            $quotedValueContains = $this->_table->getAdapter()->quote('%'.$v.'%');

            $quotedValue = str_replace("%", "\\%", $quotedValue);
            $quotedValue = str_replace("_", "\\_", $quotedValue);
            $quotedValue = str_replace(
                            substr($quotedValueContains, 2, strlen($quotedValueContains)-4),
                            substr($quotedValue, 1, strlen($quotedValue)-2),
                            $quotedValueContains);
            return $expr->getField()." LIKE ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_StartsWith) {
            return "LEFT({$expr->getField()}, ".strlen($expr->getValue()).") = ".$quotedValue;
        } else if ($expr instanceof Vps_Model_Select_Expr_NOT) {
            return "NOT (".$this->_createDbSelectExpression($expr->getExpression()).")";
        } else if ($expr instanceof Vps_Model_Select_Expr_Or) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression).")";
            }
            return implode(" OR ", $sqlExpressions);
        } else if ($expr instanceof Vps_Model_Select_Expr_And) {
            $sqlExpressions = array();
            foreach ($expr->getExpressions() as $expression) {
                $sqlExpressions[] = "(".$this->_createDbSelectExpression($expression).")";
            }
            return implode(" AND ", $sqlExpressions);
        }
    }

    //Nur zum Debuggen verwenden!
    public function getSqlForSelect($select)
    {
        //TODO: limit und order fehlen :D
        $dbSelect = $this->createDbSelect($select);
        return $dbSelect->__toString();
    }

    public function find($id)
    {
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->find($id),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function getRows($where = array(), $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $dbSelect = $this->createDbSelect($select);
        if ($order = $select->getPart(Vps_Model_Select::ORDER)) {
            foreach ($order as $o) {
                if ($o['field'] instanceof Zend_Db_Expr) {
                    $dbSelect->order($o['field']);
                } else if ($o['field'] == Vps_Model_Select::ORDER_RAND) {
                    $dbSelect->order('RAND()');
                } else {
                    if (strpos($o['field'], '.') === false &&
                        strpos($o['field'], '(') === false
                    ) {
                        $o['field'] = $this->_formatField($o['field'], $select);
                    }
                    $dbSelect->order($o['field'].' '.$o['direction']);
                }
            }
        }
        $limitCount = $select->getPart(Vps_Model_Select::LIMIT_COUNT);
        $limitOffset = $select->getPart(Vps_Model_Select::LIMIT_OFFSET);
        if ($limitCount || $limitOffset) {
            $dbSelect->limit($limitCount, $limitOffset);
        }
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->fetchAll($dbSelect),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function countRows($select = array())
    {

        if (!is_object($select)) {
            $select = $this->select($select);
        }
        $dbSelect = $this->createDbSelect($select);
        $dbSelect->reset(Zend_Db_Select::COLUMNS);
        $dbSelect->setIntegrityCheck(false);
        if ($dbSelect->getPart('group')) {
            $group = current($dbSelect->getPart('group'));
            $dbSelect->reset(Zend_Db_Select::GROUP);
            $dbSelect->from(null, "COUNT(DISTINCT $group) c");
        } else {
            $dbSelect->from(null, 'COUNT(*) c');
        }
        return $this->_table->getAdapter()->query($dbSelect)->fetchColumn();
    }

    public function getPrimaryKey()
    {
        $ret = $this->_table->info('primary');
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

    public function select($where = array(), $order = null, $limit = null, $start = null)
    {
        if (!is_array($where)) {
            $ret = new Vps_Model_Select();
            if ($where) {
                $ret->whereEquals($this->getPrimaryKey(), $where);
            }
        } else {
            $ret = new Vps_Model_Select($where);
        }
        if ($order) $ret->order($order);
        if ($limit || $start) $ret->limit($limit, $start);
        return $ret;
    }

    public function getUniqueIdentifier() {
        return $this->_table->info(Zend_Db_Table_Abstract::NAME);
    }
}
