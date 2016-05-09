<?php
/**
 * @package Model
 */
abstract class Kwf_Model_Abstract implements Kwf_Model_Interface
{
    protected $_rowClass = 'Kwf_Model_Row_Abstract';
    protected $_rowsetClass = 'Kwf_Model_Rowset_Abstract';
    protected $_default = array();
    protected $_siblingModels = array();
    protected $_dependentModels = array();
    protected $_referenceMap = array();
    protected $_toStringField;
    
    protected $_hasDeletedFlag = false;
    /**
     * Row-Filters für automatisch befüllte Spalten
     *
     * Anwendungsbeispiele:
     * _filters = 'filename' //verwendet autom. Kwf_Filter_Ascii
     * _filters = array('filename') //verwendet autom. Kwf_Filter_Ascii
     * _filters = array('pos')      //Kwf_Filter_Row_Numberize
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

    private static $_allInstances = array();
    private $_hasColumnsCache = array();
    private $_factoryConfig = null;

    protected $_proxyContainerModels = array();

    //public static $instanceCount = array();

    protected $_columnMappings = array();

    public function __construct(array $config = array())
    {
        if (isset($config['default'])) $this->_default = (array)$config['default'];
        if (isset($config['siblingModels'])) $this->_siblingModels = (array)$config['siblingModels'];
        if (isset($config['dependentModels'])) $this->_dependentModels = (array)$config['dependentModels'];
        if (isset($config['referenceMap'])) $this->_referenceMap = (array)$config['referenceMap'];
        if (isset($config['filters'])) $this->_filters = (array)$config['filters'];
        if (isset($config['toStringField'])) $this->_toStringField = (string)$config['toStringField'];
        if (isset($config['exprs'])) $this->_exprs = (array)$config['exprs'];
        if (isset($config['hasDeletedFlag'])) $this->_hasDeletedFlag = $config['hasDeletedFlag'];
        //self::$instanceCount[spl_object_hash($this)] = get_class($this);
        self::$_allInstances[] = $this;
        $this->_init();
    }

    public function __destruct()
    {
        //unset(self::$instanceCount[spl_object_hash($this)]);
    }

    /**
     * @param Kwf_Model_Abstract|string wenn string: entweder aus config (models.modelName)
     *                                               oder Klassenname von Model
     * @return Kwf_Model_Interface
     **/
    public static function getInstance($modelName)
    {
        if (is_object($modelName)) return $modelName;
        static $config;
        if (!isset($config)) $config = Kwf_Config::getValueArray('models');
        if (array_key_exists($modelName, $config)) {
            if (!$config[$modelName]) return null;
            $modelName = $config[$modelName];
        }
        return Kwf_Model_Factory_ClassName::getModelInstance($modelName);
    }

    //für unit-tests
    public static function clearInstances()
    {
        Kwf_Model_Factory_ClassName::clearInstances();
        self::$_allInstances = array();
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
        foreach ($this->_filters as $k=>$f) {
            if (is_int($k)) {
                unset($this->_filters[$k]);
                $k = $f;
                if ($k == 'pos') {
                    $f = 'Kwf_Filter_Row_Numberize';
                } else {
                    $f = 'Kwf_Filter_Ascii';
                }
            }
            if (is_string($f)) {
                if (!class_exists($f)) {
                    if (class_exists('Kwf_Filter_Row_'.$f)) {
                        $f = 'Kwf_Filter_Row_'.$f;
                        $f = new $f();
                    } else if (class_exists('Kwf_Filter_'.$f)) {
                        $f = 'Kwf_Filter_'.$f;
                        $f = new $f();
                    } else {
                        throw new Kwf_Exception("Invalid filter class");
                    }
                }
                $f = new $f();
            }
            if ($f instanceof Kwf_Filter_Row_Abstract) {
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
            if ($m instanceof Kwf_Model_SubModel_Interface) {
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
        if (!$select) {
            throw new Kwf_Exception('getRow needs a parameter, null is not allowed.');
        }
        if (!is_object($select)) {
            $select = $this->select($select);
        } else {
            $select = clone $select;
        }
        $select->limit(1);
        $rows = $this->getRows($select);
        if ($rows->valid()) {
            return $rows->current();
        } else {
            return null;
        }
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

    public function isEqual(Kwf_Model_Interface $other)
    {
        throw new Kwf_Exception("Method 'isEqual' is not yet implemented in '".get_class($this)."'");
    }

    public function select($where = array(), $order = null, $limit = null, $start = null)
    {
        if (is_array($where)) {
            $ret = new Kwf_Model_Select($where);
        } else if (!($where instanceof Kwf_Model_Select)) {
            $ret = new Kwf_Model_Select();
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
            if (is_string($i)) $this->_siblingModels[$k] = Kwf_Model_Abstract::getInstance($i);
        }
        return $this->_siblingModels;
    }

    public function getReferenceRuleByModelClass($modelClassName)
    {
        $rules = $this->getReferenceRulesByModelClass($modelClassName);
        if (count($rules) > 1) {
            throw new Kwf_Exception("there exist more than one rule with modelclass '$modelClassName'. "
                ."Try getReferenceRulesByModelClass(\$modelClassName) to get all matching rules.");
        } else if (count($rules) == 0) {
            throw new Kwf_Exception("there is no rule with modelclass '$modelClassName'.");
        } else {
            return $rules[0];
        }
    }

    public function getReferenceRulesByModelClass($modelClassName)
    {
        $ret = array();
        foreach ($this->getReferences() as $rule) {
            $ref = $this->getReference($rule);
            if ($ref === Kwf_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
                $c = $this->getParentModel();
            } else if (isset($ref['refModelClass'])) {
                $c = $ref['refModelClass'];
            } else if (isset($ref['refModel'])) {
                $c = $ref['refModel'];
            } else {
                throw new Kwf_Exception("refModelClass and refModel not set");
            }
            if (is_instance_of($modelClassName, $c)) {
                $ret[$rule] = $ref;
            }
        }
        if (count($ret) >= 1) {
            return array_keys($ret);
        } else {
            throw new Kwf_Exception("No reference from '".get_class($this)."' to '$modelClassName'");
        }
    }

    public function getReferenceByModelClass($modelClassName, $rule)
    {
        $models = array($this);
        $models = array_merge($models, $this->_proxyContainerModels);
        $m = $this;
        while ($m instanceof Kwf_Model_Proxy) {
            $m = $m->getProxyModel();
            $models[] = $m;
        }
        foreach ($models as $m) {
            $matchingRules = $m->getReferenceRulesByModelClass($modelClassName);

            if (count($matchingRules) > 1) {
                if ($rule && in_array($rule, $matchingRules)) {
                    return $m->getReference($rule);
                } else {
                    throw new Kwf_Exception("Multiple references from '".get_class($this)."' to '$modelClassName' found, but none with rule-name '$rule'");
                }
            } else if (count($matchingRules) == 1) {
                return $m->getReference($matchingRules[0]);
            }
        }
        throw new Kwf_Exception("No reference from '".get_class($this)."' to '$modelClassName'");
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
        $models = array($this);
        $models = array_merge($models, $this->_proxyContainerModels);
        $m = $this;
        while ($m instanceof Kwf_Model_Proxy) {
            $m = $m->getProxyModel();
            $models[] = $m;
        }
        foreach ($models as $m) {
            if (isset($m->_referenceMap[$rule])) {
                $ret = $m->_referenceMap[$rule];
                if (is_string($ret)) {
                    if ($ret === Kwf_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
                    } else {
                        if (strpos($ret, '->') === false) {
                            throw new Kwf_Exception("Reference '$rule' for model '".get_class($m)."' is a string but doesn't contain ->");
                        }
                        $ret = array(
                            'refModelClass' => substr($ret, strpos($ret, '->')+2),
                            'column' => substr($ret, 0, strpos($ret, '->')),
                        );
                    }
                }
                return $ret;
            }
        }

        $keys = array();
        foreach ($models as $m) {
            $keys = array_merge($keys, array_keys($m->_referenceMap));
        }
        throw new Kwf_Exception("Reference '$rule' for model '".get_class($this)."' not set, set are '".implode(', ', $keys)."'");
    }

    public function getReferencedModel($rule)
    {
        $ref = $this->getReference($rule);
        if ($ref === Kwf_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
            return $this->getParentModel();
        }
        if (isset($ref['refModelClass'])) {
            return self::getInstance($ref['refModelClass']);
        }
        if (isset($ref['refModel'])) {
            return $ref['refModel'];
        }
        throw new Kwf_Exception("refModelClass not set for reference '$rule'");
    }

    public function getDependentRuleByModelClass($modelClassName)
    {
        $rules = $this->getDependentRulesByModelClass($modelClassName);
        if (count($rules) > 1) {
            throw new Kwf_Exception("there exist more than one rule with modelclass '$modelClassName'. "
                ."Try getDependentRulesByModelClass(\$modelClassName) to get all matching rules.");
        } else if (count($rules) == 0) {
            throw new Kwf_Exception("there is no rule with modelclass '$modelClassName'.");
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
                throw new Kwf_Exception("model not set for dependentModel");
            }
            $ret = $ret['model'];
        }
        if (is_string($ret)) {
            if (strpos($ret, '->') !== false) {
                $m = Kwf_Model_Abstract::getInstance(substr($ret, 0, strpos($ret, '->')));
                $ret = $m->_createDependentModel(substr($ret, strpos($ret, '->')+2));
            } else {
                $ret = Kwf_Model_Abstract::getInstance($ret);
            }
        }
        return $ret;
    }

    public function getDependentModelWithDependentOf($rule)
    {
        if (!$rule) {
            throw new Kwf_Exception("rule parameter is required");
        }
        if (!is_string($rule)) {
            throw new Kwf_Exception("rule parameter as string is required, ".gettype($rule)." given");
        }
        $models = $this->_proxyContainerModels;
        $models[] = $this;
        $m = $this;
        while ($m instanceof Kwf_Model_Proxy) {
            $m = $m->getProxyModel();
            $models[] = $m;
        }
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
        throw new Kwf_Exception("dependent Model with rule '$rule' does not exist for '".get_class($this)."', possible are '".implode("', '", $existingRules)."'");
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

    protected static final function _optimalImportExportFormat(Kwf_Model_Interface $model1, Kwf_Model_Interface $model2)
    {
        $formats = array_values(array_intersect(
            $model1->getSupportedImportExportFormats(),
            $model2->getSupportedImportExportFormats()
        ));
        if (!$formats || !$formats[0]) {
            throw new Kwf_Exception("Model '".get_class($model1)."' cannot copy data "
                ."from model '".get_class($model2)."'. Import / Export Formats are not compatible.");
        }
        return $formats[0];
    }

    public function copyDataFromModel(Kwf_Model_Interface $sourceModel, Kwf_Model_Select $select = null, array $importOptions = array())
    {
        $format = self::_optimalImportExportFormat($this, $sourceModel);
        $this->import($format, $sourceModel->export($format, $select), $importOptions);
    }

    public function export($format, $select = array(), $options = array())
    {
        if ($format == self::FORMAT_ARRAY) {
            return $this->getRows($select)->toArray();
        } else {
            throw new Kwf_Exception_NotYetImplemented();
        }
    }

    public function import($format, $data, $options = array())
    {
        if ($format == self::FORMAT_ARRAY) {
            if (isset($options['replace']) && $options['replace']) {
                throw new Kwf_Exception_NotYetImplemented();
            }
            Kwf_Events_ModelObserver::getInstance()->disable();
            foreach ($data as $k => $v) {
                $this->createRow($v)->save();
            }
            Kwf_Events_ModelObserver::getInstance()->enable();
            $this->_afterImport($format, $data, $options);
        } else {
            throw new Kwf_Exception_NotYetImplemented();
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
        throw new Kwf_Exception('not implemented yet.');
    }

    protected function _afterDeleteRows($where)
    {
    }

    public function updateRows($data, $where)
    {
        throw new Kwf_Exception('not implemented yet.');
    }

    public function transformColumnName($c)
    {
        return $c;
    }

    public function getUniqueIdentifier()
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    public function getExprValue($row, $name)
    {
        if ($name instanceof Kwf_Model_Select_Expr_Interface) {
            $expr = $name;
        } else {
            $expr = $this->_exprs[$name];
        }

        if ($expr instanceof Kwf_Model_Select_Expr_Child_Contains) {
            if (!$row instanceof Kwf_Model_Row_Interface) {
                $row = $this->getRow($row[$this->getPrimaryKey()]);
            }
            $ret = (bool)$row->countChildRows($expr->getChild(), $expr->getSelect());
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Child_First) {
            if (!$row instanceof Kwf_Model_Row_Interface) {
                $row = $this->getRow($row[$this->getPrimaryKey()]);
            }
            $s = clone $expr->getSelect();
            $s->limit(1);
            $childRows = $row->getChildRows($expr->getChild(), $s);
            if (count($childRows)) {
                return $childRows[0]->{$expr->getField()};
            } else {
                return null;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Child) {
            if (!$row instanceof Kwf_Model_Row_Interface) {
                $row = $this->getRow($row[$this->getPrimaryKey()]);
            }
            $childs = $row->getChildRows($expr->getChild(), $expr->getSelect());
            return self::_evaluateExprForRowset($childs, $expr->getExpr());
        } else if ($expr instanceof Kwf_Model_Select_Expr_Parent) {
            if (!$row instanceof Kwf_Model_Row_Interface) {
                $row = $this->getRow($row[$this->getPrimaryKey()]);
            }
            $reference = $row->getModel()->getReference($expr->getParent());
            $parentModel = $row->getModel()->getReferencedModel($expr->getParent());
            $select = new Kwf_Model_Select();
            $select->whereId($row->{$reference['column']});
            $select->ignoreDeleted(true);
            $parent = $parentModel->getRow($select);
            if (!$parent) return null;
            $field = $expr->getField();
            if (is_string($field)) {
                return $parent->$field;
            } else {
                return $this->getExprValue($parent, $field);
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Concat) {
            $ret = '';
            foreach ($expr->getExpressions() as $e) {
                if ($e instanceof Kwf_Model_Select_Expr_Interface) {
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
        } else if ($expr instanceof Kwf_Model_Select_Expr_String) {
            return $expr->getString();
        } else if ($expr instanceof Kwf_Model_Select_Expr_Boolean) {
            return $expr->getValue();
        } else if ($expr instanceof Kwf_Model_Select_Expr_Integer) {
            return $expr->getValue();
        } else if ($expr instanceof Kwf_Model_Select_Expr_StrPad) {
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
            if ($expr->getPadType() == Kwf_Model_Select_Expr_StrPad::LEFT) {
                $padType = STR_PAD_LEFT;
            } else if ($expr->getPadType() == Kwf_Model_Select_Expr_StrPad::RIGHT) {
                $padType = STR_PAD_RIGHT;
            }
            return str_pad($v, $expr->getPadLength(), $expr->getPadStr(), $padType);
        } else if ($expr instanceof Kwf_Model_Select_Expr_Date_Year) {
            $field = $expr->getField();
            $format = $expr->getFormat();
            if (is_array($row)) {
                $v = $row[$field];
            } else {
                $v = $row->$field;
            }
            return date($format, strtotime($v));
        } else if ($expr instanceof Kwf_Model_Select_Expr_Date_Format) {
            $field = $expr->getField();
            $format = $expr->getFormat();
            if (is_array($row)) {
                $v = $row[$field];
            } else {
                $v = $row->$field;
            }
            return date($format, strtotime($v));
        } else if ($expr instanceof Kwf_Model_Select_Expr_Field) {
            $f = $expr->getField();
            if (is_array($row)) {
                return $row[$f];
            } else {
                return $row->$f;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_PrimaryKey) {
            $f = $this->getPrimaryKey();
            if (is_array($row)) {
                return $row[$f];
            } else {
                return $row->$f;
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_SumFields) {
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
                } else if ($f instanceof Kwf_Model_Select_Expr_Interface) {
                    $ret += $this->getExprValue($row, $f);
                } else {
                    throw new Kwf_Exception_NotYetImplemented();
                }
            }
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_If) {
            if ($this->getExprValue($row, $expr->getIf())) {
                return $this->getExprValue($row, $expr->getThen());
            } else {
                return $this->getExprValue($row, $expr->getElse());
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_IsNull) {
            if ($expr->getField() instanceof Kwf_Model_Select_Expr_Interface) {
                $rowValue = $this->getExprValue($row, $expr->getField());
            } else {
                $rowValue = $row->{$expr->getField()};
            }
            return is_null($rowValue);
        } else if ($expr instanceof Kwf_Model_Select_Expr_Not) {
            return !$this->getExprValue($row, $expr->getExpression());
        } else if ($expr instanceof Kwf_Model_Select_Expr_Position) {
            $f = $expr->getField();
            $s = $this->select();
            foreach ($expr->getGroupBy() as $i) {
                $s->whereEquals($i, $row->$i);
            }
            if ($expr->getDirection() == Kwf_Model_Select_Expr_Position::DIRECTION_ASC) {
                $s->where(new Kwf_Model_Select_Expr_Lower($f, $row->$f));
            } else {
                $s->where(new Kwf_Model_Select_Expr_Higher($f, $row->$f));
            }
            return $this->countRows($s)+1;
        } elseif ($expr instanceof Kwf_Model_Select_Expr_Date_Age) {
            $f = $expr->getField();
            if (!$row->$f) return null;
            $timeFrom = strtotime($row->$f);
            $timeTo = $expr->getDate()->getTimestamp();
            $ret = (date('Y', $timeTo) - date('Y', $timeFrom))
                - intval((date('md', $timeTo) < date('md', $timeFrom)));
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Divide) {
            $ret = null;
            foreach ($expr->getExpressions() as $e) {
                $value = $this->getExprValue($row, $e);
                if ($ret === null) {
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
                if ($ret === null) {
                    $ret = $value;
                } else {
                    $ret *= $value;
                }
            }
            if (!$ret) $ret = 0;
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Subtract) {
            $ret = null;
            foreach ($expr->getExpressions() as $e) {
                $value = $this->getExprValue($row, $e);
                if ($ret === null) {
                    $ret = $value;
                } else {
                    $ret -= $value;
                }
            }
            if (!$ret) $ret = 0;
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Add) {
            $ret = 0;
            foreach ($expr->getExpressions() as $e) {
                $ret += $this->getExprValue($row, $e);
            }
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_And) {
            foreach ($expr->getExpressions() as $e) {
                if (!$this->getExprValue($row, $e)) { return false; }
            }
            return true;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Or) {
            foreach ($expr->getExpressions() as $e) {
                $value = $this->getExprValue($row, $e);
                if ($this->getExprValue($row, $e)) { return true; }
            }
            return false;
        } else if ($expr instanceof Kwf_Model_Select_Expr_CompareField_Abstract) {
            $value = $expr->getFormattedValue();
            if ($value instanceof Kwf_Model_Select_Expr_Interface) {
                $value = $this->getExprValue($row, $value);
            }
            if ($expr->getField() instanceof Kwf_Model_Select_Expr_Interface) {
                $rowValue = $this->getExprValue($row, $expr->getField());
            } else {
                $rowValue = $row->{$expr->getField()};
            }

            if ($expr instanceof Kwf_Model_Select_Expr_Higher) {
                return (!$value || $rowValue > $value);
            } else if ($expr instanceof Kwf_Model_Select_Expr_Lower) {
                return (!$value || $rowValue < $value);
            } else if ($expr instanceof Kwf_Model_Select_Expr_HigherEqual) {
                return (!$value || $rowValue >= $value);
            } else if ($expr instanceof Kwf_Model_Select_Expr_Equal) {
                return ($rowValue == $value);
            } else if ($expr instanceof Kwf_Model_Select_Expr_NotEquals) {
                return ($rowValue != $value);
            } else if ($expr instanceof Kwf_Model_Select_Expr_LowerEqual) {
                return (!$value || $rowValue <= $value);
            } else {
                throw new Kwf_Exception_NotYetImplemented(
                    "CompareField-Expression '".(is_string($expr) ? $expr : get_class($expr))."' is not yet implemented"
                );
            }
        } else if ($expr instanceof Kwf_Model_Select_Expr_Parent_Contains) {
            if (!$row instanceof Kwf_Model_Row_Interface) {
                $row = $this->getRow($row[$this->getPrimaryKey()]);
            }
            $refModel = $this->getReferencedModel($expr->getParent());
            $ref = $this->getReference($expr->getParent());
            return in_array($row->{$ref['column']}, $refModel->getIds($expr->getSelect()));
        } else {
            throw new Kwf_Exception_NotYetImplemented(
                "Expression '".(is_string($expr) ? $expr : get_class($expr))."' is not yet implemented"
            );
        }
    }

    public function evaluateExpr(Kwf_Model_Select_Expr_Interface $expr, Kwf_Model_Select $select = null)
    {
        $rows = $this->getRows($select);
        return self::_evaluateExprForRowset($rows, $expr);
    }

    private static function _evaluateExprForRowset($rowset, Kwf_Model_Select_Expr_Interface $expr)
    {
        if ($expr instanceof Kwf_Model_Select_Expr_Count) {
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
        } else if ($expr instanceof Kwf_Model_Select_Expr_Sum) {
            $f = $expr->getField();
            $ret = 0;
            foreach ($rowset as $r) {
                $ret += $r->$f;
            }
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Max) {
            $f = $expr->getField();
            $ret = $rowset->current()->$f;
            foreach ($rowset as $r) {
                $ret = max($ret, $r->$f);
            }
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_Min) {
            $f = $expr->getField();
            $ret = $rowset->current()->$f;
            foreach ($rowset as $r) {
                $ret = min($ret, $r->$f);
            }
            return $ret;
        } else if ($expr instanceof Kwf_Model_Select_Expr_GroupConcat) {
            $f = $expr->getField();
            $orderField = $expr->getOrderField();
            $ret = array();

            if ($orderField) {
                $orderFieldValue = $orderField['field'];
                $orderFieldDirection = ($orderField['direction'] == 'DESC') ? SORT_DESC : SORT_ASC;

                $rowData = array();
                foreach ($rowset as $r) {
                    $rowData[] = array(
                        $f => $r->$f,
                        $orderFieldValue => $r->$orderFieldValue
                    );
                }

                $orderFieldValues = array();

                foreach ($rowData as $key => $data) {
                    $orderFieldValues[$key] = $data[$orderFieldValue];
                }
                array_multisort($orderFieldValues, $orderFieldDirection, $rowData);
                foreach ($rowData as $r) {
                    $ret[] = $r[$f];
                }
            } else {
                foreach ($rowset as $r) {
                    $ret[] = $r->$f;
                }
            }

            return implode($expr->getSeparator(), $ret);
        } else if ($expr instanceof Kwf_Model_Select_Expr_Field) {
            if (!count($rowset)) {
                return null;
            }
            $f = $expr->getField();
            return $rowset->current()->$f;
        } else {
            throw new Kwf_Exception_NotYetImplemented("support for ".get_class($expr)." is not yet implemented");
        }
    }

    public function updateRow(array $data)
    {
        $row = $this->getRow($data[$this->getPrimaryKey()]);
        if (!$row) {
            throw new Kwf_Exception("Can't update row, row not found");
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
    public function dependentModelRowUpdated(Kwf_Model_Row_Abstract $row, $action)
    {
    }

    /**
     * @internal
     */
    public function childModelRowUpdated(Kwf_Model_Row_Abstract $row, $action)
    {
    }

    /**
     * Kann zum Speicher-Sparen aufgerufen werden
     * @internal
     * @deprecated
     */
    public function clearRows()
    {
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

    public function freeMemory()
    {
        foreach ($this->_rows as $row) {
            if (is_object($row)) $row->freeMemory();
        }
        $this->_rows = array();
    }

    public static function clearAllRows()
    {
        foreach (self::$_allInstances as $i) {
            $i->freeMemory();
        }
    }

    public function fetchColumnByPrimaryId($column, $id)
    {
        $row = $this->getRow($id);
        if (!$row) return null;
        return $row->$column;
    }

    public function fetchColumnsByPrimaryId(array $columns, $id)
    {
        $row = $this->getRow($id);
        if (!$row) return array();
        $ret = array();
        foreach ($columns as $c) {
            $ret[$c] = $row->$c;
        }
        return $ret;
    }

    public function getColumnMapping($mapping, $column)
    {
        $models = array($this);
        $models = array_merge($models, $this->_proxyContainerModels);
        foreach ($models as $model) {
            foreach ($model->_columnMappings as $map=>$columns) {
                do {
                    if (isset($model->_columnMappings[$map]) &&
                        array_key_exists($column, $model->_columnMappings[$map])
                    ) {
                        return $model->_columnMappings[$map][$column];
                    }
                } while ($map = get_parent_class($map));
            }
        }
        throw new Kwf_Exception("unknown mapping column: '$column' for mapping '$mapping' in model '".get_class($model)."'");
    }

    public function getColumnMappings($mapping)
    {
        $models = array($this);
        $models = array_merge($models, $this->_proxyContainerModels);

        foreach ($models as $model) {

            foreach ($model->_columnMappings as $map=>$columns) {
                $curMap = $map;
                do {
                    if ($curMap == $mapping) {
                        return $model->_columnMappings[$map];
                    }
                } while ($curMap = get_parent_class($curMap));
            }

        }

        throw new Kwf_Exception("unknown mapping '$mapping' for '".get_class($this)."'");
    }

    public function hasColumnMappings($mapping)
    {
        $models = array($this);
        $models = array_merge($models, $this->_proxyContainerModels);

        foreach ($models as $model) {

            foreach ($model->_columnMappings as $map=>$columns) {
                do {
                    if ($map == $mapping) {
                        return true;
                    }
                } while ($map = get_parent_class($map));
            }
        }
        return false;
    }
    
    public function hasDeletedFlag()
    {
        return $this->_hasDeletedFlag;
    }

    public static function convertValueToType($value, $type)
    {
        if (is_null($value)) return $value;

        if ($type == Kwf_Model_Abstract::TYPE_STRING) {
            $value = (string)$value;
        } else if ($type == Kwf_Model_Abstract::TYPE_BOOLEAN) {
            $value = (bool)$value;
        } else if ($type == Kwf_Model_Abstract::TYPE_INTEGER) {
            $value = (int)$value;
        } else if ($type == Kwf_Model_Abstract::TYPE_DATE) {
            $value = (string)$value;
        } else if ($type == Kwf_Model_Abstract::TYPE_DATETIME) {
            $value = (string)$value;
        } else if ($type == Kwf_Model_Abstract::TYPE_FLOAT) {
            $value = (float)$value;
        }
        return $value;
    }

    public function getEventSubscribers()
    {
        return array();
    }

    public function setFactoryConfig(array $factoryConfig)
    {
        $this->_factoryConfig = $factoryConfig;
    }

    public function getFactoryId()
    {
        $c = $this->getFactoryConfig();
        if (!$c) return null;
        return $c['id'];
    }

    public function getFactoryConfig()
    {
        if ($this->_factoryConfig && !isset($this->_factoryConfig['id'])) {
            $this->_factoryConfig = call_user_func(array('Kwf_Model_Factory_'.$this->_factoryConfig['type'], 'processConfig'), $this->_factoryConfig);
        }
        return $this->_factoryConfig;
    }

    private static function _findAllInstancesProcessModel(&$ret, $model)
    {
        $model = self::getInstance($model);
        if (isset($ret[$model->getFactoryId()])) {
            return;
        }
        $ret[$model->getFactoryId()] = $model;

        if ($model instanceof Kwf_Model_Proxy) {
            self::_findAllInstancesProcessModel($ret, $model->getProxyModel());
        } else if ($model instanceof Kwf_Model_Union) {
            foreach ($model->getUnionModels() as $subModel) {
                self::_findAllInstancesProcessModel($ret, $subModel);
            }
        }

        foreach ($model->getDependentModels() as $m) {
            self::_findAllInstancesProcessModel($ret, $m);
        }
        foreach ($model->getSiblingModels() as $m) {
            self::_findAllInstancesProcessModel($ret, $m);
        }
        foreach ($model->getReferences() as $rule) {
            $m = $model->getReferencedModel($rule);
            self::_findAllInstancesProcessModel($ret, $m);
        }
    }

    /**
     * Try to find all used models in the current app
     */
    public static function findAllInstances()
    {
        $ret = array();
        foreach (glob('models/*.php') as $m) {
            $m = str_replace('/', '_', substr($m, 7, -4));
            $reflectionClass = new ReflectionClass($m);
            if (!$reflectionClass->isAbstract() && is_instance_of($m, 'Kwf_Model_Interface')) {
                self::_findAllInstancesProcessModel($ret, $m);
            }
        }

        if (Kwf_Config::getValue('user.model')) {
            self::_findAllInstancesProcessModel($ret, Kwf_Config::getValue('user.model'));
        }

        foreach (Kwc_Abstract::getComponentClasses() as $componentClass) {
            $cls = strpos($componentClass, '.') ? substr($componentClass, 0, strpos($componentClass, '.')) : $componentClass;
            $m = call_user_func(array($cls, 'createOwnModel'), $componentClass);
            if ($m) self::_findAllInstancesProcessModel($ret, $m);

            $m = call_user_func(array($cls, 'createChildModel'), $componentClass);
            if ($m) self::_findAllInstancesProcessModel($ret, $m);

            foreach (Kwc_Abstract::getSetting($componentClass, 'generators') as $g) {
                if (isset($g['model'])) {
                    self::_findAllInstancesProcessModel($ret, $g['model']);
                }
            }
        }

        //hardcoded models that always exist
        self::_findAllInstancesProcessModel($ret, 'Kwf_Util_Model_Welcome');
        self::_findAllInstancesProcessModel($ret, 'Kwf_Util_Model_Redirects');
        return $ret;
    }

    public function getUpdates()
    {
        if ($this->_factoryConfig['type'] == 'ClassName') {
            $id = $this->_factoryConfig['id'];
            if (strpos($id, '_') === false) {
                $classPrefix = 'Update_'.$id;
            } else {
                $classPrefix = substr($id, 0, strrpos($id, '_')).'_Update_'.substr($id, strrpos($id, '_')+1);
            }
            return Kwf_Util_Update_Helper::getUpdatesForDir($classPrefix);
        } else {
            return array();
        }
    }
}
