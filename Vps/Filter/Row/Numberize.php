<?php
class Vps_Filter_Row_Numberize extends Vps_Filter_Row_Abstract
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

    //legacy für Db_Table
    private function _getWhere($row)
    {
        $where = array();
        foreach ($this->_groupBy as $k=>$field) {
            if (is_array($field)) {
                $values = $field;
                $field = $k;
                $valueFound = false;
                foreach ($values as $value) {
                    if ($row->$field == $value) {
                        $valueFound = true;
                        if (is_null($value)) {
                            $where[] = "ISNULL($field)";
                        } else {
                            $where["$field = ?"] = $value;
                        }
                        break;
                    }
                }
                if (!$valueFound) {
                    $in = array();
                    foreach ($values as $value) {
                        $in[] = $row->getTable()->getAdapter()->quote($value);
                    }
                    $in = implode(',', $in);
                    $where[] = "$field NOT IN ($in)";
                }
            } else {
                if (is_null($row->$field)) {
                    $where[] = "ISNULL($field)";
                } else {
                    $where["$field = ?"] = $row->$field;
                }
            }
        }
        return $where;
    }

    private function _getSelect($row)
    {
        $ret = new Vps_Model_Select();
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
        $fieldname = $this->_field;
        $value = $row->$fieldname;

        if ($row instanceof Vps_Model_Row_Interface) {
            $select = $this->_getSelect($row);
            $pk = $row->getModel()->getPrimaryKey();
            if ($row->{$pk}) {
                $select->whereNotEquals($pk, $row->{$pk});
            }
            $count = $row->getModel()->countRows($select) + 1;
        } else {
            $where = $this->_getWhere($row);
            foreach ($row->getPrimaryKey() as $k=>$i) {
                if ($i) {
                    $where["$k != ?"] = $i;
                }
            }
            $count = $row->getTable()->fetchAll($where)->count() + 1;
        }



        // Wenn value null ist, Datensatz am Ende einfügen
        if (is_null($value)) {
            $value = $count;
        }
        if ($value < 1) $value = 1;
        if ($value > $count) $value = $count;

        $x = 0;
        if ($row instanceof Vps_Model_Row_Interface) {
            $select->order($fieldname);
            $rows = $row->getModel()->getRows($select);
        } else {
            $rows = $row->getTable()->fetchAll($where, $fieldname);
        }
        foreach ($rows as $r) {
            $x++;
            if ($x == $value) $x++;
            if ($r->$fieldname != $x) {
                $r->$fieldname = $x;
                $r->saveSkipFilters();
            }
        }
        return $value;
    }

    public function onDeleteRow($row)
    {
        $fieldname = $this->_field;
        $value = $row->$fieldname;

        $where = $this->_getWhere($row);

        if ($row instanceof Vps_Model_Row_Interface) {
            $select = $this->_getSelect($row);
            $pk = $row->getModel()->getPrimaryKey();
            if ($row->{$pk}) {
                $select->whereNotEquals($pk, $row->{$pk});
            }
            $select->order($fieldname);
            $rows = $row->getModel()->getRows($select);
        } else {
            $where = $this->_getWhere($row);
            foreach ($row->getPrimaryKey() as $k=>$i) {
                if ($i) {
                    $where["$k != ?"] = $i;
                }
            }
            $rows = $row->getTable()->fetchAll($where, $fieldname);
        }

        $x = 0;
        foreach ($rows as $r) {
            $x++;
            if ($r->$fieldname != $x) {
                $r->$fieldname = $x;
                $r->saveSkipFilters();
            }
        }
        return $value;
    }
}
