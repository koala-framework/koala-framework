<?php
/**
 * @package Model
 */
class Kwf_Model_Mongo extends Kwf_Model_Abstract
{
    protected $_rowClass = 'Kwf_Model_Mongo_Row';
    protected $_data = array();

    /**
     * @var MongoCollection
     */
    protected $_collection;

    public function __construct(array $config = array())
    {
        if (!isset($config['db'])) {
            $config['db'] = Kwf_Registry::get('dao')->getMongoDb();
        }
        if (isset($config['collection'])) {
            $this->_collection = $config['collection'];
        }
        if (is_string($this->_collection)) {
            $this->_collection = $config['db']->{$this->_collection};
        }
        parent::__construct($config);
    }

    public function getCollection()
    {
        return $this->_collection;
    }

    protected function _init()
    {
    }

    public function dependentModelRowUpdated(Kwf_Model_Row_Abstract $row, $action)
    {
        parent::dependentModelRowUpdated($row, $action);
        $models = array($this);
        $models = array_merge($models, $this->_proxyContainerModels);
        foreach ($models as $model) {
            foreach ($model->_exprs as $column=>$expr) {
                if ($expr instanceof Kwf_Model_Select_Expr_Parent) {
                    if ($model->getReferencedModel($expr->getParent()) === $row->getModel()) {

                        //blöd dass diese schleife hier notwendig ist
                        //TODO: getDependentModels sollte was anderes zurückgeben
                        //gleiches problem wie bei getChildRows
                        foreach ($row->getModel()->getDependentModels() as $depName=>$m) {
                            if (!$m instanceof Kwf_Model_Abstract) $m = Kwf_Model_Abstract::getInstance($m);
                            if ($m === $model) {
                                $rows = $row->getChildRows($depName); //TODO effizienter machen, nicht über rows
                                foreach ($rows as $r) {
                                    $r->$column = $row->{$expr->getField()};
                                    $r->save();
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function childModelRowUpdated(Kwf_Model_Row_Abstract $row, $action)
    {
        parent::childModelRowUpdated($row, $action);
        $models = array($this);
        $models = array_merge($models, $this->_proxyContainerModels);
        foreach ($models as $model) {
            foreach ($model->_exprs as $column=>$expr) {
                if ($expr instanceof Kwf_Model_Select_Expr_Child) {
                    if ($model->getDependentModel($expr->getChild()) === $row->getModel()) {
                        $rule = $row->getModel()->getReferenceRuleByModelClass(get_class($model));
                        $parentRow = $row->getParentRow($rule);
                        $parentRow->$column = $model->getExprValue($parentRow, $expr);
                        $parentRow->save();
                    }
                }
            }
        }
    }

    public function update(Kwf_Model_Row_Interface $row, $rowData)
    {
        $ret = $this->_collection->update(
            array('_id' => $row->_id),
            $rowData,
            array('safe'=>true, 'multiple'=>false)
        );
        if (!$ret || $ret['ok'] != 1) {
            throw new Kwf_Exception("update failed");
        }
    }

    public function insert(Kwf_Model_Row_Interface $row, $rowData)
    {
        //TODO: id?
        $ret = $this->getCollection()->insert(
            $rowData
        , array('safe'=>true));
        if (!$ret || $ret['ok'] != 1) {
            throw new Kwf_Exception("insert failed");
        }
    }

    public function delete(Kwf_Model_Row_Interface $row)
    {
        $ret = $this->_collection->remove(
            array('_id' => $row->_id),
            array('safe'=>true, 'multiple'=>false)
        );
        if (!$ret || $ret['ok'] != 1) {
            throw new Kwf_Exception("delete failed");
        }
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

    public function getData()
    {
        return $this->_data;
    }

    public function countRows($where = array())
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where);
        } else {
            $select = $where;
        }
        $profiler = Kwf_Registry::get('db')->getProfiler();
        $p = $profiler->queryStart($this->_collection->__toString()."\n".Zend_Json::encode($this->_getQuery($select)));
        $ret = $this->_collection->find($this->_getQuery($select))->count();
        if ($profiler instanceof Kwf_Db_Profiler) $profiler->getLogger()->info('(count) count result '.$ret);
        $p = $profiler->queryEnd($p);
        return $ret;
    }

    private function _getQuery(Kwf_Model_Select $select)
    {
        $where = array();
        if ($equals = $select->getPart(Kwf_Model_Select::WHERE_EQUALS)) {
            foreach ($equals as $k=>$v) {
                $where[$k] = $v;
            }
        }
        if ($exprs = $select->getPart(Kwf_Model_Select::WHERE_EXPRESSION)) {
            foreach ($exprs as $e) {
                if ($e instanceof Kwf_Model_Select_Expr_Equals) {
                    $where[$e->getField()] = $e->getValue();
                } else if ($e instanceof Kwf_Model_Select_Expr_NotEquals) {
                    $where[$e->getField()]['$ne'] = $e->getValue();
                } else if ($e instanceof Kwf_Model_Select_Expr_HigherDate) {
                    $where[$e->getField()]['$gt'] = new MongoDate(strtotime($e->getValue()));
                } else if ($e instanceof Kwf_Model_Select_Expr_SmallerDate) {
                    $where[$e->getField()]['$lt'] = new MongoDate(strtotime($e->getValue()));
                } else if ($e instanceof Kwf_Model_Select_Expr_HigherEqualDate) {
                    $where[$e->getField()]['$gte'] = new MongoDate(strtotime($e->getValue()));
                } else if ($e instanceof Kwf_Model_Select_Expr_SmallerEqualDate) {
                    $where[$e->getField()]['$lte'] = new MongoDate(strtotime($e->getValue()));
                }
            }
        }
        if ($id = $select->getPart(Kwf_Model_Select::WHERE_ID)) {
            $where['id'] = $id; //TODO dynam.
        }
        return $where;
    }

    public function getIds($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }
        $cursor = $this->_getCursor($select)->fields(array('id'=>1)); //TODO 'id' dynam.
        $ret = array();
        $profiler = Kwf_Registry::get('db')->getProfiler();
        $p = $profiler->queryStart($this->_collection->__toString()."\n".Zend_Json::encode($this->_getQuery($select)));
        foreach ($cursor as $row) {
            $ret[] = $row['id']; //TODO: dynam.
        }
        if ($profiler instanceof Kwf_Db_Profiler) {
            $profiler->getLogger()->info('(ids) count result '.count($ret));
            //$profiler->getLogger()->debug(print_r($cursor->explain(), true));
        }
        $p = $profiler->queryEnd($p);
        return $ret;
    }

    private function _getCursor($select)
    {
        $cursor = $this->_collection->find($this->_getQuery($select));
        if ($order = $select->getPart(Kwf_Model_Select::ORDER)) {
            $o = array();
            foreach ($order as $i) {
                if (isset($i['direction']) && $i['direction']=='DESC') {
                    $o[$i['field']] = -1;
                } else {
                    $o[$i['field']] = 1;
                }
            }
            $cursor->sort($o);
        }
        if ($select->getPart(Kwf_Model_Select::LIMIT_COUNT)) {
            $cursor->limit($select->getPart(Kwf_Model_Select::LIMIT_COUNT));
        }
        if ($select->getPart(Kwf_Model_Select::LIMIT_OFFSET)) {
            $cursor->skip($select->getPart(Kwf_Model_Select::LIMIT_OFFSET));
        }
        return $cursor;
    }

    public function getRow($select)
    {
        if (!$select) {
            throw new Kwf_Exception('getRow needs a parameter, null is not allowed.');
        }
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        $profiler = Kwf_Registry::get('db')->getProfiler();
        $p = $profiler->queryStart($this->_collection->__toString()."\n".Zend_Json::encode($this->_getQuery($select)));
        $row = $this->_collection->findOne($this->_getQuery($select));
        $p = $profiler->queryEnd($p);
        if (!$row) return null;

        $id = $row['_id'];
        if ($id instanceof MongoId) $id = $id->__toString();
        if (!isset($this->_data[$id])) {
            $this->_data[$id] = $row;
        }
        $ret =  new $this->_rowClass(array(
            'data' => $this->_data[$id],
            'model' => $this
        ));
        return $ret;
    }

    public function getRows($where=null, $order=null, $limit=null, $start=null)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where, $order, $limit, $start);
        } else {
            $select = $where;
        }

        $keys = array();
        $cursor = $this->_getCursor($select);


        $profiler = Kwf_Registry::get('db')->getProfiler();
        $p = $profiler->queryStart($this->_collection->__toString()."\n".Zend_Json::encode($this->_getQuery($select)));
        foreach($cursor as $row) {
            $id = $row['_id'];
            if ($id instanceof MongoId) $id = $id->__toString();
            if (!isset($this->_data[$id])) {
                $this->_data[$id] = $row;
            }
            $keys[] = $id;
        }
        $p = $profiler->queryEnd($p);
        if ($profiler instanceof Kwf_Db_Profiler) {
            $profiler->getLogger()->debug('(rows) count result '.count($keys));
            /*
            $profiler->getLogger()->debug(print_r($cursor->explain(), true));
            $i = 0;
            foreach ($keys as $id) {
                $i++;
                $profiler->getLogger()->debug(print_r($this->_data[$id], true));
                if ($i > 10) {
                    $profiler->getLogger()->debug('...');
                    break;
                }
            }
            */
        }

        $ret =  new $this->_rowsetClass(array(
            'dataKeys' => $keys,
            'model' => $this
        ));
        return $ret;
    }

    public function hasColumn($c)
    {
        if ($c == 'component_id') return false; //TODO
        if ($c == 'visible') return false; //TODO
        return parent::hasColumn($c);
    }

    protected function _getOwnColumns()
    {
        return array();
    }

    public function getPrimaryKey()
    {
        return 'id'; //TODO: _id verwenden, id soll optional sein
    }

    public function deleteRows($where)
    {
        if (!is_object($where)) {
            if (is_string($where)) $where = array($where);
            $select = $this->select($where);
        } else {
            $select = $where;
        }
        $profiler = Kwf_Registry::get('db')->getProfiler();
        $p = $profiler->queryStart($this->_collection->__toString()."\n".Zend_Json::encode($this->_getQuery($select)));
        $ret = $this->_collection->remove(
            $this->_getQuery($select),
            array('safe'=>true, 'multiple'=>false)
        );
        $p = $profiler->queryEnd($p);
        if (!$ret || !$ret['ok']) {
            throw new Kwf_Exception("delete failed");
        }
    }

    public function import($format, $data, $options = array())
    {
        if ($format == self::FORMAT_ARRAY) {
            if (isset($options['replace']) && $options['replace']) {
                throw new Kwf_Exception_NotYetImplemented();
            }
            $ret = $this->getCollection()->batchInsert($data, array('safe'=>true));
            if (!$ret || !$ret['ok']) {
                throw new Kwf_Exception("import failed");
            }
        } else {
            parent::import($format, $data, $options);
        }
    }
}

