<?php
class Vps_Controller_Action_Auto_Abstract extends Vps_Controller_Action
{
    protected $_primaryKey;
    protected $_table;
    protected $_tableName;
    protected $_buttons = array();
    protected $_permissions; //todo: Zend_Acl ??

    public function init()
    {
        if (!isset($this->_table) && isset($this->_tableName)) {
            $this->_table = new $this->_tableName();
        }
        if (!isset($this->_permissions)) {
            $this->_permissions = $this->_buttons;
        }
        if (isset($this->_table)) {
            $info = $this->_table->info();
            if(!isset($this->_primaryKey)) {
                $info = $this->_table->info();
                $this->_primaryKey = $info['primary'][1];
            }
        }
    }
    protected function _fetchFromParentRow($row, $findParent)
    {
        if (!$row instanceof Zend_Db_Table_Row_Abstract) {
            throw new Vps_Exception("Can use findParent only if _fetchData returns a RowSet object");
        }
        if (is_string($findParent)) {
            $findParent = array('table'=>$findParent);
        }
        if (!isset($findParent['rule'])) $findParent['rule'] = null;
        $parentRow = $row->findParentRow($findParent['table'], $findParent['rule']);
        if (!$parentRow) {
            return null;
        }
        if (!isset($findParent['field'])) {
            if (!method_exists($parentRow, '__toString')) {
                throw new Vps_Exception("Method __toString ".get_class($parentRow)." for row of parent-table '$findParent[table]' does not exist, implement the function or specify a field");
            }
            return $parentRow->__toString();
        }
        if (!is_null($parentRow->$findParent['field']) && !isset($parentRow->$findParent['field'])) {
            throw new Vps_Exception("Index '$findParent[field]' for parent-table '$findParent[table]' not found");
        }
        return $parentRow->$findParent['field'];
    }

    protected function _fetchFromRow($row, $dataIndex)
    {
        if (!is_null($row->$dataIndex) && !isset($row->$dataIndex)) {
            throw new Vps_Exception("Index '$dataIndex' not found in row");
        }
        return $row->$dataIndex;
    }
}
