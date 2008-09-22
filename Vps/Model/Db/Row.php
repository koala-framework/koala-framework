<?php
class Vps_Model_Db_Row implements Vps_Model_Row_Interface
{
    protected $_row;
    protected $_model;
    private $_internalId;

    public function __construct(array $config)
    {
        $this->_row = $config['row'];
        $this->_model = $config['model'];
        static $internalId = 0;
        $this->_internalId = $internalId++;
    }
    
    public function getInternalId()
    {
        return $this->_internalId;
    }

    public function __isset($name)
    {
        return isset($this->_row->$name);
    }

    public function __unset($name)
    {
        unset($this->_row->$name);
    }

    public function __get($name)
    {
        $value = $this->_row->$name;
        if (is_string($value) && substr($value, 0, 13) =='vpsSerialized') {
            $value = unserialize(substr($value, 13));
        }
        return $value;
    }

    public function __set($name, $value)
    {
        if (is_array($value) || is_object($value)) {
            $value = 'vpsSerialized'.serialize($value);
        }
        $this->_row->$name = $value;
    }

    public function save()
    {
        return $this->_row->save();
    }

    public function delete()
    {
        $this->_row->delete();
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
    public function getModel()
    {
        return $this->_model;
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
