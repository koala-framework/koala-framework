<?php
class Vps_Filter_Row_UniqueAscii extends Vps_Filter_Row_Abstract
{
    private $_sourceField;
    private $_groupBy = array();

    public function __construct($sourceField = null)
    {
        $this->_sourceField = $sourceField;
    }

    public function setSourceField($f)
    {
        $this->_sourceField = $f;
    }
    public function getSourceField($f)
    {
        return $this->_sourceField;
    }
    public function setGroupBy($fields)
    {
        if (is_string($fields)) $fields = array($fields);
        $this->_groupBy = $fields;
    }
    public function getGroupBy()
    {
        return $this->_groupBy;
    }

    public function filter($row)
    {
        if (is_null($this->_sourceField)) {
            $value = $row->__toString();
        } else {
            $f = $this->_sourceField;
            $value = $row->$f;
        }
        $value = Vps_Filter::get($value, 'Ascii');

        $where = array();
        foreach ($this->_groupBy as $f) {
            $where["$f = ?"] = $row->$f;
        }
        foreach ($row->getPrimaryKey() as $k=>$i) {
            $where["$k != ?"] = $i;
        }

        $x = 0;
        $unique = $value;
        $where["$this->_field = ?"] = $unique;
        while ($row->getTable()->fetchAll($where)->count() > 0) {
            $unique = $value . '_' . ++$x;
            $where["$this->_field = ?"] = $unique;
        }
        return $unique;
    }
}
