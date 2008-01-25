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
        $key = array(
            'page_id = ?' => $row->page_id,
            'component_key = ?' => $row->component_key . '-' . $row->id . $this->_tagSuffix
        );

        $row = $table->fetchAll($key)->current();
        if ($row) {
            $name = $this->_dataIndex;
            return $row->$name;
        } else {
            return '';
        }
    }
}
