<?php
class Vps_Db_Table_Rowset extends Zend_Db_Table_Rowset
{
    /**
     * Rowset in Array wie es für Ext.store.ArrayReader benötigt wird umwandeln.
     */
    public function toStringDataArray($key = 'id')
    {
        $data = array();
        foreach ($this as $row) {
            $data[] = array($row->$key, $row->__toString());
        }
        return $data;
    }
}
