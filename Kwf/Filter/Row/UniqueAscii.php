<?php
/**
 * @package Filter
 */
class Kwf_Filter_Row_UniqueAscii extends Kwf_Filter_Row_Abstract
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
        $value = Kwf_Filter::filterStatic($value, 'Ascii');

        $select = new Kwf_Model_Select();
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

        return $unique;
    }

    private function _isUnique($value, $select, $model)
    {
        $select = clone $select;
        $select->whereEquals($this->_field, $value);
        return !$model->countRows($select);
    }
}
