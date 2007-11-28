<?p
abstract class Vps_Auto_Data_Abstract implements Vps_Auto_Data_Interfa

    private $_fieldnam

    //wird autom. aufgerufen in Auto_Grid_Column::setData und Auto_Field::setDa
    public function setFieldname($nam
   
        $this->_fieldname = $nam
   

    public function getFieldname
   
        return $this->_fieldnam
   

    public function save(Zend_Db_Table_Row_Abstract $row, $dat
   
   

    public function delete
   
   
