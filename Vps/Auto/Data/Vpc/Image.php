<?p
class Vps_Auto_Data_Vpc_Image extends Vps_Auto_Data_Abstra

    protected $_clas
    protected $_pageI
    protected $_componentKe
  
    public function __construct($class, $pageId, $componentKe
   
        $this->_class = $clas
        $this->_pageId = $pageI
        $this->_componentKey = $componentKe
   

    public function load($ro
   
        $tablename = Vpc_Abstract::getSetting($this->_class, 'tablename'
        $table = new $tablename(array('componentClass'=>$this->_class)
        $componentKey = $this->_componentKey . '-' . $row->i
        $row = $table->find($this->_pageId, $componentKey)->current(
        if ($row)
            return '<img src="' . $row->getImageUrl(Vpc_Basic_Image_Row::DIMENSION_MINI) . '" />
        } else
            return '
       
   

    public function save(Zend_Db_Table_Row_Abstract $row, $dat
   
        throw new Vps_Exception('Save is not possible for Vps_Auto_Data_Table_Parent.'
   

    public function delete
   
        throw new Vps_Exception('Delete is not possible for Vps_Auto_Data_Table_Parent.'
   
