<?php
/**
 * @package Model
 * @internal
 */
class Kwf_Model_Db_Row extends Kwf_Model_Row_Abstract
{
    protected $_row;

    public function __construct(array $config)
    {
        $this->_row = $config['row'];
        parent::__construct($config);
    }

    function __clone()
    {
        // Force a copy of this->object, otherwise
        // it will point to same object.
        $this->_row = clone $this->_row;
    }

    public function __isset($name)
    {
        $n = $this->_transformColumnName($name);
        if (isset($this->_row->$n)) return true;
        return parent::__isset($name);
    }

    public function __unset($name)
    {
        $n = $this->_transformColumnName($name);
        if (isset($this->_row->$n)) {
            unset($this->_row->$n);
        } else {
            parent::__unset($name);
        }
    }

    public function __get($name)
    {
        $n = $this->_transformColumnName($name);
        if (isset($this->_row->$n)) {
            $value = $this->_row->$n;
            $value = $this->getModel()->convertValueType($name, $value);
            if (is_string($value) && substr($value, 0, 13) =='kwfSerialized') {
                $value = unserialize(substr($value, 13));
            }
            return $value;
        } else {
            return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
        $n = $this->_transformColumnName($name);
        if (isset($this->_row->$n)) {
            if (is_array($value) || is_object($value)) {
                $value = 'kwfSerialized'.serialize($value);
            }
            $value = $this->getModel()->convertValueType($name, $value);

            // scheis php... bei $this->$name sucht er nur nach einem property
            // und vergisst, dass es __get() auch gibt
            $currentValue = $this->__get($name);
            if (is_array($currentValue) || is_object($currentValue)) {
                $currentValue = 'kwfSerialized'.serialize($currentValue);
            }
            if ($currentValue !== $value) {
                $this->_setDirty($name);
            }
            $this->_row->$n = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    protected function _saveWithoutResetDirty()
    {
        $insert = (!is_array($this->_getPrimaryKey()) && !$this->getCleanValue($this->_getPrimaryKey()))
            || ($this->_model->hasDeletedFlag() && $this->isDirty('deleted') && !$this->deleted);

        if ($insert) {
            $this->_beforeInsert();
        } else {
            $this->_beforeUpdate();
        }
        $this->_beforeSaveSiblingMaster();
        $this->_beforeSave();
        if ($insert || $this->_isDirty()) {
            $ret = $this->_row->save();
        } else {
            $ret = $this->{$this->_getPrimaryKey()};
        }
        if ($insert) {
            $this->_model->afterInsert($this);
        }
        parent::_saveWithoutResetDirty(); //siblings nach uns speichern; damit auto-inc id vorhanden
        if ($insert) {
            $this->_afterInsert();
        } else {
            $this->_afterUpdate();
        }
        $this->_afterSave();


        return $ret;
    }

    protected function _beforeDelete()
    {
        $this->_model->beforeDelete($this);
        parent::_beforeDelete();
    }

    public function delete()
    {
        parent::delete();
        $this->_beforeDelete();
        if ($this->_model->hasDeletedFlag()) {
            $this->_row->deleted = true;
            $this->_row->save();
        } else {
            $this->_row->delete();
        }
        $this->_afterDelete();
    }

    public function toDebug()
    {
        $i = get_class($this);
        $ret = print_r($this->toArray(), true);
        $ret = preg_replace('#^Array#', $i, $ret);
        $ret = "<pre>$ret</pre>";
        return $ret;
    }
    public function __toString()
    {
        if ($this->_model->getToStringField()) {
            return $this->{$this->_model->getToStringField()};
        }
        if (method_exists($this->_row, '__toString')) {
            return $this->_row->__toString();
        } else {
            $pk = $this->_model->getPrimaryKey();
            return get_class($this->_model).': '.$this->_row->$pk;
        }
    }

    public function getRow()
    {
        return $this->_row;
    }

    public function toArray()
    {
        $ret = parent::toArray();
        foreach ($this->_model->getOwnColumns() as $c) {
            $ret[$c] = $this->$c;
        }
        return $ret;
    }

    public function findDependentRowset($dependentTable, $ruleKey = null, Kwf_Model_Select $select = null)
    {
        $dbSelect = $this->_model->createDbSelect($select);
        if ($dependentTable instanceof Kwf_Model_Db) {
            $dependentTable = $dependentTable->getTable();
        }
        return $this->_row->findDependentRowset($dependentTable, $ruleKey, $dbSelect);
    }

    public function findParentRow($parentTable, $ruleKey = null, Kwf_Model_Select $select = null)
    {
        $dbSelect = $this->_model->createDbSelect($select);
        if ($parentTable instanceof Kwf_Model_Db) {
            $parentTable = $parentTable->getTable();
        }
        $class = get_class($this);
        return $this->_row->findParentRow($parentTable, $ruleKey, $dbSelect);
    }

    public function findManyToManyRowset($matchTable, $intersectionTable, $callerRefRule = null,
                                         $matchRefRule = null, Kwf_Model_Select $select = null)
    {
        $dbSelect = $this->_model->createDbSelect($select);
        if ($matchTable instanceof Kwf_Model_Db) {
            $matchTable = $matchTable->getTable();
        }
        if ($intersectionTable instanceof Kwf_Model_Db) {
            $intersectionTable = $intersectionTable->getTable();
        }
        return $this->_row->findManyToManyRowset($matchModel, $intersectionModel, $callerRefRule, $matchRefRule, $dbSelect);
    }
}
