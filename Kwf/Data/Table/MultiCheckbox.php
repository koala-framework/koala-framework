<?php
class Kwf_Data_Table_MultiCheckbox extends Kwf_Data_Abstract
{
    protected $_values;
    protected $_tableName;

    public function __construct($tableName, Kwf_Db_Table_RowSet_Abstract $values)
    {
        $this->_tableName = $tableName;
        $this->_values = $values;
    }

    public function load($row)
    {
        $selected = $row->getRow()->findDependentRowset($this->_tableName);
        $ref = $selected->getTable()->getReference(get_class($this->_values->getTable()));
        $key = $ref['columns'][0];

        $pk = $this->_values->getTable()->getPrimaryKey();
        $pk = $pk[1];

        $selectedIds = array();
        foreach ($selected as $i) {
            $selectedIds[] = $i->$key;
        }
        $ret = array();
        foreach ($this->_values as $v) {
            if (in_array($v->$pk, $selectedIds)) {
                $ret[] = $v->__toString();
            }
        }
        return implode(', ', $ret);
    }
}
