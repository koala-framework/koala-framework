<?p
class Vps_Auto_Data_Table extends Vps_Auto_Data_Abstra

    protected $_dataInde

    /
     * @param string Spaltenname in Tabelle, standard ist Feldna
     *
    public function __construct($dataIndex = nul
   
        $this->_dataIndex = $dataInde
   

    public function load($ro
   
        $name = $this->_dataInde
        if (!$name) $name = $this->getFieldname(
        if (!isset($row->$name) && !is_null($row->$name)) { //scheiß p
            throw new Vps_Exception("Index '$name' doesn't exist in row."
       
        return $row->$nam
   

    public function save(Zend_Db_Table_Row_Abstract $row, $dat
   
        $name = $this->getFieldname(
        $row->$name = $dat
   

