<?p
abstract class Vps_Db_Table_Rowset_Abstract extends Zend_Db_Table_Rowset_Abstra

    /
     * Rowset in Array wie es für Ext.store.ArrayReader benötigt wird umwandel
     
    public function toStringDataArray($key = 'id
   
        $data = array(
        foreach ($this as $row)
            $data[] = array($row->$key, $row->__toString()
       
        return $dat
   

