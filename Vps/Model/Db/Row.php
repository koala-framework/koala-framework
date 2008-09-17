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
    
    public function findDependentRowset($dependentModel, $ruleKey = null, Vps_Model_Select $select = null)
    {
        $dbSelect = $this->_createDbSelect($select);
        $dependentTable = $dependentModel->getTable();
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->findDependentRowset($dependentTable, $ruleKey, $dbSelect),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }
        
    public function findParentRow($parentModel, $ruleKey = null, Vps_Model_Select $select = null)
    {
        $dbSelect = $this->_createDbSelect($select);
        $parentTable = $parentModel->getTable();
        return new $this->_rowClass(array(
            'row' => $this->_table->findParentRow($parentTable, $ruleKey, $dbSelect),
            'model' => $this->_model
        ));
    }

    public function findManyToManyRowset($matchModel, $intersectionModel, $callerRefRule = null,
                                         $matchRefRule = null, Vps_Model_Select $select = null)
    {
        $dbSelect = $this->_createDbSelect($select);
        $matchTable = $matchModel->getTable();
        $intersectionTable = $intersectionModel->getTable();
        return new $this->_rowsetClass(array(
            'rowset' => $this->_table->findManyToManyRowset($matchModel, $intersectionModel, $callerRefRule, $matchRefRule, $dbSelect),
            'rowClass' => $this->_rowClass,
            'model' => $this
        ));
    }
}
