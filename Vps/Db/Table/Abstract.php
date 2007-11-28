<?p
abstract class Vps_Db_Table_Abstract extends Zend_Db_Table_Abstra

    private $_da
    protected $_rowClass = 'Vps_Db_Table_Row
    protected $_rowsetClass = 'Vps_Db_Table_Rowset

    public function setDao($da
   
        $this->_dao = $da
   

    public function getDao
   
        return $this->_da
   

    public function numberize($id, $fieldname, $value, array $where = array(
   
        $row = $this->find($id)->current(
        if ($row)
            return $row->numberize($fieldname, $value, $where
        } else
            return fals
       
   
  
    public function numberizeAll($fieldname, $where = array(
   
        $rows = $this->fetchAll($where, $fieldname
        $i = 
        foreach ($rows as $row)
            if ($row->$fieldname != $i)
                $row->$fieldname = $
                $row->save(
           
            $i+
       

   


