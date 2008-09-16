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

    protected function _getWhere($row)
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

    public function filter($row)
    {
        $fieldname = $this->_field;
        $value = $row->$fieldname;

        $where = $this->_getWhere($row);

        foreach ($row->getPrimaryKey() as $k=>$i) {
            if ($i) {
                $where["$k != ?"] = $i;
            }
        }

        $count = $row->getTable()->fetchAll($where)->count()+1;

        // Wenn value null ist, Datensatz am Ende einfügen
        if (is_null($value)) {
            $value = $count;
        }
        if ($value < 1) $value = 1;
        if ($value > $count) $value = $count;

        $x = 0;
        foreach ($row->getTable()->fetchAll($where, $fieldname) as $r) {
            $x++;
            if ($x == $value) $x++;
            if ($r->$fieldname != $x) {
                $r->$fieldname = $x;
                $r->saveSkipFilters();
            }
        }
        return $value;
    }

    public function onDeleteRow(Vps_Db_Table_Row_Abstract $row)
    {
        $fieldname = $this->_field;
        $value = $row->$fieldname;

        $where = $this->_getWhere($row);

        foreach ($row->getPrimaryKey() as $k=>$i) {
            $where["$k != ?"] = $i;
        }

        $x = 0;
        foreach ($row->getTable()->fetchAll($where, $fieldname) as $r) {
            $x++;
            if ($r->$fieldname != $x) {
                $r->$fieldname = $x;
                $r->saveSkipFilters();
            }
        }
        return $value;
    }
}
