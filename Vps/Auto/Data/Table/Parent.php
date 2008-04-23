<?php
class Vps_Auto_Data_Table_Parent extends Vps_Auto_Data_Abstract
{
    protected $_dataIndex;
    protected $_parentTable;
    protected $_ruleKey;
    
    public function __construct($parentTable, $dataIndex = null, $ruleKey = null)
    {
        $this->_parentTable = $parentTable;
        $this->_dataIndex = $dataIndex;
        $this->_ruleKey = $ruleKey;
    }

    public function load($row)
    {
        $name = $this->_dataIndex;
        if (is_string($this->_parentTable) || !is_array($this->_parentTable)) {
            $tables = array($this->_parentTable);
        } else {
            $tables = $this->_parentTable;
        }
        if (!($row instanceof Vps_Model_Db_Row)) {
            throw new Vps_Exception("Data_Table_Parent only works with Vps_Model_Db");
        }
        $row = $row->getRow();
        foreach ($tables as $t) {
            $row = $row->findParentRow($t, $this->_ruleKey);
            if (!$row) return '';
        }
        if (!$this->_dataIndex) {
            return $row->__toString();
        }
        if (!isset($row->$name) && !is_null($row->$name)) { //scheiß php
            throw new Vps_Exception("Index '$name' doesn't exist in row.");
        }
        return $row->$name;
    }
}
