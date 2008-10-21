<?php
class Vps_Model_Db_Row extends Vps_Model_Row_Abstract
{
    protected $_row;

    public function __construct(array $config)
    {
        $this->_row = $config['row'];
        parent::__construct($config);
    }

    public function __isset($name)
    {
        if (in_array($name, $this->_model->getColumns())) {
            return isset($this->_row->$name);
        } else {
            return parent::__isset($name);
        }
    }

    public function __unset($name)
    {
        if (in_array($name, $this->_model->getColumns())) {
            unset($this->_row->$name);
        } else {
            parent::__unset($name);
        }
    }

    public function __get($name)
    {
        if (in_array($name, $this->_model->getColumns())) {
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
        if (in_array($name, $this->_model->getColumns())) {
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
        parent::save();

        $id = $this->{$this->_getPrimaryKey()};
        if (!$id) {
            $this->_beforeInsert();
        } else {
            $this->_beforeUpdate();
        }
        $this->_beforeSave();
        $ret = $this->_row->save();
        if (!$id) {
            $this->_afterInsert();
            $this->_model->afterInsert($this);
        } else {
            $this->_afterUpdate();
        }
        $this->_afterSave();
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
        $class = $this->_model->getRowsetClass();
        return new $class(array(
            'rowset' => $this->_row->findDependentRowset($dependentTable, $ruleKey, $dbSelect),
            'rowClass' => get_class($this),
            'model' => $this
        ));
    }

    public function findParentRow($parentTable, $ruleKey = null, Vps_Model_Select $select = null)
    {
        $dbSelect = $this->_model->createDbSelect($select);
        if ($parentTable instanceof Vps_Model_Db) {
            $parentTable = $parentTable->getTable();
        }
        $class = get_class($this);
        $dbRow = $this->_row->findParentRow($parentTable, $ruleKey, $dbSelect);
        if (!$dbRow) return null;
        return new $class(array(
            'row' => $dbRow,
            'model' => $this->_model
        ));
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
        $class = $this->_model->getRowsetClass();
        return new $class(array(
            'rowset' => $this->_row->findManyToManyRowset($matchModel, $intersectionModel, $callerRefRule, $matchRefRule, $dbSelect),
            'rowClass' => get_class($this),
            'model' => $this
        ));
    }
}
