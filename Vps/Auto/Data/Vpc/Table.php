<?php
class Vps_Auto_Data_Vpc_Table extends Vps_Auto_Data_Table_Parent
{
    private $_componentClass;
    private $_tagSuffix;

    public function __construct($parentTable, $dataIndex = null, $componentClass, $tagSuffix = '')
    {
        parent::__construct($parentTable, $dataIndex);
        $this->_componentClass = $componentClass;
        $this->_tagSuffix = $tagSuffix;
    }

    public function load($row)
    {
        $table = new $this->_parentTable(array('componentClass' => $this->_componentClass));
        if ($this->_tagSuffix) {
            $componentId = $row->component_id . '-' . $row->id . '-' . $this->_tagSuffix;
        } else {
            $componentId = $row->component_id . '-' . $row->id;
        }
        $where = array(
            'component_id = ?' => $componentId
        );
        $row = $table->fetchAll($where)->current();
        if ($row) {
            $name = $this->_dataIndex;
            return $row->$name;
        } else {
            return '';
        }
    }
}
