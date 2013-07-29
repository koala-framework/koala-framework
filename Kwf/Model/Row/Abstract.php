<?php
/**
 * @package Model
 * @internal
 */
abstract class Kwf_Model_Row_Abstract implements Kwf_Model_Row_Interface, Serializable
{
    private $_skipFilters = false; //für saveSkipFilters
    /**
     * @var Kwf_Model_Abstract
     **/
    protected $_model;
    private $_internalId;
    protected $_siblingRows;
    protected $_exprValues = array();
    private $_cleanData = array();
    static private $_internalIdCounter = 0;

    //damit im save() die childRows autom. mitgespeichert werden können
    private $_childRows = array();
    private $_isDeleted = false;
    private $_siblingRowsWhereSet = false;  //for free memory

    //public static $objectsCount;
    //public static $objectsByModel = array();
    //private $countKey;

    public function __construct(array $config)
    {
        //self::$objectsCount++;
        //$this->countKey = get_class($config['model']);
        //if ($this->countKey == 'Kwf_Model_Db') {
        //    $this->countKey = $config['model']->getTableName();
        //}
        //if (!isset(self::$objectsByModel[$this->countKey])) self::$objectsByModel[$this->countKey] = 0;
        //self::$objectsByModel[$this->countKey]++;

        if (isset($config['siblingRows'])) {
            $this->_siblingRows = (array)$config['siblingRows'];
            $this->_siblingRowsWhereSet = true;
        }

        if (isset($config['exprValues'])) {
            $this->_exprValues = (array)$config['exprValues'];
        }

        $this->_model = $config['model'];
        $this->_internalId = self::$_internalIdCounter++;

        $this->_init();

        //Kwf_Benchmark::count('Model_Row', get_class($this->_model).' '.$this->{$this->_model->getPrimaryKey()});
    }

    public function __destruct()
    {
        //self::$objectsCount--;
        //self::$objectsByModel[$this->countKey]--;
        //if (!self::$objectsByModel[$this->countKey]) unset(self::$objectsByModel[$this->countKey]);
    }

    public function serialize()
    {
        if (Kwf_Model_Abstract::getInstance(get_class($this->getModel())) !== $this->getModel()) {
            throw new Kwf_Exception("You can only serialize rows of models that where created with Kwf_Model_Abstract::getInstance()");
        }
        $data = array(
            'model' => get_class($this->getModel()),
            'siblingRows' => $this->_siblingRows
        );
        return serialize($data);
    }

    public function unserialize($str)
    {
        //self::$objectsCount++;

        $data = unserialize($str);
        $this->_siblingRows = $data['siblingRows'];
        $this->_model = Kwf_Model_Abstract::getInstance($data['model']);
        $this->_internalId = self::$_internalIdCounter++;
    }

    protected function _init()
    {
    }

    public function getInternalId()
    {
        return $this->_internalId;
    }

    public function setSiblingRows(array $rows)
    {
        $this->_siblingRowsWhereSet = true;
        $this->_siblingRows = $rows;
        return $this;
    }

    public function getSiblingRow($rule)
    {
        $rows = $this->_getSiblingRows();
        return $rows[$rule];
    }

    protected function _getSiblingRows()
    {
        if (!isset($this->_siblingRows)) {
            $this->_siblingRows = array();
            foreach ($this->_model->getSiblingModels() as $k=>$m) {
                if ($m instanceof Kwf_Model_SubModel_Interface) {
                    $r = $m->getRowBySiblingRow($this);
                } else {
                    $ref = $m->getReferenceByModelClass(get_class($this->_model), $k);
                    $r = null;
                    if ($this->{$this->_getPrimaryKey()}) {
                        $r = $m->getRow(array('equals'=>array($ref['column']=>$this->{$this->_getPrimaryKey()})));
                    }
                    if (!$r) {
                        $r = $m->createRow();
                        $r->{$ref['column']} = $this->{$this->_getPrimaryKey()};
                    }
                }
                $this->_siblingRows[$k] = $r;
            }
        }
        return $this->_siblingRows;
    }

    protected function _transformColumnName($name)
    {
        return $this->_model->transformColumnName($name);
    }

    public function __isset($name)
    {
        return $this->hasColumn($name);
    }

    public function __unset($name)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            throw new Kwf_Exception("Expr Columns are read only");
        }
        foreach ($this->_getSiblingRows() as $r) {
            if ($r->hasColumn($name)) {
                unset($r->$name);
                return;
            }
        }
        throw new Kwf_Exception("Invalid column '$name' for model '".get_class($this->getModel())."'");
    }

    public function __get($name)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            if (!array_key_exists($name, $this->_exprValues)) {
                $this->_exprValues[$name] = $this->_model->getExprValue($this, $name);
            }
            return $this->_exprValues[$name];
        }
        foreach ($this->_getSiblingRows() as $r) {
            if ($r->hasColumn($name)) {
                return $r->$name;
            }
        }
        throw new Kwf_Exception("Invalid column '$name' for model '".get_class($this->getModel())."'");
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            throw new Kwf_Exception("Expr Columns are read only");
        }
        if ($this->_model->getOwnColumns() && !in_array($name, $this->_model->getOwnColumns())) {
            foreach ($this->_getSiblingRows() as $r) {
                if ($r->hasColumn($name)) {
                    $r->$name = $value;
                    return;
                }
            }
            throw new Kwf_Exception("Invalid column '$name' for model '".get_class($this->getModel())."'");
        }
    }

    protected function _postSet($name, $value)
    {
        if ($name == $this->_getPrimaryKey()) {
            foreach ($this->_getSiblingRows() as $k=>$r) {
                if (!$r->getModel() instanceof Kwf_Model_SubModel_Interface) {
                    $ref = $r->getModel()->getReferenceByModelClass(get_class($this->_model), $k);
                    $r->{$ref['column']} = $value;
                }
            }
        }
    }

    /**
     * Speichert in jedem Fall, auch wenn sich keine daten geändert haben.
     */
    final public function forceSave()
    {
        $this->_setDirty($this->_getPrimaryKey());
        return $this->save();
    }

    protected function _setDirty($column)
    {
        if (!array_key_exists($column, $this->_cleanData)) {
            $this->_cleanData[$column] = $this->$column;
        }
    }

    protected function _resetDirty()
    {
        $this->_cleanData = array();
        foreach ($this->_getSiblingRows() as $r) {
            $r->_resetDirty();
        }
    }

    /**
     * Ob die Row seblst dirty ist
     */
    protected function _isDirty()
    {
        return !empty($this->_cleanData);
    }

    /**
     * Ob die Row oder eine sibling row dirty ist
     */
    public final function isDirty()
    {
        if ($this->_isDirty()) return true;
        foreach ($this->_getSiblingRows() as $r) {
            if ($r->_isDirty()) return true;
        }
        foreach ($this->_childRows as $row) {
            if ($row->isDirty()) return true;
        }
        return false;
    }

    /**
     * dirty columns der row und der sibling rows
     */
    public function getDirtyColumns()
    {
        $ret = array_keys($this->_cleanData);
        foreach ($this->_getSiblingRows() as $r) {
            $ret = array_merge($ret, $r->getDirtyColumns());
        }
        return $ret;
    }

    /**
     * Returns the original value of a column, like it exists in the data source.
     *
     * After save() it will return the new value.
     */
    public function getCleanValue($name)
    {
        if (array_key_exists($name, $this->_cleanData)) {
            return $this->_cleanData[$name];
        }
        foreach ($this->_getSiblingRows() as $r) {
            if (array_key_exists($name, $r->_cleanData)) {
                return $r->_cleanData[$name];
            }
        }
        return $this->__get($name);
    }

    /**
     * Nötig wenn man in einer Form Cards hat (jede Card mit eigener Form) und die
     * Card-Forms als Sibling gespeichert werden. Da will man dann immer nur ein
     * Sibling speichern (das von der ausgewählten Card), aber beim load() will
     * man alle Siblings dabei haben. Wenn man dann diese Funktion überschreibt
     * kann man nur ein Sibling zurückgeben und genau das wird dann beim speichern
     * verwendet und beim load() wird _getSiblingRows() direkt verwendet.
     */
    protected function _getSiblingRowsForSave()
    {
        return $this->_getSiblingRows();
    }

    //calling _resetDirty has to be done *after* all saving finished (proxies, siblings)
    //to have cleanData available in eg. _afterSave
    //TODO: investigate if it would make sense to move _afterSave also out to have it consistent across proxies&siblings
    protected function _saveWithoutResetDirty()
    {
        foreach ($this->_getSiblingRowsForSave() as $k=>$r) {
            if (!$r->getModel() instanceof Kwf_Model_SubModel_Interface) {
                $ref = $r->getModel()->getReferenceByModelClass(get_class($this->_model), $k);
                if (!$r->{$ref['column']}) {
                    $r->{$ref['column']} = $this->{$this->_getPrimaryKey()};
                }
            }
            $r->_saveWithoutResetDirty();
        }
        return null;
    }

    //override _saveWithoutResetDirty instead, or better _afterSave/_beforeSave
    public final function save()
    {
        $ret = $this->_saveWithoutResetDirty();

        $this->_resetDirty();

        return $ret;
    }

    protected function _postInsert()
    {
    }

    public function delete()
    {
    }

    public function getModel()
    {
        return $this->_model;
    }

    protected function _getPrimaryKey()
    {
        return $this->_model->getPrimaryKey();
    }

    public function countChildRows($rule, $select = array())
    {
        if ($rule instanceof Kwf_Model_Abstract) {
            $m = $rule;
            $dependentOf = $this->_model;
        } else {
            $dependent = $this->_model->getDependentModelWithDependentOf($rule);
            $m = $dependent['model'];
            $dependentOf = $dependent['dependentOf'];
        }

        if ($m instanceof Kwf_Model_RowsSubModel_Interface) {
            //could be improved
            return count($m->getRowsByParentRow($this, $select));
        } else {
            if (!$select instanceof Kwf_Model_Select) {
                $select = $m->select($select);
            } else {
                $select = clone $select; //nicht select objekt ändern
            }
            $ref = $m->getReferenceByModelClass(get_class($dependentOf), isset($dependent['rule']) ? $dependent['rule'] : null);
            if (!$this->{$this->_getPrimaryKey()}) {
                return 0;
            }
            $select->whereEquals($ref['column'], $this->{$this->_getPrimaryKey()});
            return $m->countRows($select);
        }
    }

    public function getChildRows($rule, $select = array())
    {
        if ($rule instanceof Kwf_Model_Abstract) {
            $m = $rule;
            $dependentOf = $this->_model;
        } else {
            $dependent = $this->_model->getDependentModelWithDependentOf($rule);
            $m = $dependent['model'];
            $dependentOf = $dependent['dependentOf'];
        }

        if ($m instanceof Kwf_Model_RowsSubModel_Interface) {
            $ret = $m->getRowsByParentRow($this, $select);
        } else {
            if (!$select instanceof Kwf_Model_Select) {
                $select = $m->select($select);
            } else {
                $select = clone $select; //nicht select objekt ändern
            }
            $ref = $m->getReferenceByModelClass(get_class($dependentOf), isset($dependent['rule']) ? $dependent['rule'] : null);
            if (!$this->{$this->_getPrimaryKey()}) {
                return array();
            }
            $select->whereEquals($ref['column'], $this->{$this->_getPrimaryKey()});
            $ret = $m->getRows($select);
        }
        foreach ($ret as $r) {
            if (!in_array($r, $this->_childRows, true)) $this->_childRows[] = $r;
        }
        $ret->rewind();
        return $ret;
    }

    public function createChildRow($rule, array $data = array())
    {
        if ($rule instanceof Kwf_Model_Abstract) {
            $m = $rule;
        } else {
            $m = $this->_model->getDependentModel($rule);
        }

        if ($m instanceof Kwf_Model_RowsSubModel_Interface) {
            $ret = $m->createRowByParentRow($this, $data);
        } else {
            $ret = $m->createRow($data);
            $ref = $m->getReferenceByModelClass(get_class($this->_model), null);
            $ret->{$ref['column']} = $this->{$this->_getPrimaryKey()};
        }
        $this->_childRows[] = $ret;
        return $ret;
    }

    public function getParentRow($rule)
    {
        $ref = $this->_model->getReference($rule);
        if ($ref === Kwf_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
            if (!($this instanceof  Kwf_Model_RowsSubModel_Row_Interface)) {
                throw new Kwf_Exception("row '".get_class($this)."' must implement Kwf_Model_RowsSubModel_Row_Interface");
            }
            return $this->getSubModelParentRow();
        }
        if (!isset($ref['column'])) {
            throw new Kwf_Exception("column for reference '$rule' not set");
        }
        $id = $this->{$ref['column']};
        if (!$id) return null;
        if (isset($ref['refModelClass'])) {
            $refModel = Kwf_Model_Abstract::getInstance($ref['refModelClass']);
        } else if (isset($ref['refModel'])) {
            $refModel = $ref['refModel'];
        } else {
            throw new Kwf_Exception("refModel or refModelClass for reference '$rule' not set");
        }
        return $refModel->getRow($id);
    }

    public function toDebug()
    {
        $i = get_class($this);
        try {
            if (method_exists($this, '__toString')) {
                $i .= " (".$this->__toString().")\n";
            }
        } catch (Kwf_Exception $e) {}
        $ret = print_r($this->toArray(), true);
        $ret = preg_replace('#^Array#', $i, $ret);
        $ret .= "Model: ".get_class($this->getModel());
        $ret = "<pre>$ret</pre>";
        return $ret;
    }

    public function __toString()
    {
        $field = $this->getModel()->getToStringField();
        if ($field && isset($this->$field)) {
            return $this->$field;
        }
        throw new Kwf_Exception('Either override __toString() or define $_toStringField in Model '.get_class($this->getModel()).'');
    }

    /**
     * Um in Model_Field vor dem speichern den wert setzen zu können
     */
    protected function _beforeSaveSiblingMaster()
    {
        foreach ($this->_getSiblingRows() as $k=>$r) {
            $r->_beforeSaveSiblingMaster();
        }
    }

    protected function _beforeSave()
    {
        $this->_updateFilters(false);
        foreach ($this->_childRows as $row) {
            if ($row->_isDeleted) continue;
            //if (method_exists($row->getModel(), 'createRowByParentRow')) {
            if ($row->getModel() instanceof Kwf_Model_RowsSubModel_Interface) {
                //FieldRows müssen *vor* der row gespeichert werden, damit das data Feld die korrekten Werte hat
                $row->save();
            }
        }
    }

    protected function _callObserver($fn)
    {
        Kwf_Component_ModelObserver::getInstance()->add($fn, $this);
    }

    protected function _afterSave()
    {
        foreach ($this->_childRows as $row) {
            if ($row->_isDeleted) continue;
            if (!($row->getModel() instanceof Kwf_Model_RowsSubModel_Interface)) {
                if (!$row->{$row->_getPrimaryKey()}) {
                    //Tabellen Relationen müssen *nach* der row gespeichert werden,
                    //da beim hinzufügen die id noch nicht verfügbar ist
                    $ref = $row->getModel()->getReferenceByModelClass(get_class($this->_model), null);
                    $row->{$ref['column']} = $this->{$this->_getPrimaryKey()};
                }
                if ($row->isDirty()) {
                    $row->save();
                }
            }
        }
        $this->_updateFilters(true);

        $this->_callReferencedModelsRowUpdated('save');

        $this->_callObserver('save');
    }

    private function _callReferencedModelsRowUpdated($action)
    {
        $called = array();
        foreach ($this->getModel()->getDependentModels() as $depName=>$m) {
            if (!in_array($m, $called, true)) {
                $m->dependentModelRowUpdated($this, $action);
                $called[] = $m;
            }
        }

        $called = array();
        foreach ($this->getModel()->getReferences() as $refName) {
            if ($this->getModel()->getReference($refName) === Kwf_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
                continue;
            }
            $m = $this->getModel()->getReferencedModel($refName);
            if (!in_array($m, $called, true)) {
                $m->childModelRowUpdated($this, $action);
                $called[] = $m;
            }
        }
    }

    protected function _beforeUpdate()
    {
    }

    protected function _afterUpdate()
    {
        $this->_callObserver('update');
        $this->_updateFilters(true);
    }

    protected function _beforeInsert()
    {
    }

    protected function _afterInsert()
    {
        $this->_callObserver('insert');
    }

    protected function _beforeDelete()
    {
        $filters = $this->getModel()->getFilters();
        foreach($filters as $k=>$f) {
            if ($f instanceof Kwf_Filter_Row_Abstract) {
                $f->onDeleteRow($this);
            }
        }
        $this->_callObserver('delete'); //before to have the data still in the row
    }

    protected function _afterDelete()
    {
        $this->_isDeleted = true;
        $this->_callReferencedModelsRowUpdated('delete');
    }

    protected function _updateFilters($filterAfterSave = false)
    {
        if ($this->_skipFilters) return; //für saveSkipFilters

        $filters = $this->getModel()->getFilters();
        foreach($filters as $k=>$f) {
            if ($f instanceof Kwf_Filter_Row_Abstract) {
                if ($f->skipFilter($this, $k)) continue;
                if ($f->filterAfterSave() != $filterAfterSave) continue;
                $this->$k = $f->filter($this);
            } else {
                if ($filterAfterSave) continue;
                $this->$k = $f->filter($this->__toString());
            }
            if ($filterAfterSave) {
                $this->_skipFilters = true;
                $this->save();
            }
        }
    }

    //Speichern und abei die Filter nicht verwenden
    //wird benötigt bei der Nummerierung um eine Endlusschleife zu verhindern
    public function saveSkipFilters()
    {
        $this->_skipFilters = true;
        $this->save();
        $this->_skipFilters = false;
    }

    public function getTable()
    {
        return $this->getModel()->getTable();
    }

    public function toArray()
    {
        $ret = array();
        foreach ($this->_getSiblingRows() as $r) {
            $ret = array_merge($ret, $r->toArray());
        }
        return $ret;
    }

    // ist momentan nur fürs duplicate.
    protected function _toArrayWithoutPrimaryKeys()
    {
        $ret = $this->toArray();
        unset($ret[$this->getModel()->getPrimaryKey()]);
        foreach ($this->_getSiblingRows() as $r) {
            $primaryKey = $r->getModel()->getPrimaryKey();
            if ($primaryKey) {
                unset($ret[$primaryKey]);
            }
        }
        return $ret;
    }

    //kopiert von model, da in row _getSiblingRows überschrieben sein kann
    public function hasColumn($col)
    {
        if (!$this->getModel()->getOwnColumns()) return true;
        if (in_array($col, $this->getModel()->getOwnColumns())) return true;
        if (in_array($col, $this->getModel()->getExprColumns())) return true;
        foreach ($this->_getSiblingRows() as $r) {
            if ($r->hasColumn($col)) return true;
        }
        return false;
    }

    /**
     * Hilfsfunktion die von duplicate aufgerufen werden kann
     */
    protected final function _duplicateDependentModel($newRow, $rule, $select = array())
    {
        $rowset = $this->getChildRows($rule, $select);
        foreach ($rowset as $row) {
            $ref = $row->getModel()->getReferenceByModelClass(get_class($this->getModel()), null);
            $data = array();
            $data[$ref['column']] = $newRow->{$this->_getPrimaryKey()};
            $row->duplicate($data);
        }
    }

    public function duplicate(array $data = array())
    {
        $data = array_merge($this->_toArrayWithoutPrimaryKeys(), $data);
        $new = $this->getModel()->createRow($data);
        $new->save();
        return $new;
    }

    /**
     * @internal
     */
    public function freeMemory()
    {
        if (!$this->_siblingRowsWhereSet) {
            $this->_siblingRows = null;
        }
        $this->_childRows = array();
    }

    public function getByColumnMapping($mapping, $column)
    {
        $c = $this->getModel()->getColumnMapping($mapping, $column);
        return $this->$c;
    }
}
