<?p
class Vps_Auto_Data_Table_Parent extends Vps_Auto_Data_Abstra

    protected $_dataInde
    protected $_parentTabl

    public function __construct($parentTable, $dataIndex = nul
   
        $this->_parentTable = $parentTabl
        $this->_dataIndex = $dataInde
   

    public function load($ro
   
        $name = $this->_dataInde
        if (is_string($this->_parentTable))
            $tables = array($this->_parentTable
        } else
            $tables = $this->_parentTabl
       
        foreach ($tables as $t)
            $row = $row->findParentRow($t
            if (!$row) return '
       
        if (!$this->_dataIndex)
            return $row->__toString(
       
        if (!isset($row->$name) && !is_null($row->$name)) { //scheiß p
            throw new Vps_Exception("Index '$name' doesn't exist in row."
       
        return $row->$nam
   

    public function save(Zend_Db_Table_Row_Abstract $row, $dat
   
        throw new Vps_Exception('Save is not possible for Vps_Auto_Data_Table_Parent.'
   

    public function delete
   
        throw new Vps_Exception('Delete is not possible for Vps_Auto_Data_Table_Parent.'
   
