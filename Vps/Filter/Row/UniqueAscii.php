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

        if ($row instanceof Vps_Model_Row_Interface) {
            $select = new Vps_Model_Select();
            foreach ($this->_groupBy as $f) {
                $select->whereEquals($f, $row->$f);
            }
            $pk = $row->getModel()->getPrimaryKey();
            if ($row->$pk) {
                $select->whereNotEquals($pk, $row->$pk);
            }
            $x = 0;
            $unique = $value;
            while (!$this->_isUnique($unique, $select, $row->getModel())) {
                $unique = $value . '_' . ++$x;
            }
        } else {
            $where = array();
            foreach ($this->_groupBy as $f) {
                if (is_null($row->$f)) {
                    $where["ISNULL($f)"] = '';
                } else {
                    $where["$f = ?"] = $row->$f;
                }
            }
            foreach ($row->getPrimaryKey() as $k=>$i) {
                if (!is_null($i)) {
                    $where["$k != ?"] = $i;
                }
            }
            $x = 0;
            $unique = $value;
            $where["$this->_field = ?"] = $unique;
            while ($row->getTable()->fetchAll($where)->count() > 0) {
                $unique = $value . '_' . ++$x;
                $where["$this->_field = ?"] = $unique;
            }
        }
        return $unique;
    }

    private function _isUnique($value, $select, $model)
    {
        $select = clone $select;
        $select->whereEquals($this->_field, $value);
        return !$model->countRows($select);
    }
}
