<?p
/
 * Basisklasse für Decorato
 * @package V
 * @subpackage Decorat
 * @copyright Copyright (c) 2007, Vivid Planet Software Gm
 
abstract class Vpc_Decorator_Abstract implements Vpc_Interfa

    protected $_componen
    protected $_da
    protected $_pageCollectio

    /
     * Ein Decorator kann im Gegensatz zu einer Komponenten direkt 
     * Konstruktor erstellt werden, da die Eigenschaften der Komponen
     * ohnehin durchgeschleift werde
     
     * @param Vps_Dao D
     * @param Vpc_Interface Komponente, die dekoriert werden so
     
    public function __construct(Vps_Dao $dao, Vpc_Interface $component
    
        $this->_dao = $dao
        $this->_component = $component
    
   
    public static function getSettings(
    
        return array()
    
  
    /
     * Setzt für sich und für die dekorierte Komponente die pageCollecti
     
     * @param Vps_PageCollection_Abstra
     
    public function setPageCollection(Vps_PageCollection_Abstract $pageCollectio
   
        $this->_pageCollection = $pageCollectio
        $this->_component->setPageCollection($pageCollection
   

    /
     * Schleift die Methode auf auf dekorierte Komponente durc
     
    public function getTemplateVars
   
        return $this->_component->getTemplateVars(
   
  
    /
     * Schleift die Methode auf auf dekorierte Komponente durc
     
    public function getId
   
        return $this->_component->getId(
   
  
    /
     * Schleift die Methode auf auf dekorierte Komponente durc
     
    public function getPageId
   
        return $this->_component->getPageId(
   
  
    /
     * Schleift die Methode auf auf dekorierte Komponente durc
     
    public function getComponentInfo
   
        return $this->_component->getComponentInfo(
   

    /
     * @return Vps_Dao D
     
    protected function getDao
   
        return $this->_da
   

    /
     * Schleift die Methode auf auf dekorierte Komponente durc
     
    public function generateHierarchy($filename = '
   
        return $this->_component->generateHierarchy($filename
   

    /
     * Schleift die Methode auf auf dekorierte Komponente durc
     
    public function saveFrontendEditing(Zend_Controller_Request_Http $reques
   
        return $this->_component->saveFrontendEditing($request
   
  
    /
     * Schleift die Methode auf auf dekorierte Komponente durc
     
    public function getChildComponents(
    
        return array($this->_component)
    
   
    public function getChildComponent(
    
        return $this->_component
    
   
    /
     * Schleift die Methode auf auf dekorierte Komponente durc
     
    public function findComponent($i
   
        return $this->_component->findComponent($id
   
  
    /
     * Schleift die Methode auf auf dekorierte Komponente durch, find
     * aber auch den Decorator selbs
     
    public function findComponentByClass($clas
   
        if (get_class($this) == $class)
            return $thi
        } else
            return $this->_component->findComponentByClass($class
       
   

    /
     * Shortcut für $this->_dao->getTable($tablenam
     * @param string Name des Mode
     
    protected function _getTable($tablename
    
        return $this->_dao->getTable($tablename)
    

    /*
     * @return Vpc_PageCollection_Abstract/null Vorsicht! In einer Komponente nicht darauf verlassen, dass es die PageCollection gibt
     *
    public function getPageCollection(
    
        return $this->_pageCollection
    

