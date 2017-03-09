<?php
class Kwf_Model_Union extends Kwf_Model_Abstract
{
    protected $_models;
    protected $_columnMapping;
    protected $_rowClass = 'Kwf_Model_Union_Row';
    protected $_rowsetClass = 'Kwf_Model_Union_Rowset';
    protected $_sourceRows = array();
    protected $_allDb = false;
    protected $_mergeSelects = array();
    
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
            if (is_array($i)) {
                if (!isset($i['model'])) throw new Kwf_Exception("model key for models setting is required");
                if (isset($i['select'])) {
                    $this->_mergeSelects[$k] = $i['select'];
                }
                $i = $i['model'];
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

    private function _convertExpr($expr, $modelKey, $targetModel)
    {
        if ($expr instanceof Kwf_Model_Select_Expr_CompareField_Abstract) {
            $f = $expr->getField();
            $v = $expr->getValue();
            if ($f == 'id') {
                if (substr($v, 0, strlen($modelKey)) == $modelKey) {
                    $v = substr($v, strlen($modelKey));
                } else {
                    return new Kwf_Model_Select_Expr_Boolean(false);
                }
                $mappedField = $targetModel->getPrimaryKey();
            } else {
                if (in_array($f, $this->_getOwnColumns())) {
                    $mappedField = $targetModel->getColumnMapping($this->_columnMapping, $f);
                    if (!$mappedField) {
                        if ($expr instanceof Kwf_Model_Select_Expr_Equal || $expr instanceof Kwf_Model_Select_Expr_Like) {
                            return new Kwf_Model_Select_Expr_Boolean(false);
                        } else {
                            throw new Kwf_Exception_NotImplemented();
                        }
                    }
                } else {
                    return null;
                }
            }
            $cls = get_class($expr);
            return new $cls($mappedField, $v);
        } else if ($expr instanceof Kwf_Model_Select_Expr_Not) {
            $e = $this->_convertExpr($expr->getExpression(), $modelKey, $targetModel);
            if (!$e) return null;
            return new Kwf_Model_Select_Expr_Not($e);
        } else if ($expr instanceof Kwf_Model_Select_Expr_Unary_Abstract) {
            $exprs = array();
            foreach ($expr->getExpressions() as $i) {
                $e = $this->_convertExpr($i, $modelKey, $targetModel);
                if (!$e) return null;
                $exprs[] = $e;
            }
            $cls = get_class($expr);
            return new $cls($exprs);
        } else if ($expr instanceof Kwf_Model_Select_Expr_String || $expr instanceof Kwf_Model_Select_Expr_Boolean || $expr instanceof Kwf_Model_Select_Expr_Integer) {
            return $expr;
        } else if ($expr instanceof Kwf_Model_Select_Expr_IsNull) {
            $f = $expr->getField();
            if (in_array($f, $this->_getOwnColumns())) {
                $mappedField = $targetModel->getColumnMapping($this->_columnMapping, $f);
                if (!$mappedField) {
                    return new Kwf_Model_Select_Expr_Boolean(true);
                } else {
                    $cls = get_class($expr);
                    return new $cls($mappedField);
                }
            } else {
                return null;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Sql) {
            return $expr;
        } else {
            throw new Kwf_Exception_NotYetImplemented();
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
                if ($f == 'id') {
                    if (is_array($v)) {
                        $filterV = array();
                        foreach ($v as $i) {
                            if (substr($i, 0, strlen($modelKey)) == $modelKey) {
                                $filterV[] = substr($i, strlen($modelKey));
                            }
                        }
                        if (!$filterV) return null;
                        $v = $filterV;
                    } else {
                        if (substr($v, 0, strlen($modelKey)) == $modelKey) {
                            $v = substr($v, strlen($modelKey));
                        } else {
                            return null;
                        }
                    }
                }
                if (in_array($f, $this->_getOwnColumns())) {
                    $f = $this->_mapColumn($m, $f);
                    $s->whereEquals($f, $v);
                }
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_NOT_EQUALS)) {
            foreach ($p as $f=>$v) {
                if ($f == 'id') {
                    if (substr($v, 0, strlen($modelKey)) != $modelKey) {
                        continue;
                    } else {
                        $v = substr($v, strlen($modelKey));
                    }
                }
                if (in_array($f, $this->_getOwnColumns())) {
                    $f = $this->_mapColumn($m, $f);
                    $s->whereNotEquals($f, $v);
                }
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_NULL)) {
            foreach ($p as $f) {
                if (in_array($f, $this->_getOwnColumns())) {
                    $f = $this->_mapColumn($m, $f);
                    $s->whereNull($f);
                }
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_EXPRESSION)) {
            foreach ($p as $expr) {
                $e = $this->_convertExpr($expr, $modelKey, $m);
                if ($e) {
                    $s->where($e);
                }
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::IGNORE_DELETED)) {
            $s->ignoreDeleted($p);
        }
        if (isset($this->_mergeSelects[$modelKey])) {
            $s->merge($this->_mergeSelects[$modelKey]);
        }
        return $s;
    }

    private function _convertExprForSibling($expr, $targetModel)
    {
        if ($expr instanceof Kwf_Model_Select_Expr_CompareField_Abstract) {
            $f = $expr->getField();
            if (!in_array($f, $this->_getOwnColumns()) && $targetModel->hasColumn($f)) {
                $cls = get_class($expr);
                return new $cls($f, $expr->getValue());
            } else {
                return null;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Not) {
            $e = $this->_convertExprForSibling($expr->getExpression(), $targetModel);
            if (!$e) return null;
            return new Kwf_Model_Select_Expr_Not($e);
        } else if ($expr instanceof Kwf_Model_Select_Expr_Unary_Abstract) {
            $exprs = array();
            foreach ($expr->getExpressions() as $i) {
                $e = $this->_convertExprForSibling($i, $targetModel);
                if (!$e) return null;
                $exprs[] = $e;
            }
            $cls = get_class($expr);
            return new $cls($exprs);
        } else if ($expr instanceof Kwf_Model_Select_Expr_String || $expr instanceof Kwf_Model_Select_Expr_Boolean || $expr instanceof Kwf_Model_Select_Expr_Integer) {
            return $expr;
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
    }

    private function _convertSelectForSibling($select, $m)
    {
        $s = new Kwf_Model_Select();
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
            foreach ($p as $f=>$v) {
                if (!in_array($f, $this->_getOwnColumns()) && $m->hasColumn($f)) {
                    $s->whereEquals($f, $v);
                }
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_NOT_EQUALS)) {
            foreach ($p as $f=>$v) {
                if (!in_array($f, $this->_getOwnColumns()) && $m->hasColumn($f)) {
                    $s->whereNotEquals($f, $v);
                }
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_NULL)) {
            foreach ($p as $f) {
                if (!in_array($f, $this->_getOwnColumns()) && $m->hasColumn($f)) {
                    $s->whereNull($f);
                }
            }
        }
        if ($p = $select->getPart(Kwf_Model_Select::WHERE_EXPRESSION)) {
            foreach ($p as $expr) {
                $e = $this->_convertExprForSibling($expr, $m);
                if ($e) {
                    $s->where($e);
                }
            }
        }
        return $s;
    }

    public function countRows($select = array())
    {
        if (!is_object($select)) {
            $select = $this->select($select);
        }

        $this->_selectIdsFromSiblingModels($select);

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

    private function _selectIdsFromSiblingModels(Kwf_Model_Select $select)
    {
        $filteredSiblingIds = null;
        foreach ($this->getSiblingModels() as $sm) {
            $s = $this->_convertSelectForSibling($select, $sm);
            if ($s->getParts() == array()) {
                //nothing to filter, skip
                continue;
            }
            $ids = $sm->getIds($s);
            if (is_null($filteredSiblingIds)) {
                $filteredSiblingIds = $ids;
            } else {
                $filteredSiblingIds = array_intersect($filteredSiblingIds, $ids);
            }
        }

        if (!is_null($filteredSiblingIds)) {
            $select->whereEquals('id', $filteredSiblingIds);
        }
    }

    public function getDbSelects($select, $colums = array('id'))
    {
        if (!$this->_allDb) {
            throw new Kwf_Exception("dbSelect can only be created if all models are db model");
        }

        $this->_selectIdsFromSiblingModels($select);

        $dbSelects = array();
        $order = null;
        foreach ($this->_models as $modelKey => $m) {
            $s = $this->_convertSelect($select, $modelKey, $m);
            if (!$s) continue;
            $options = array(
                'columns' => array()
            );
            foreach ($colums as $col) {
                if ($col == 'id') {
                    $col = $m->getPrimaryKey();
                } else {
                    $col = $m->getColumnMapping($this->_columnMapping, $col);
                }
                $options['columns'][] = $col;
            }
            while ($m instanceof Kwf_Model_Proxy) $m = $m->getProxyModel();
            $dbSelect = $m->_createDbSelectWithColumns($s, $options);
            $dbSelectColumns = array_values($dbSelect->getPart(Zend_Db_Select::COLUMNS));
            foreach ($colums as $kCol=>$col) {
                $dbSelectColumns[$kCol][2] = $col;
                if ($col == 'id') {
                    $dbSelectColumns[$kCol][1] = new Zend_Db_Expr("CONCAT('$modelKey', ".$dbSelectColumns[$kCol][1].")");
                }
            }
            if ($p = $select->getPart(Kwf_Model_Select::ORDER)) {
                if (count($p) != 1) throw new Kwf_Exception_NotYetImplemented();
                foreach ($p as $v) {
                    $v['field'] = $this->_mapColumn($m, $v['field']);
                    $expr = $m->_formatField($v['field'], $dbSelect);
                    $dbSelectColumns[] = array(
                        '', new Zend_Db_Expr($expr), 'orderField'
                    );
                }
            }
            $dbSelect->setPart(Zend_Db_Select::COLUMNS, $dbSelectColumns);
            if (!isset($order)) $order = $dbSelect->getPart(Zend_Db_Select::ORDER);
            $dbSelect->reset(Zend_Db_Select::ORDER);
            $dbSelects[] = $dbSelect;
        }
        if (!$dbSelects) {
            return array();
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
        return $sel;
    }

    public function getIds($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where) || $where instanceof Kwf_Model_Select_Expr_Interface) {
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        if ($this->_allDb) {

            $sel = $this->getDbSelects($select);

            $ids = array();
            if ($sel) {
                $rows = Kwf_Registry::get('db')->query($sel)->fetchAll();
                foreach ($rows as $row) {
                    $ids[] = $row['id'];
                }
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
            unset($m);

            foreach ($this->getSiblingModels() as $sm) {
                $s = $this->_convertSelectForSibling($select, $sm);
                if ($s->getParts() == array()) {
                    //nothing to filter, skip
                    continue;
                }
                $filteredOrderValues = array();
                $ids = $sm->getIds($s);
                foreach ($ids as $i) {
                    if (isset($orderValues[$i])) {
                        $filteredOrderValues[$i] = $orderValues[$i];
                    }
                }
                $orderValues = $filteredOrderValues;
                unset($filteredOrderValues);
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
        return $ids;
    }

    public function getRows($where = null, $order = null, $limit = null, $start = null)
    {
        $ids = $this->getIds($where, $order, $limit, $start);
        return new $this->_rowsetClass(array(
            'ids' => $ids,
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }

    /**
     * @internal
     */
    public function _getRowById($id)
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

    public function getRowBySourceRow(Kwf_Model_Row_Interface $row)
    {
        foreach ($this->getUnionModels() as $k=>$m) {
            if ($row->getModel() == $m) {
                return $this->getRow($k.$row->{$m->getPrimaryKey()});
            }
        }
        throw new Kwf_Exception("Model '".get_class($row->getModel())."' doesn't exist as unionModel in '".get_class($this)."'");
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
