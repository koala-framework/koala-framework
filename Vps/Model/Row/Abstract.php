<?php
abstract class Vps_Model_Row_Abstract implements Vps_Model_Row_Interface, Serializable
{
    private $_skipFilters = false; //für saveSkipFilters
    /**
     * @var Vps_Model_Abstract
     **/
    protected $_model;
    private $_internalId;
    protected $_siblingRows;
    protected $_exprValues = array();
    private $_dirtyColumns = array();
    static private $_internalIdCounter = 0;

    //damit im save() die childRows autom. mitgespeichert werden können
    private $_childRows = array();
    private $_isDeleted = false;

    public function __construct(array $config)
    {
        if (isset($config['siblingRows'])) {
            $this->_siblingRows = (array)$config['siblingRows'];
        }

        if (isset($config['exprValues'])) {
            $this->_exprValues = (array)$config['exprValues'];
        }

        $this->_model = $config['model'];
        $this->_internalId = self::$_internalIdCounter++;

        $this->_init();

        //Vps_Benchmark::count('Model_Row', get_class($this->_model).' '.$this->{$this->_model->getPrimaryKey()});
    }

    public function serialize()
    {
        if (Vps_Model_Abstract::getInstance(get_class($this->getModel())) !== $this->getModel()) {
            throw new Vps_Exception("You can only serialize rows of models that where created with Vps_Model_Abstract::getInstance()");
        }
        $data = array(
            'model' => get_class($this->getModel()),
            'siblingRows' => $this->_siblingRows
        );
        return serialize($data);
    }

    public function unserialize($str)
    {
        $data = unserialize($str);
        $this->_siblingRows = $data['siblingRows'];
        $this->_model = Vps_Model_Abstract::getInstance($data['model']);
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
                if ($m instanceof Vps_Model_SubModel_Interface) {
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
            throw new Vps_Exception("Expr Columns are read only");
        }
        foreach ($this->_getSiblingRows() as $r) {
            if ($r->hasColumn($name)) {
                unset($r->$name);
                return;
            }
        }
        throw new Vps_Exception("Invalid column '$name'");
    }

    public function __get($name)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            if (!isset($this->_exprValues[$name])) {
                $this->_exprValues[$name] = $this->_model->getExprValue($this, $name);
            }
            return $this->_exprValues[$name];
        }
        foreach ($this->_getSiblingRows() as $r) {
            if ($r->hasColumn($name)) {
                return $r->$name;
            }
        }
        throw new Vps_Exception("Invalid column '$name'");
    }

    public function __set($name, $value)
    {
        if (in_array($name, $this->_model->getExprColumns())) {
            throw new Vps_Exception("Expr Columns are read only");
        }
        if ($this->_model->getOwnColumns() && !in_array($name, $this->_model->getOwnColumns())) {
            foreach ($this->_getSiblingRows() as $r) {
                if ($r->hasColumn($name)) {
                    $r->$name = $value;
                    return;
                }
            }
            throw new Vps_Exception("Invalid column '$name'");
        }
    }

    protected function _postSet($name, $value)
    {
        if ($name == $this->_getPrimaryKey()) {
            foreach ($this->_getSiblingRows() as $k=>$r) {
                if (!$r->getModel() instanceof Vps_Model_SubModel_Interface) {
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
        if (!in_array($column, $this->_dirtyColumns)) {
            $this->_dirtyColumns[] = $column;
        }
    }

    protected function _resetDirty()
    {
        $this->_dirtyColumns = array();
    }

    /**
     * Ob die Row seblst dirty ist
     */
    protected function _isDirty()
    {
        return !empty($this->_dirtyColumns);
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
        return false;
    }

    /**
     * dirty columns der row und der sibling rows
     */
    public function getDirtyColumns()
    {
        $ret = $this->_dirtyColumns;
        foreach ($this->_getSiblingRows() as $r) {
            $ret = array_merge($ret, $r->getDirtyColumns());
        }
        return $ret;
    }

    public function save()
    {
        foreach ($this->_getSiblingRows() as $k=>$r) {
            if (!$r->getModel() instanceof Vps_Model_SubModel_Interface) {
                $ref = $r->getModel()->getReferenceByModelClass(get_class($this->_model), $k);
                if (!$r->{$ref['column']}) {
                    $r->{$ref['column']} = $this->{$this->_getPrimaryKey()};
                }
            }
            $r->save();
        }
        return null;
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

    public function getChildRows($rule, $select = array())
    {
        if ($rule instanceof Vps_Model_Abstract) {
            $m = $rule;
            $dependentOf = $this->_model;
        } else {
            $dependent = $this->_model->getDependentModelWithDependentOf($rule);
            $m = $dependent['model'];
            $dependentOf = $dependent['dependentOf'];
        }

        if ($m instanceof Vps_Model_RowsSubModel_Interface) {
            $ret = $m->getRowsByParentRow($this, $select);
        } else {
            if (!$select instanceof Vps_Model_Select) {
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
            $this->_childRows[] = $r;
        }
        $ret->rewind();
        return $ret;
    }

    public function createChildRow($rule, array $data = array())
    {
        if ($rule instanceof Vps_Model_Abstract) {
            $m = $rule;
        } else {
            $m = $this->_model->getDependentModel($rule);
        }

        if ($m instanceof Vps_Model_RowsSubModel_Interface) {
            $ret = $m->createRowByParentRow($this, $data);
        } else {
            $ret = $m->createRow();
            $ref = $m->getReferenceByModelClass(get_class($this->_model), null);
            $ret->{$ref['column']} = $this->{$this->_getPrimaryKey()};
        }
        $this->_childRows[] = $ret;
        return $ret;
    }

    public function getParentRow($rule)
    {
        $ref = $this->_model->getReference($rule);
        if ($ref === Vps_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
            if (!($this instanceof  Vps_Model_RowsSubModel_Row_Interface)) {
                throw new Vps_Exception("row '".get_class($this)."' must implement Vps_Model_RowsSubModel_Row_Interface");
            }
            return $this->getSubModelParentRow();
        }
        if (!isset($ref['column'])) {
            throw new Vps_Exception("column for reference '$rule' not set");
        }
        $id = $this->{$ref['column']};
        if (!$id) return null;
        if (isset($ref['refModelClass'])) {
            $refModel = Vps_Model_Abstract::getInstance($ref['refModelClass']);
        } else if (isset($ref['refModel'])) {
            $refModel = $ref['refModel'];
        } else {
            throw new Vps_Exception("refModel or refModelClass for reference '$rule' not set");
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
        } catch (Vps_Exception $e) {}
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
        throw new Vps_Exception('Either override __toString() or define $_toStringField in Model '.get_class($this->getModel()).'');
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
            if ($row->getModel() instanceof Vps_Model_RowsSubModel_Interface) {
                //FieldRows müssen *vor* der row gespeichert werden, damit das data Feld die korrekten Werte hat
                $row->save();
            }
        }
        $this->_callObserver('save');
    }

    protected function _callObserver($fn)
    {
        Vps_Component_ModelObserver::getInstance()->add($fn, $this);
    }

    protected function _afterSave()
    {
        foreach ($this->_childRows as $row) {
            if ($row->_isDeleted) continue;
            if (!($row->getModel() instanceof Vps_Model_RowsSubModel_Interface)) {
                if (!$row->{$row->_getPrimaryKey()}) {
                    //Tabellen Relationen müssen *nach* der row gespeichert werden,
                    //da beim hinzufügen die id noch nicht verfügbar ist
                    $ref = $row->getModel()->getReferenceByModelClass(get_class($this->_model), null);
                    $row->{$ref['column']} = $this->{$this->_getPrimaryKey()};
                }
                if ($row->_isDirty()) {
                    $row->save();
                }
            }
        }
        $this->_updateFilters(true);

        $this->_callReferencedModelsRowUpdated('save');
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
            if ($this->getModel()->getReference($refName) === Vps_Model_RowsSubModel_Interface::SUBMODEL_PARENT) {
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
        $this->_callObserver('update');
    }

    protected function _afterUpdate()
    {
        $this->_updateFilters(true);
    }

    protected function _beforeInsert()
    {
        $this->_callObserver('insert');
    }

    protected function _afterInsert()
    {
    }

    protected function _beforeDelete()
    {
        $filters = $this->getModel()->getFilters();
        foreach($filters as $k=>$f) {
            if ($f instanceof Vps_Filter_Row_Abstract) {
                $f->onDeleteRow($this);
            }
        }
        $this->_callObserver('delete');

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
            if ($f instanceof Vps_Filter_Row_Abstract) {
                if ($f->skipFilter($this)) continue;
                if ($f->filterAfterSave() != $filterAfterSave) continue;
                $this->$k = $f->filter($this);
            } else {
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
    protected final function _duplicateDependentModel($newRow, $rule)
    {
        $rowset = $this->getChildRows($rule);
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
}
