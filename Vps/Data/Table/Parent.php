<?php
class Vps_Data_Table_Parent extends Vps_Data_Abstract
{
    protected $_dataIndex;
    protected $_parentTable;
    protected $_ruleKey;

    /**
     * @param string $parentTable wenn Zend_Db_Table: table, wenn Vps_Model: rule
     * @param string $dataIndex row die angezeigt werden soll, wenn null wird __toString verwendet
     * @param string $ruleKey nur wenn Zend_Db_Table
     */
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
        foreach ($tables as $t) {
            if ((is_object($t) || class_exists($t)) && is_instance_of($t, 'Zend_Db_Table_Abstract')) {
                $row = $row->findParentRow($t, $this->_ruleKey);
            } else {
                $row = $row->getParentRow($t);
            }
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
