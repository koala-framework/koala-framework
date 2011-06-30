<?php
abstract class Vps_Model_Abstract implements Vps_Model_Interface
{
    protected $_rowClass = 'Vps_Model_Row_Abstract';
    protected $_rowsetClass = 'Vps_Model_Rowset_Abstract';
    protected $_default = array();
    protected $_siblingModels = array();
    protected $_dependentModels = array();
    protected $_referenceMap = array();
    protected $_toStringField;
    /**
     * Row-Filters für automatisch befüllte Spalten
     *
     * Anwendungsbeispiele:
     * _filters = 'filename' //verwendet autom. Vps_Filter_Ascii
     * _filters = array('filename') //verwendet autom. Vps_Filter_Ascii
     * _filters = array('pos')      //Vps_Filter_Row_Numberize
     * _filters = array('pos' => 'MyFilter')
     * _filters = array('pos' => new MyFilter($settings))
     */
    protected $_filters = array();

    /**
     * Unterstützte Import / Export Formate
     *
     * Reihenfolge: Bevorzugtes Format muss als erstes stehen.
     */
    protected $_supportedImportExportFormats = array(self::FORMAT_ARRAY);

    protected $_exprs = array();


    protected $_rows = array();

    private static $_instances = array();
    private $_hasColumnsCache = array();

    protected $_proxyContainerModels = array();

    public function __construct(array $config = array())
    {
        if (isset($config['default'])) $this->_default = (array)$config['default'];
        if (isset($config['siblingModels'])) $this->_siblingModels = (array)$config['siblingModels'];
        if (isset($config['dependentModels'])) $this->_dependentModels = (array)$config['dependentModels'];
        if (isset($config['referenceMap'])) $this->_referenceMap = (array)$config['referenceMap'];
        if (isset($config['filters'])) $this->_filters = (array)$config['filters'];
        if (isset($config['toStringField'])) $this->_toStringField = (string)$config['toStringField'];
        if (isset($config['exprs'])) $this->_exprs = (array)$config['exprs'];
        $this->_init();
    }

    /**
     * @param Vps_Model_Abstract|string wenn string: entweder aus config (models.modelName)
     *                                               oder Klassenname von Model
     * @return Vps_Model_Interface
     **/
    public static function getInstance($modelName)
    {
        if (is_object($modelName)) return $modelName;
        static $config;
        if (!isset($config)) $config = Vps_Registry::get('config')->models->toArray();
        if (array_key_exists($modelName, $config)) {
            if (!$config[$modelName]) return null;
            $modelName = $config[$modelName];
        }
        if (!isset(self::$_instances[$modelName])) {
            self::$_instances[$modelName] = new $modelName();
        }
        return self::$_instances[$modelName];
    }

    //für unit-tests
    public static function clearInstances()
    {
        self::$_instances = array();
    }

    public static function getInstances()
    {
        return self::$_instances;
    }

    protected function _init()
    {
        $this->_setupFilters();
    }

    //kann gesetzt werden von proxy
    public function addProxyContainerModel($m)
    {
        $this->_proxyContainerModels[] = $m;
    }

    protected function _setupFilters()
    {
    }

    public function getFilters()
    {
        if (is_string($this->_filters)) $this->_filters = array($this->_filters);
        foreach($this->_filters as $k=>$f) {
            if (is_int($k)) {
                unset($this->_filters[$k]);
                $k = $f;
                if ($k == 'pos') {
                    $f = 'Vps_Filter_Row_Numberize';
                } else {
                    $f = 'Vps_Filter_Ascii';
                }
            }
            if (is_string($f)) {
                if (!class_exists($f)) {
                    if (class_exists('Vps_Filter_Row_'.$f)) {
                        $f = 'Vps_Filter_Row_'.$f;
                        $f = new $f();
                    } else if (class_exists('Vps_Filter_'.$f)) {
                        $f = 'Vps_Filter_'.$f;
                        $f = new $f();
                    } else {
                        throw new Vps_Exception("Invalid filter class");
                    }
                }
                $f = new $f();
            }
            if ($f instanceof Vps_Filter_Row_Abstract) {
                $f->setField($k);
            }
            $this->_filters[$k] = $f;
        }
        return $this->_filters;
    }

    public function setDefault(array $default)
    {
        $this->_default = $default;
        return $this;
    }

    public function createRow(array $data=array())
    {
        return $this->_createRow($data);
    }
    protected function _createRow(array $data=array(), array $rowConfig = array())
    {
        $rowConfig['model'] = $this;
        $rowConfig['data'] = $this->_default;
        $ret = new $this->_rowClass($rowConfig);

        $siblingRows = array();
        foreach ($this->getSiblingModels() as $m) {
            if ($m instanceof Vps_Model_SubModel_Interface) {
                $siblingRows[] = $m->getRowBySiblingRow($ret);
            } else {
                $siblingRows[] = $m->createRow();
            }
        }
        $ret->setSiblingRows($siblingRows);
        foreach ($data as $k=>$i) {
            $ret->$k = $i;
        }
        $pk = $this->getPrimaryKey();
        if (isset($ret->$pk) && !$ret->$pk) {
            $ret->$pk = null;
        }
        return $ret;
    }

    public function getRow($select)
    {
        if (!is_object($select)) {
            $select = $this->select($select);
        }
        $select->limit(1);
        return $this->getRows($select)->current();
    }

    public function getIds($where=null, $order=null, $limit=null, $start=null)
    {
        $ret = array();
        foreach ($this->getRows($where, $order, $limit, $start) as $row) {
            $ret[] = $row->{$this->getPrimaryKey()};
        }
        return $ret;
    }

    public function countRows($select = array())
    {
        return count($this->getRows($select));
    }


    public function getDefault()
    {
        return $this->_default;
    }

    public function isEqual(Vps_Model_Interface $other)
    {
        throw new Vps_Exception("Method 'isEqual' is not yet implemented in '".get_class($this)."'");
    }

    public function select($where = array(), $order = null, $limit = null, $start = null)
    {
        if (is_array($where)) {
            $ret = new Vps_Model_Select($where);
        } else if (!($where instanceof Vps_Model_Select)) {
            $ret = new Vps_Model_Select();
            if ($where) {
                $ret->whereEquals($this->getPrimaryKey(), $where);
            }
        } else {
            $ret = $where;
        }
        if ($order) $ret->order($order);
        if ($limit || $start) $ret->limit($limit, $start);
        return $ret;
    }

    public function getColumnType($col)
    {
        if (in_array($col, $this->getExprColumns())) {
            return $this->_exprs[$col]->getResultType();
        }
        foreach ($this->getSiblingModels() as $m) {
            return $m->getColumnType($col);
        }
        return null;
    }

    private function _hasColumn($col)
    {
        if (!$this->getOwnColumns()) return true;
        if (in_array($col, $this->getOwnColumns())) return true;
        if (in_array($col, $this->getExprColumns())) return true;
        foreach ($this->getSiblingModels() as $m) {
            if ($m->hasColumn($col)) return true;
        }
        return false;
    }

    public function hasColumn($col)
    {
        if (!isset($this->_hasColumnsCache[$col])) {
            $this->_hasColumnsCache[$col] = $this->_hasColumn($col);
        }
        return $this->_hasColumnsCache[$col];
    }

    public function getExprColumns()
    {
        return array_keys($this->_exprs);
    }

    public final function getOwnColumns()
    {
        $ret = $this->_getOwnColumns();
        return $ret;
    }

    abstract protected function _getOwnColumns();

    public function getColumns()
    {
        $ret = $this->getOwnColumns();
        $ret = array_merge($ret, $this->getExprColumns());
        foreach ($this->getSiblingModels() as $m) {
            $ret = array_merge($ret, $m->getColumns());
        }
        return $ret;
    }

    public function getSiblingModels()
    {
        foreach ($this->_siblingModels as $k=>$i) {
            if (is_string($i)) $this->_siblingModels[$k] = Vps_Model_Abstract::getInstance($i);
        }
        return $this->_siblingModels;
    }

    public function getReferenceRuleByModelClass($modelClassName)
    {
        $rules = $this->getReferenceRulesByModelClass($modelClassName);
        if (count($rules) > 1) {
            throw new Vps_Exception("there exist more than one rule with modelclass '$modelClassName'. "
                ."Try getReferenceRulesByModelClass(\$modelClassName) to get all matching rules.");
        } else if (count($rules) == 0) {
            throw new Vps_Exception("there is no rule with modelclass '$modelClassName'.");
        } else {
            return $rules[0];
        }
    }

    public function getReferenceRulesByModelClass($modelClassName)
    {
        $ret = array();
        foreach ($this->getReferences() as $rule) {
            $ref = $this->getReference($rule);
            if ($ref === Vps_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
                $c = $this->getParentModel();
            } else if (isset($ref['refModelClass'])) {
                $c = $ref['refModelClass'];
            } else if (isset($ref['refModel'])) {
                $c = $ref['refModel'];
            } else {
                throw new Vps_Exception("refModelClass and refModel not set");
            }
            if (is_instance_of($modelClassName, $c)) {
                $ret[$rule] = $ref;
            }
        }
        if (count($ret) >= 1) {
            return array_keys($ret);
        } else {
            throw new Vps_Exception("No reference from '".get_class($this)."' to '$modelClassName'");
        }
    }

    public function getReferenceByModelClass($modelClassName, $rule)
    {
        $models = array($this);
        $models = array_merge($models, $this->_proxyContainerModels);
        foreach ($models as $m) {
            $matchingRules = $m->getReferenceRulesByModelClass($modelClassName);

            if (count($matchingRules) > 1) {
                if ($rule && in_array($rule, $matchingRules)) {
                    return $m->getReference($rule);
                } else {
                    throw new Vps_Exception("Multiple references from '".get_class($this)."' to '$modelClassName' found, but none with rule-name '$rule'");
                }
            } else if (count($matchingRules) == 1) {
                return $m->getReference($matchingRules[0]);
            }
        }
        throw new Vps_Exception("No reference from '".get_class($this)."' to '$modelClassName'");
    }

    /**
     * Namen der verfügbaren References
     *
     * Details zu einer Reference über getReferencedModel() bzw. getReference() holen
     */
    public function getReferences()
    {
        return array_keys($this->_referenceMap);
    }

    public function getReference($rule)
    {
        if (!isset($this->_referenceMap[$rule])) {
            throw new Vps_Exception("Reference '$rule' for model '".get_class($this)."' not set, set are '".implode(', ', array_keys($this->_referenceMap))."'");
        }
        $ret = $this->_referenceMap[$rule];
        if (is_string($ret)) {
            if ($ret === Vps_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
            } else {
                if (strpos($ret, '->') === false) {
                    throw new Vps_Exception("Reference '$rule' for model '".get_class($this)."' is a string but doesn't contain ->");
                }
                $ret = array(
                    'refModelClass' => substr($ret, strpos($ret, '->')+2),
                    'column' => substr($ret, 0, strpos($ret, '->')),
                );
            }
        }
        return $ret;
    }

    public function getReferencedModel($rule)
    {
        $ref = $this->getReference($rule);
        if ($ref === Vps_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
            return $this->getParentModel();
        }
        if (isset($ref['refModelClass'])) {
            return self::getInstance($ref['refModelClass']);
        }
        if (isset($ref['refModel'])) {
            return $ref['refModel'];
        }
        throw new Vps_Exception("refModelClass not set for reference '$rule'");
    }

    public function getDependentRuleByModelClass($modelClassName)
    {
        $rules = $this->getDependentRulesByModelClass($modelClassName);
        if (count($rules) > 1) {
            throw new Vps_Exception("there exist more than one rule with modelclass '$modelClassName'. "
                ."Try getDependentRulesByModelClass(\$modelClassName) to get all matching rules.");
        } else if (count($rules) == 0) {
            throw new Vps_Exception("there is no rule with modelclass '$modelClassName'.");
        } else {
            return $rules[0];
        }
    }

    public function getDependentRulesByModelClass($modelClassName)
    {
        $ret = array();
        foreach ($this->_dependentModels as $k => $m) {
            if (is_instance_of($modelClassName, $m)) {
                $ret[] = $k;
            }
        }
        return $ret;
    }

    protected function _createDependentModel($rule)
    {
        $ret = $this->_dependentModels[$rule];
        if (is_array($ret)) {
            if (!isset($ret['model'])) {
                throw new Vps_Exception("model not set for dependentModel");
            }
            $ret = $ret['model'];
        }
        if (is_string($ret)) {
            if (strpos($ret, '->') !== false) {
                $m = Vps_Model_Abstract::getInstance(substr($ret, 0, strpos($ret, '->')));
                $ret = $m->_createDependentModel(substr($ret, strpos($ret, '->')+2));
            } else {
                $ret = Vps_Model_Abstract::getInstance($ret);
            }
        }
        return $ret;
    }

    public function getDependentModelWithDependentOf($rule)
    {
        if (!$rule) {
            throw new Vps_Exception("rule parameter is required");
        }
        if (!is_string($rule)) {
            throw new Vps_Exception("rule parameter as string is required, ".gettype($rule)." given");
        }
        $models = $this->_proxyContainerModels;
        $models[] = $this;
        foreach ($models as $m) {
            if (isset($m->_dependentModels[$rule])) {
                $ret = $m->_dependentModels[$rule];
                if (!is_array($ret)) $ret = array();
                $ret['model'] = $m->_createDependentModel($rule);
                $ret['dependentOf'] = $m;
                return $ret;
            }
        }

        $existingRules = array();
        foreach ($models as $m) {
            $existingRules = array_merge($existingRules, array_keys($m->_dependentModels));
        }
        throw new Vps_Exception("dependent Model with rule '$rule' does not exist for '".get_class($this)."', possible are '".implode("', '", $existingRules)."'");
    }

    public function getDependentModel($rule)
    {
        $ret = $this->getDependentModelWithDependentOf($rule);
        return $ret['model'];
    }

    /**
     * @internal return value will change, don't use it
     */
    public function getDependentModels()
    {
        $ret = array();
        //TODO _proxyContainerModels berücksichtigen
        foreach (array_keys($this->_dependentModels) as $rule) {
            $ret[$rule] = $this->_createDependentModel($rule);
        }
        return $ret;
    }

    public function getRowsetClass()
    {
        return $this->_rowsetClass;
    }


    public function find($id)
    {
        $s = $this->select();
        $s->whereEquals($this->getPrimaryKey(), $id);
        return $this->getRows($s);
    }

    public function fetchAll($where=null, $order=null, $limit=null, $start=null)
    {
        return $this->getRows($where, $order, $limit, $start);
    }

    public function fetchCount($where = array())
    {
        return $this->countRows($where);
    }

    public function getToStringField()
    {
        return $this->_toStringField;
    }

    public function getSupportedImportExportFormats()
    {
        if (!$this->_supportedImportExportFormats) return null;
        return $this->_supportedImportExportFormats;
    }

    protected static final function _optimalImportExportFormat(Vps_Model_Interface $model1, Vps_Model_Interface $model2)
    {
        $formats = array_values(array_intersect(
            $model1->getSupportedImportExportFormats(),
            $model2->getSupportedImportExportFormats()
        ));
        if (!$formats || !$formats[0]) {
            throw new Vps_Exception("Model '".get_class($model1)."' cannot copy data "
                ."from model '".get_class($model2)."'. Import / Export Formats are not compatible.");
        }
        return $formats[0];
    }

    public function copyDataFromModel(Vps_Model_Interface $sourceModel, Vps_Model_Select $select = null, array $importOptions = array())
    {
        $format = self::_optimalImportExportFormat($this, $sourceModel);
        $this->import($format, $sourceModel->export($format, $select), $importOptions);
    }

    public function export($format, $select = array())
    {
        if ($format == self::FORMAT_ARRAY) {
            return $this->getRows($select)->toArray();
        } else {
            throw new Vps_Exception_NotYetImplemented();
        }
    }

    public function import($format, $data, $options = array())
    {
        if ($format == self::FORMAT_ARRAY) {
            if (isset($options['replace']) && $options['replace']) {
                throw new Vps_Exception_NotYetImplemented();
            }
            foreach ($data as $k => $v) {
                $this->createRow($v)->save();
            }
            $this->_afterImport($format, $data, $options);
        } else {
            throw new Vps_Exception_NotYetImplemented();
        }
    }

    protected function _afterImport($format, $data, $options)
    {
        foreach ($this->_proxyContainerModels as $m) {
            $m->_afterImport($format, $data, $options);
        }
    }

    public function writeBuffer()
    {
    }

    public function deleteRows($where)
    {
        throw new Vps_Exception('not implemented yet.');
    }

    protected function _afterDeleteRows($where)
    {
    }

    public function updateRows($data, $where)
    {
        throw new Vps_Exception('not implemented yet.');
    }

    public function transformColumnName($c)
    {
        return $c;
    }

    public function getUniqueIdentifier()
    {
        throw new Vps_Exception_NotYetImplemented();
    }

    public function getExprValue($row, $name)
    {
        if ($name instanceof Vps_Model_Select_Expr_Interface) {
            $expr = $name;
        } else {
            $expr = $this->_exprs[$name];
        }

        if ($expr instanceof Vps_Model_Select_Expr_Child_Contains) {
            if (!$row instanceof Vps_Model_Row_Interface) {
                $row = $this->getRow($row[$this->getPrimaryKey()]);
            }
            return (bool)count($row->getChildRows($expr->getChild(), $expr->getSelect()));
        } else if ($expr instanceof Vps_Model_Select_Expr_Child) {
            if (!$row instanceof Vps_Model_Row_Interface) {
                $row = $this->getRow($row[$this->getPrimaryKey()]);
            }
            $childs = $row->getChildRows($expr->getChild(), $expr->getSelect());
            return self::_evaluateExprForRowset($childs, $expr->getExpr());
        } else if ($expr instanceof Vps_Model_Select_Expr_Parent) {
            if (!$row instanceof Vps_Model_Row_Interface) {
                $row = $this->getRow($row[$this->getPrimaryKey()]);
            }
            $parent = $row->getParentRow($expr->getParent());
            if (!$parent) return null;
            $field = $expr->getField();
            return $parent->$field;
        } else if ($expr instanceof Vps_Model_Select_Expr_Concat) {
            $ret = '';
            foreach ($expr->getExpressions() as $e) {
                if ($e instanceof Vps_Model_Select_Expr_Interface) {
                    $ret .= $this->getExprValue($row, $e);
                } else {
                    if (is_array($row)) {
                        $ret .= $row[$e];
                    } else {
                        $ret .= $row->$e;
                    }
                }
            }
            return $ret;
        } else if ($expr instanceof Vps_Model_Select_Expr_String) {
            return $expr->getString();
        } else if ($expr instanceof Vps_Model_Select_Expr_StrPad) {
            $f = $expr->getField();
            if (is_array($row)) {
                $v = $row[$f];
            } else {
                $v = $row->$f;
            }
            // faking mysql's implementation of LPAD / RPAD
            // mysql cuts always right when the string is too long, it does not
            // depend on the pad-type
            if ($expr->getPadLength() < mb_strlen($v)) {
                return substr($v, 0, $expr->getPadLength());
            }
            $padType = STR_PAD_RIGHT;
            if ($expr->getPadType() == Vps_Model_Select_Expr_StrPad::LEFT) {
                $padType = STR_PAD_LEFT;
            } else if ($expr->getPadType() == Vps_Model_Select_Expr_StrPad::RIGHT) {
                $padType = STR_PAD_RIGHT;
            }
            return str_pad($v, $expr->getPadLength(), $expr->getPadStr(), $padType);
        } else if ($expr instanceof Vps_Model_Select_Expr_Date_Year) {
            $f = $expr->getField();
            if (is_array($row)) {
                $v = $row[$f];
            } else {
                $v = $row->$f;
            }
            return date('Y', strtotime($v));
        } else if ($expr instanceof Vps_Model_Select_Expr_Field) {
            $f = $expr->getField();
            if (is_array($row)) {
                return $row[$f];
            } else {
                return $row->$f;
            }
        } else if ($expr instanceof Vps_Model_Select_Expr_PrimaryKey) {
            $f = $this->getPrimaryKey();
            if (is_array($row)) {
                return $row[$f];
            } else {
                return $row->$f;
            }
        } else if ($expr instanceof Vps_Model_Select_Expr_SumFields) {
            $ret = 0;
            foreach ($expr->getFields() as $f) {
                if (is_int($f)) {
                    $ret += $f;
                } else if (is_string($f)) {
                    if (is_array($row)) {
                        $ret += $row[$f];
                    } else {
                        $ret += $row->$f;
                    }
                } else if ($f instanceof Vps_Model_Select_Expr_Interface) {
                    $ret += $this->getExprValue($row, $f);
                } else {
                    throw new Vps_Exception_NotYetImplemented();
                }
            }
            return $ret;
        } else {
            throw new Vps_Exception_NotYetImplemented(
                "Expression '".(is_string($expr) ? $expr : get_class($expr))."' is not yet implemented"
            );
        }
    }

    public function evaluateExpr(Vps_Model_Select_Expr_Interface $expr, Vps_Model_Select $select = null)
    {
        $rows = $this->getRows($select);
        return self::_evaluateExprForRowset($rows, $expr);
    }

    private static function _evaluateExprForRowset($rowset, Vps_Model_Select_Expr_Interface $expr)
    {
        if ($expr instanceof Vps_Model_Select_Expr_Count) {
            if ($expr->getField() != '*') {
                $f = $expr->getField();
                $values = array();
                foreach ($rowset as $r) {
                    if (!is_null($r->$f)) $values[] = $r->$f;
                }
                if ($expr->getDistinct()) {
                    $values = array_unique($values);
                }
                return count($values);
            } else {
                return $rowset->count();
            }
        } else if ($expr instanceof Vps_Model_Select_Expr_Sum) {
            $f = $expr->getField();
            $ret = 0;
            foreach ($rowset as $r) {
                $ret += $r->$f;
            }
            return $ret;
        } else if ($expr instanceof Vps_Model_Select_Expr_Max) {
            $f = $expr->getField();
            $ret = $rowset->current()->$f;
            foreach ($rowset as $r) {
                $ret = max($ret, $r->$f);
            }
            return $ret;
        } else if ($expr instanceof Vps_Model_Select_Expr_Min) {
            $f = $expr->getField();
            $ret = $rowset->current()->$f;
            foreach ($rowset as $r) {
                $ret = min($ret, $r->$f);
            }
            return $ret;
        } else if ($expr instanceof Vps_Model_Select_Expr_Field) {
            if (!count($rowset)) {
                return null;
            }
            $f = $expr->getField();
            return $rowset->current()->$f;
        } else {
            throw new Vps_Exception_NotYetImplemented("support for ".get_class($expr)." is not yet implemented");
        }
    }

    public function updateRow(array $data)
    {
        $row = $this->getRow($data[$this->getPrimaryKey()]);
        if (!$row) {
            throw new Vps_Exception("Can't update row, row not found");
        }
        foreach ($data as $k => $v) {
            $row->$k = $v;
        }
        $row->save();
        return $row->toArray();
    }

    public function insertRow(array $data)
    {
        $row = $this->createRow();
        foreach ($data as $k => $v) {
            if ($this->getPrimaryKey() != $k) {
                $row->$k = $v;
            }
        }
        $row->save();
        return $row->toArray();
    }

    public function callMultiple(array $call)
    {
        $ret = array();
        foreach ($call as $method=>$arguments) {
            $ret[$method] = call_user_func_array(array($this, $method), $arguments);
        }
        return $ret;
    }

    public function toDebug()
    {
        $ret = '<pre> Model '.get_class($this).'</pre>';
        return $ret;
    }

    /**
     * @internal
     */
    public function dependentModelRowUpdated(Vps_Model_Row_Abstract $row, $action)
    {
    }

    /**
     * @internal
     */
    public function childModelRowUpdated(Vps_Model_Row_Abstract $row, $action)
    {
    }

    /**
     * Kann zum Speicher-Sparen aufgerufen werden
     */
    public function clearRows()
    {
        $this->_rows = array();
    }

    /**
     * @internal
     */
    public function getProxyContainerModels()
    {
        return $this->_proxyContainerModels;
    }

    /**
     * @internal
     */
    public function getExpr($name)
    {
        return $this->_exprs[$name];
    }
}
