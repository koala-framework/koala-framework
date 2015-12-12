<?php
class Kwf_Data_Table_Parent extends Kwf_Data_Abstract
{
    protected $_dataIndex;
    protected $_parentTable;

    /**
     * @param string $parentTable rule
     * @param string $dataIndex row die angezeigt werden soll, wenn null wird __toString verwendet
     */
    public function __construct($parentTable, $dataIndex = null)
    {
        $this->_parentTable = $parentTable;
        $this->_dataIndex = $dataIndex;
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
            $row = $row->getParentRow($t);
            if (!$row) return '';
        }
        if (!$this->_dataIndex) {
            return $row->__toString();
        }
        if (!isset($row->$name) && !is_null($row->$name)) { //scheiß php
            throw new Kwf_Exception("Index '$name' doesn't exist in row.");
        }
        return $row->$name;
    }
}
