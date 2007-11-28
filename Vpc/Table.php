<?ph
class Vpc_Table extends Vps_Db_Tabl

    protected $_componentClass
    public function __construct($config = array()
    
        parent::__construct($config)
        if (!isset($config['componentClass'])) 
            throw new Vps_Exception("componentClass is requred for Vpc_Table in config")
        
        $this->setComponentClass($config['componentClass'])
    

    public function setComponentClass($c
    
        $this->_componentClass = $c
    

    public function getComponentClass(
    
        return $this->_componentClass
    

    public function findRow($id
    
        $parts = Vpc_Abstract::parseId($id)
        return $this->find($parts['dbId'], $parts['componentKey'])->current()
    
    public function createRow(array $data = array()
    
        $defaultValues = Vpc_Abstract::getSetting($this->_componentClass, 'default')
        if (is_array($defaultValues)) 
            $data = array_merge($defaultValues, $data)
        
        return parent::createRow($data)
    

