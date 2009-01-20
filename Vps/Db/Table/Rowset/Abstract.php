<?php
abstract class Vps_Db_Table_Rowset_Abstract extends Zend_Db_Table_Rowset_Abstract
{
    /**
     * Rowset in Array wie es für Ext.store.ArrayReader benötigt wird umwandeln.
     **/
    public function toStringDataArray($fields = array('id', '__toString'))
    {
        if (is_string($fields)) {
            //falls nur die id als string angegeben wurde
            $fields = array($field, '__toString');
        }
        $data = array();
        foreach ($this as $row) {
            $d = array();
            foreach ($fields as $f) {
                if ($f == '__toString') {
                    $d[] = $row->__toString();
                } else {
                    $d[] = $row->$f;
                }
            }
            $data[] = $d;
        }
        return $data;
    }
    /**
     * Sortieren nach jedem beliebigen Feld das die row zurückgibt.
     * auch solche die nur in row::__get existieren
     *
     * (recht langsam, erstellt alle rows)
     **/
    public function sort($order, $count = null, $offset = null)
    {
        if (!count($this)) return;

        $sortFields = explode(',', $order);
        $sortData = array();
        foreach ($this as $row) {
            foreach ($sortFields as $k=>$i) {
                $i = trim($i);
                if (substr($i, -4) == 'DESC') $i = substr($i, 0, -4);
                if (substr($i, -3) == 'ASC') $i = substr($i, 0, -3);
                $i = trim($i);
                if (!isset($row->$i)) {
                    throw new Vps_Exception("Can't sort by '$i', field doesn't exist in row");
                }
                $sortData[$k][] = $row->$i;
            }
            $rows[] = $row;
        }
        $args = array();
        foreach ($sortFields as $k=>$i) {
            $i = trim($i);
            $args[] = $sortData[$k];
            if (substr($i, -4) == 'DESC') $args[] = SORT_DESC;
            if (substr($i, -3) == 'ASC') $args[] = SORT_ASC;
        }
        $args[] =& $rows; //ohne & hört sich der spaß auf

        if (!call_user_func_array('array_multisort', &$args)) {
            throw new Vps_Exception("Can't sort by '$order', array_multisort returned an error");
        }
        $this->_rows = array();

        //set the rows in the new order
        //does not update _data - so who cares?
        foreach ($rows as $i=>$row) {
            if ($offset && $i < $offset) continue;
            $this->_rows[$i] = $row;
            if ($count && $i >= count($this->_rows)) break;
        }
    }
    public function toDebug()
    {
        $i = get_class($this);
        $ret = print_r($this->_data, true);
        $ret = preg_replace('#^Array#', $i, $ret);
        $ret = "<pre>$ret</pre>";
        return $ret;
    }
}
