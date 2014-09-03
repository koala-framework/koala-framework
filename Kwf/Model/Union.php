<?php
class Kwf_Model_Union extends Kwf_Model_Abstract
{
    protected $_models;
    protected $_columnMapping;
    protected $_rowClass = 'Kwf_Model_Union_Row';
    protected $_rowsetClass = 'Kwf_Model_Union_Rowset';
    protected $_sourceRows = array();
    protected $_allDb = false;
    
    public function __construct(array $config = array())
    {
        if (isset($config['models'])) $this->_models = (array)$config['models'];
        if (isset($config['columnMapping'])) $this->_columnMapping = $config['columnMapping'];
        if (!$this->_models) throw new Kwf_Exception("models setting is required");
        if (!$this->_columnMapping) throw new Kwf_Exception("columnMapping setting is required");

        $allDb = true;
        foreach ($this->_models as $k=>$i) {
            if (is_numeric(substr($k, -1))) {
                if (!$this->_columnMapping) throw new Kwf_Exception("model key '$k' must not end numeric");
            }
            foreach (array_keys($this->_models) as $key) {
                if ($key != $k && substr($k, 0, strlen($key)) == $key) {
                    throw new Kwf_Exception("Invalid key '$k', another key ('$key') also starts with this string");
                }
            }
            $i = Kwf_Model_Abstract::getInstance($i);
            $this->_models[$k] = $i;
            if (!$i->getFactoryConfig()) {
                $i->setFactoryConfig(array(
                    'type' => 'UnionSource',
                    'union' => $this,
                    'modelKey' => $k,
                ));
            }
            while ($i instanceof Kwf_Model_Proxy) $i = $i->getProxyModel();
            if (!$i instanceof Kwf_Model_Db) {
                $allDb = false;
            }
        }
        $this->_allDb = $allDb;
        parent::__construct($config);
    }

    public function getUnionColumnMapping()
    {
        return $this->_columnMapping;
    }

    public function getUnionModels()
    {
        return $this->_models;
    }

    private function _convertExpr($expr, $targetModel)
    {
        if ($expr instanceof Kwf_Model_Select_Expr_CompareField_Abstract) {
            $f = $targetModel->getColumnMapping($this->_columnMapping, $expr->getField());
            $cls = get_class($expr);
            return new $cls($f, $expr->getValue());
        } else if ($expr instanceof Kwf_Model_Select_Expr_Not) {
            return new Kwf_Model_Select_Expr_Not($this->_convertExpr($expr->getExpression(), $targetModel));
        } else if ($expr instanceof Kwf_Model_Select_Expr_Unary_Abstract) {
            $exprs = array();
            foreach ($expr->getExpressions() as $i) {
                $exprs[] = $this->_convertExpr($i, $targetModel);
            }
            $cls = get_class($expr);
            return new $cls($exprs);
        } else {
            throw new Kwf_Exception_NotImplemented();
        }
    }

    private function _mapColumn($model, $column)
    {
        if ($column == 'id') return $model->getPrimaryKey();
        return $model->getColumnMapping($this->_columnMapping, $column);
    }

    private function _convertSelect($select, $modelKey, $m)
    {
        $s = new Kwf_Model_Select();
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_ID)) {
            if (substr($p, 0, strlen($modelKey)) == $modelKey) {
                $s->whereId(substr($p, strlen($modelKey)));
            } else {
                return null;
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
            foreach ($p as $f=>$v) {
                if ($f == 'id') $v = substr($v, strlen($modelKey));
                $f = $this->_mapColumn($m, $f);
                $s->whereEquals($f, $v);
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_NOT_EQUALS)) {
            foreach ($p as $f=>$v) {
                if ($f == 'id') $v = substr($v, strlen($modelKey));
                $f = $this->_mapColumn($m, $f);
                $s->whereNotEquals($f, $v);
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_NULL)) {
            foreach ($p as $f) {
                $f = $this->_mapColumn($m, $f);
                $s->whereNull($f);
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_EXPRESSION)) {
            foreach ($p as $expr) {
                $s->where($this->_convertExpr($expr, $m));
            }
        }
        $select->reset(Kwf_Model_Select::ORDER);
        $select->reset(Kwf_Model_Select::LIMIT_COUNT);
        $select->reset(Kwf_Model_Select::LIMIT_OFFSET);
        return $s;
    }

    public function countRows($select = array())
    {
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        $ret = 0;
        foreach ($this->_models as $modelKey => $m) {
            $s = $this->_convertSelect($select, $modelKey, $m);
            if (!$s) continue;
            $ret += $m->countRows($s);
        }
        return $ret;
    }

    protected function _getOwnColumns()
    {
        $vars = get_class_vars($this->_columnMapping);
        $ret = $vars['columns'];
        $ret[] = 'id';
        return $ret;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getRows($where = null, $order = null, $limit = null, $start = null)
    {
        if (!is_object($where) || $where instanceof Kwf_Model_Select_Expr_Interface) {
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        if ($this->_allDb) {
            $dbSelects = array();
            $order = null;
            foreach ($this->_models as $modelKey => $m) {
                $s = $this->_convertSelect($select, $modelKey, $m);
                if (!$s) continue;
                $options = array(
                    'columns' => array($m->getPrimaryKey())
                );
                while ($m instanceof Kwf_Model_Proxy) $m = $m->getProxyModel();
                $dbSelect = $m->_createDbSelectWithColumns($s, $options);
                $columns = $dbSelect->getPart(Zend_Db_Select::COLUMNS);
                $columns[1][2] = 'id';
                $columns[] = array(
                    '', new Zend_Db_Expr("'$modelKey'"), 'modelKey'
                );
                if ($p = $select->getPart(Kwf_Model_Select::ORDER)) {
                    if (count($p) != 1) throw new Kwf_Exception_NotYetImplemented();
                    foreach ($p as $v) {
                        $v['field'] = $this->_mapColumn($m, $v['field']);
                        $columns[] = array(
                            '', $m->_formatField($v['field'], $dbSelect), 'orderField'
                        );
                    }
                }
                $dbSelect->setPart(Zend_Db_Select::COLUMNS, $columns);
                if (!isset($order)) $order = $dbSelect->getPart(Zend_Db_Select::ORDER);
                $dbSelect->reset(Zend_Db_Select::ORDER);
                $dbSelects[] = $dbSelect;
            }
            $sel = $m->getAdapter()->select();
            $sel->union($dbSelects);
            if ($p = $select->getPart(Kwf_Model_Select::ORDER)) {
                $sel->order('orderField '. $p[0]['direction']);
            }
            if ($limitCnt = $select->getPart(Kwf_Model_Select::LIMIT_COUNT)) {
                $limitOffs = $select->getPart(Kwf_Model_Select::LIMIT_OFFSET);
                $sel->limit($limitCnt, $limitOffs);
            }
            $rows = $m->getAdapter()->query($sel)->fetchAll();
            $ids = array();
            foreach ($rows as $row) {
                $ids[] = $row['modelKey'].$row['id'];
            }
        } else {
            $orderValues = array();
            foreach ($this->_models as $modelKey => $m) {
                $s = $this->_convertSelect($select, $modelKey, $m);
                if (!$s) continue;
                $rows = $m->getRows($s);
                $pk = $m->getPrimaryKey();
                foreach ($rows as $i) {
                    if (!isset($this->_sourceRows[$modelKey.$i->$pk])) {
                        $this->_sourceRows[$modelKey.$i->$pk] = $i;
                    }
                    $id = $modelKey.$i->$pk;
                    if ($p = $select->getPart(Kwf_Model_Select::ORDER)) {
                        if (count($p) != 1) throw new Kwf_Exception_NotYetImplemented();
                        $p[0]['field'] = $m->getColumnMapping($this->_columnMapping, $p[0]['field']);
                        $orderValues[$id] = $i->{$p[0]['field']};
                    } else {
                        $orderValues[$id] = true;
                    }
                }
            }
            if ($p = $select->getPart(Kwf_Model_Select::ORDER)) {
                if (count($p) != 1) throw new Kwf_Exception_NotYetImplemented();
                if ($p[0]['direction'] == 'DESC') {
                    arsort($orderValues);
                } else if ($p[0]['direction'] == 'ASC') {
                    asort($orderValues);
                } else {
                    throw new Kwf_Exception_NotYetImplemented();
                }
            }
            $ids = array_keys($orderValues);
            if ($limitCnt = $select->getPart(Kwf_Model_Select::LIMIT_COUNT)) {
                $limitOffs = (int)$select->getPart(Kwf_Model_Select::LIMIT_OFFSET);
                $ids = array_slice($ids, $limitOffs, $limitCnt);
            }
        }
        return new $this->_rowsetClass(array(
            'ids' => $ids,
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    public function getRowById($id)
    {
        if (isset($this->_rows[$id])) {
            return $this->_rows[$id];
        }
        if (!isset($this->_sourceRows[$id])) {
            foreach ($this->_models as $modelKey => $m) {
                if (substr($id, 0, strlen($modelKey)) == $modelKey) {
                    $this->_sourceRows[$id] = $m->getRow(substr($id, strlen($modelKey)));
                }
            }
        }
        $sRow = $this->_sourceRows[$id];
        return new $this->_rowClass(array(
            'id' => $id,
            'sourceRow' => $sRow,
            'model' => $this
        ));
    }

    public function createRow(array $data = array())
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    public function deleteRows($where)
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    public function updateRows($data, $where)
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    public function import($format, $data, $options = array())
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    public function getEventSubscribers()
    {
        $ret = array();
        foreach ($this->_models as $m) {
            $ret = array_merge($ret, $m->getEventSubscribers());
        }
        $ret[] = Kwf_Model_EventSubscriber::getInstance('Kwf_Model_Union_Events', array(
            'modelFactoryConfig' => $this->getFactoryConfig()
        ));
        return $ret;
    }
}
