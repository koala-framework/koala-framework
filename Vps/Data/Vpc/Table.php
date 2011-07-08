<?php
class Vps_Data_Vpc_Table extends Vps_Data_Table_Parent
{
    private $_componentClass;
    private $_tagSuffix;
    private $_idSeparator;

    public function __construct($parentTable, $dataIndex = null, $componentClass, $tagSuffix = '', $idSeparator = '-')
    {
        parent::__construct($parentTable, $dataIndex);
        $this->_componentClass = $componentClass;
        $this->_tagSuffix = $tagSuffix;
        $this->_idSeparator = $idSeparator;
    }

    private function _getParentRow($row)
    {
        $table = new $this->_parentTable(array('componentClass' => $this->_componentClass));
        if ($this->_tagSuffix) {
            $componentId = $row->component_id . $this->_idSeparator . $row->id . '-' . $this->_tagSuffix;
        } else {
            $componentId = $row->component_id . $this->_idSeparator . $row->id;
        }
        $where = array(
            'component_id = ?' => $componentId
        );
        $ret = $table->fetchAll($where)->current();
        if (!$ret) {
            $ret = $table->createRow();
            $ret->component_id = $componentId;
        }
        return $ret;
    }

    public function load($row)
    {
        $row = $this->_getParentRow($row);
        $name = $this->_dataIndex;
        return $row->$name;
    }

    public function save(Vps_Model_Row_Interface $row, $data)
    {
        $row = $this->_getParentRow($row);
        $name = $this->_dataIndex;
        $row->$name = $data;
        $row->save();
    }

}
