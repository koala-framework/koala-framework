<?php
class Vps_Model_Db_Row extends Vps_Model_Row_Abstract
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
        $ret = isset($this->_row->$name);
        if (!$ret) $ret = parent::__isset($name);
        return $ret;
    }

    public function __unset($name)
    {
        if (isset($this->_row->$name)) {
            unset($this->_row->$name);
        } else {
            parent::__unset($name);
        }
    }

    public function __get($name)
    {
        if (isset($this->_row->$name)) {
            $value = $this->_row->$name;
            if (is_string($value) && substr($value, 0, 13) =='vpsSerialized') {
                $value = unserialize(substr($value, 13));
            }
            return $value;
        } else {
            return parent::__get($name);
        }
    }

    public function __set($name, $value)
    {
        if (isset($this->_row->$name)) {
            if (is_array($value) || is_object($value)) {
                $value = 'vpsSerialized'.serialize($value);
            }
            $this->_row->$name = $value;
        } else {
            parent::__set($name, $value);
        }
    }

    public function save()
    {
        $insert =
            !is_array($this->_getPrimaryKey())
            && !$this->{$this->_getPrimaryKey()};
        if ($insert) {
            $this->_beforeInsert();
        } else {
            $this->_beforeUpdate();
        }
        $this->_beforeSave();
        $ret = $this->_row->save();
        if ($insert) {
            $this->_afterInsert();
            $this->_model->afterInsert($this);
        } else {
            $this->_afterUpdate();
        }
        $this->_afterSave();

        parent::save();
        return $ret;
    }

    public function delete()
    {
        parent::delete();
        $this->_beforeDelete();
        $this->_row->delete();
        $this->_afterDelete();
    }

    public function toDebug()
    {
        return $this->_row->toDebug();
    }
    public function __toString()
    {
        return $this->_row->__toString();
    }

    public function getRow()
    {
        return $this->_row;
    }
    public function toArray()
    {
        return $this->_row->toArray();
    }

    public function findDependentRowset($dependentTable, $ruleKey = null, Vps_Model_Select $select = null)
    {
        $dbSelect = $this->_model->createDbSelect($select);
        if ($dependentTable instanceof Vps_Model_Db) {
            $dependentTable = $dependentTable->getTable();
        }
        return $this->_row->findDependentRowset($dependentTable, $ruleKey, $dbSelect);
    }

    public function findParentRow($parentTable, $ruleKey = null, Vps_Model_Select $select = null)
    {
        $dbSelect = $this->_model->createDbSelect($select);
        if ($parentTable instanceof Vps_Model_Db) {
            $parentTable = $parentTable->getTable();
        }
        $class = get_class($this);
        return $this->_row->findParentRow($parentTable, $ruleKey, $dbSelect);
    }

    public function findManyToManyRowset($matchTable, $intersectionTable, $callerRefRule = null,
                                         $matchRefRule = null, Vps_Model_Select $select = null)
    {
        $dbSelect = $this->_model->createDbSelect($select);
        if ($matchTable instanceof Vps_Model_Db) {
            $matchTable = $matchTable->getTable();
        }
        if ($intersectionTable instanceof Vps_Model_Db) {
            $intersectionTable = $intersectionTable->getTable();
        }
        return $this->_row->findManyToManyRowset($matchModel, $intersectionModel, $callerRefRule, $matchRefRule, $dbSelect);
    }

    public function getTable()
    {
        return $this->_row->getTable();
    }
}
