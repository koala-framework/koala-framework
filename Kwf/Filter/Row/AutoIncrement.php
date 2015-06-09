<?php
class Kwf_Filter_Row_AutoIncrement extends Kwf_Filter_Row_Abstract
{
    private $_groupBy = array();

    public function __construct()
    {
    }

    /**
     * Nach welchen Spalten die Nummerierung Gruppiert sein soll.
     *
     * 'spalte': nach dieser einen Spalte Gruppieren
     *
     * array('spalte1', 'spalte2'): nach diesen Spalten Gruppieren
     *
     * Spezialfall:
     * array('spalte' => array('wert1', 'wert2')): nach dieser Spalte Gruppieren
     *                        jedoch nach wert1, wert2 oder NOT IN(wert1, wert2)
     */
    public function setGroupBy($fields)
    {
        if (is_string($fields)) $fields = array($fields);
        $this->_groupBy = $fields;
    }

    public function getGroupBy()
    {
        return $this->_groupBy;
    }

    protected function _getSelect($row)
    {
        $ret = new Kwf_Model_Select();
        foreach ($this->_groupBy as $k=>$field) {
            if (is_array($field)) {
                $values = $field;
                $field = $k;
                $valueFound = false;
                foreach ($values as $value) {
                    if ($row->$field == $value) {
                        $valueFound = true;
                        $ret->whereEquals($field, $value);
                        break;
                    }
                }
                if (!$valueFound) {
                    $ret->whereNotEquals($field, $values);
                }
            } else {
                if (is_null($row->$field)) {
                    $ret->whereNull($field);
                } else {
                    $ret->whereEquals($field, $row->$field);
                }
            }
        }
        return $ret;
    }

    public function filter($row)
    {
        if (!$row->{$this->_field}) {
            $max = $row->getModel()->evaluateExpr(new Kwf_Model_Select_Expr_Max($this->_field), $this->_getSelect($row));
            return $max + 1;
        } else {
            return $row->{$this->_field};
        }
    }
}

