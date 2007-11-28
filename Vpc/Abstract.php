<?ph
/*
 * Vivid Planet Component (Vpc
 * @package Vp
 * @copyright Copyright (c) 2007, Vivid Planet Software Gmb
 *
abstract class Vpc_Abstract implements Vpc_Interfac

    protected $_dao
    private $_id
    private $_hasGeneratedForFilename = array()
    private $_pageCollection = null

    private $_store
    protected $_row
    private $_tables = array()

    protected $_table
   
    const LOREM_IPSUM = 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.'

    /*
     * Sollte nicht direkt aufgerufen werden, sondern über statische Methoden der Klasse. Kann nich
     * überschrieben werden, stattdessen sollte setup() verwendet werden
     
     * @see createInstanc
     * @see createPag
     
     * @param Vps_Dao DA
     * @param int Falls Komponenten geschachtelt werden, die componentId der obersten Komponent
     * @param int Id der aktuellen Komponent
     * @param string Falls dynamische Unterseit
     * @param string Falls dynamische Unterkomponent
     *
    public final function __construct(Vps_Dao $dao = null, $id = null, $pageCollection = null
    
        if (is_null($dao)) { return; 
       
        $this->_dao = $dao
        $this->_pageCollection = $pageCollection

        if (is_object($id)) 
            foreach (Vpc_Abstract::getSetting(get_class($this), 'default') as $k=>$i) 
                if (!isset($id->$k)) $id->$k = $i
            
            $this->_row = $id
        } else 
            $this->_id = $this->parseId($id)
        

        $table = $this->getTable()
        if ($table && !isset($this->_row)) 
            $info = $table->info()
            if ($info['primary'] == array(1 => 'page_id', 2 => 'component_key')) 
                $this->_row = $table->find($this->getPageId(), $this->getComponentKey())->current()
                if (!$this->_row) 
                    $this->_row = $table->createRow()
                
            
        

        $this->_init()

        if (Zend_Registry::isRegistered('infolog')) 
            if (!is_string($id)) $id = '(static)'
            Zend_Registry::get('infolog')->createComponent(get_class($this) . ' - ' . $id)
        
    

    /*
     * Wird nach dem Konstruktor aufgerufen. Initialisierungscode in Unterklassen ist hier richtig
     *
    protected function _init() {

    /*
     * Erstellt aus der ID der Komponente die Komponente
     
     * @param Vps_Dao DA
     * @param string ID der Komponent
     * @return Vpc_Abstrac
     *
    public static function createInstance(Vps_Dao $dao, $class, $id, $pageCollection = null
    
        return self::_createInstance($dao, $class, $id, $pageCollection)
    

    /*
     * Erstellt eine Komponente, die als neue Seite in den Seitenbaum eingefügt werde
     * kann
     
     * Wird von generateHierarchy() verwendet. Wenn eine Unterseite erstellt wird, muss dieser ein
     * neue pageId zugewiesen werden. Diese pageId besteht aus topComponentId und pageKeys/pageTags
     * Die pageKeys/pageTags dienen zur Unterscheidung bei gleicher topComponentId. pageKeys werde
     * verwendet, wenn bei gleichen Seitenaufbau der Unterseite unterschiedliche Inhalte angezeig
     * werden sollen, pageTags, wenn die gleichen Inhalte angezeigt werden sollen bzw. nur darau
     * spezialisierte Komponenten auf den pageTag reagieren und unterschiedliche Inhalte liefern
     
     * @see generateHierarch
     * @param string Klassenname der Komponente, die erstellt werden sol
     * @param int Falls bestehende Komponente aus DB erstellt werden soll (className wird bei Werten != 0 wirkungslos!
     * @param int Für Unterscheidung in Seitenbaum und des Komponenteninhalt
     * @param int Für Unterscheidung in Seitenbaum ohne Unterscheidung des Komponenteninhalt
     * @param int Wie pageTag, wird jedoch nicht hierarchisch an die URL angehängt, sondern überschriebe
     * @return Vpc_Abstract Komponente, die als Seite im Seitenbaum hinzugefügt werden kan
     * @throws Vpc_Exception Falls pageKeySuffix und pageTagSuffix gleichzeit gesetzt werde
     *
    public function createPage($class, $pageKeySuffix = '', $pageTagSuffix = ''
    
        $id = $this->getId()
        if ($pageKeySuffix != '') 
            $id .= '_' . $pageKeySuffix
        

        if ($pageTagSuffix != '') 
            $id .= ',' . $pageTagSuffix
        

        // Page erstelle
        $page = self::_createInstance($this->getDao(), $class, $id, $this->getPageCollection())

        // Erstellte Komponente hinzufüge
        return $page
    
   
    /*
     * Falls eine Komponente Unterkomponenten hat (zB. TextPic hat eine Textbox- un
     * ein Pic-Komponente), werden diese hier erstellt
     
     * @param string Klassenname der Komponente, die erstellt werden sol
     * @param int Falls bestehende Komponente aus DB erstellt werden soll (className wird bei Werten != 0 wirkungslos!
     * @param int Für Unterscheidung des Komponenteninhalt
     * @return Vpc_Abstract Komponent
     *
    public function createComponent($class, $pageKeySuffix = ''
    
        $id = $this->getId()
        if ($pageKeySuffix != '') 
            $id .= '-' . $pageKeySuffix
        

        // Komponente erstelle
        $component = self::_createInstance($this->getDao(), $class, $id, $this->getPageCollection())

        // Erstellte Komponente hinzufüge
        return $component
    

    /*
     * Erstellt die Komponente tatsächlich
     * @throws Vpc_ComponentNotFoundException Falls Klasse für Komponente nicht gefunden wir
     *
    private static function _createInstance(Vps_Dao $dao, $class, $id, $pageCollection = null
    
        // Komponente erstelle
        if (class_exists($class)) 
            $component = new $class($dao, $id, $pageCollection)
        } else 
            throw new Vpc_ComponentNotFoundException("Component '$class' not found.")
        

        // Decorators hinzufüge
        if (!is_null($component)) 
            $decoratorData = $dao->getTable('Vps_Dao_Pages')->retrieveDecoratorData($component->getId())
            foreach ($decoratorData as $decoratorClass) 
                if (class_exists($decoratorClass)) 
                    $component = new $decoratorClass($dao, $component)
                
            
        

        return $component
    

    /*
     * Die id identifiziert jede Komponente (auch Unterkomponente) und kan
     * hier in ihre Bestandteile zerlegt werden
     
     * Die id besteht aus componentId_pageKey-componentKey, wobei der pageKey un
     * der componentKey optional sein können und der pageKey aus pageKey und pageTa
     * zusammengesetzt wird. Der pageKey und der componentKey können bei geschachtel
     * werden (Trennzeichen .)
     
     * @param string i
     * @return array Array mit Bestandteilen der i
     * @throws Vpc_Exception Falls id nicht auf Muster pass
     *
    public static function parseId($id
    
        $keys = array()
        $pattern = self::getIdPattern()
        preg_match("#^$pattern\$#", $id, $keys)

        if ($keys == null) 
            throw new Vpc_Exception("ID '$id' doesn't match pattern for Id: $pattern")
        

        $parts['id'] = $keys[0]
        $parts['dbId'] = $keys[1]
        $parts['componentId'] = ''
        $parts['pageId'] = ''
        $parts['componentKey'] = ''
        $parts['pageKey'] = ''
        $parts['pageKeys'] = array()
        $parts['currentComponentKey'] = ''
        $parts['currentPageKey'] = ''
        $parts['currentPageTag'] = ''

        $pageKey = isset($keys[2]) ? $keys[2] : ''
        $pageKeys = array()
        $currentPageKey = ''
        foreach (str_split($pageKey) as $pos => $key) 
            if ($key == ',' || $key == '-' || $key == '_') 
                if ($currentPageKey != '') 
                    $pageKeys[substr($pageKey, 0, $pos)] = $currentPageKey
                
                $currentPageKey = $key
            } else 
                $currentPageKey .= $key
            
        
        if ($currentPageKey != '') 
            $pageKeys[$pageKey] = $currentPageKey
        
        foreach ($pageKeys as $currentPageKey => $value) 
            $key = substr($value, 0, 1)
            $val = substr($value, 1)
            if ($key != '-') 
                $parts['pageKeys'][$currentPageKey] = $val
                $parts['pageKey'] = $currentPageKey
                if ($key == ',') 
                    $parts['currentPageTag'] = $val
                    $parts['currentPageKey'] = ''
                } else if ($key == '_') 
                    $parts['currentPageKey'] = $val
                    $parts['currentPageTag'] = ''
                
            
            if ($key != ',') 
                $parts['componentKey'] .= $value
            
            if ($key == '-') 
                $parts['currentComponentKey'] = $value
            
        
        $parts['componentId'] = $parts['dbId'] . $parts['componentKey']
        $parts['pageId'] = $parts['dbId'] . $parts['pageKey']
        return $parts
    

    public static function getIdPattern(
    
        $pattern = '([0-9a-zA-Z]+)'; // PageI
        $pattern .= '(((-|_|,)[0-9a-zA-Z]+)*)?'; // PageKe
        return $pattern
    

    /*
     * @return string id der Komponente
     * @see parseI
     *
    public function getId(
    
        return (string)$this->_id['id']
    

    /*
     * @return string pageId der Komponente
     * @see parsePageI
     *
    public function getPageId(
    
        return (string)$this->_id['pageId']
    

    /*
     * @return string pageId der Komponente
     * @see parsePageI
     *
    public function getDbId(
    
        return (int)$this->_id['dbId']
    

    /*
     * Da der pageKey in der URL auch die pageTags beinhalten kann
     * wird er hier zerlegt und nur die pageKeys zurückgegeben
     
     * @return string pageKey, falls es mehrere gibt, durch . aneinandergekette
     *
    public function getPageKey(
    
        return (string)$this->_id['pageKey']
    

    public function getComponentKey(
    
        return (string)$this->_id['componentKey']
    

    public function getCurrentComponentKey(
    
        return (string)$this->_id['currentComponentKey']
    

    /*
     * Der pageKey wird in Normalfall hierarchisch gespeichert. Hie
     * wird nur der letzte pageKey zurückgegeben
     
     * @return string pageTa
     *
    public function getCurrentPageKey(
    
        return (string)$this->_id['currentPageKey']
    

    /*
     * Der pageKey wird in Normalfall hierarchisch gespeichert. Hie
     * wird nur der letzte pageTag zurückgegeben
     
     * @return string pageTa
     *
    public function getCurrentPageTag(
    
        return (string)$this->_id['currentPageTag']
    

    /*
     * Durchsucht die aktuelle Komponente und deren Unterkomponenten nach de
     * Komponente mit der entsprechenden id
     
     * @param string id der Komponent
     * @return Vpc_Abstract/nul
     *
    public function findComponent($id
    
        if ($this->getId() == $id) 
            return $this
        } else 
            foreach ($this->getChildComponents() as $childComponent) 
                $component = $childComponent->findComponent($id)
                if ($component != null) 
                    return $component
                
            
        
        return null
    

    /*
     * Durchsucht die aktuelle Komponente und deren Unterkomponenten nach de
     * Komponente mit der entsprechenden Klasse
     
     * @param string Klassenname der gesuchten Komponent
     * @return Vpc_Abstract/nul
     *
    public function findComponentByClass($class
    
        if (get_class($this) == $class) 
            return $this
        } else 
            foreach ($this->getChildComponents() as $childComponent) 
                $component = $childComponent->findComponentByClass($class)
                if ($component != null) 
                    return $component
                
            
        
        return null
    

    /*
     * Wird von extern gesetzt, wenn es einen Seitenbaum gibt
     * @param Vps_PageCollection_Abstrac
     *
    public function setPageCollection(Vps_PageCollection_Abstract $pageCollection
    
        $this->_pageCollection = $pageCollection
    

    /*
     * @return Vpc_PageCollection_Abstract/null Vorsicht! In einer Komponente nicht darauf verlassen, dass es die PageCollection gibt
     *
    public function getPageCollection(
    
        return $this->_pageCollection
    

    /*
     * Falls eine Komponente Unterseiten im Seitenbaum erstellt, wird das hier gemacht
     
     * Standardmäßig werden die Seiten aus dem als Unterseite im Seitenbaum hinzugefügt. Fall
     * eine Komponente dynamisch Unterseiten erstellen will, sollte das in dieser Methode erfolgen
     * parent::generateHierarchy sollte dennoch aufgerufen werden
     
     * Der zweite Parameter bestimmt, ob die Seite als Home ausgeführt wird. Falls die Seit
     * also Home ausgeführt wird, werden die Unterseiten der obersten Ebene hinzugefügt, di
     * Seite fungiert also als Rootpage
     
     * @param string Nächster Bestandteil der URL für lazy loading, damit nicht immer alle Unterseiten erstellt werden müsse
     * @param boolean Hierarchie wird im Kontext der Homepage erstell
     * @return Array mit erstellten Unterseite
     *
    public function generateHierarchy($filename = ''
    
        $return = array()
        if (!in_array('', $this->_hasGeneratedForFilename) && !in_array($filename, $this->_hasGeneratedForFilename)) 

            $rows = $this->_dao->getTable('Vps_Dao_Pages')->retrieveChildPagesData($this->getId())
            foreach($rows as $pageRow) 
                if ($filename != '' && $filename != $pageRow['filename']) { continue; 
                $page = self::createInstance($this->getDao(), $pageRow['component_class'], $pageRow['id'], $this->getPageCollection())
                $this->getPageCollection()->addTreePage($page, $pageRow['filename'], $pageRow['name'], $this)
                $r['page'] = $page
                $r['filename'] = $pageRow['filename']
                $return[] = $r
            

            $this->_hasGeneratedForFilename[] = $filename
        

        return $return
    

    /*
     * Falls eine Komponente Unterkomponente erstellt, wird das hier gemacht
     
     * @return Array mit erstellten Unterkomponent
     *
    public function getChildComponents(
    
        return array()
    
   
    /*
     * Gibt die Variablen für View zurück
     
     * Variable 'template' muss immer gesetzt werden
     
     * @return array Template-Variable
     *
    public function getTemplateVars(
    
        $vars = array()
        $vars['assets']['js'] = array()
        $vars['assets']['css'] = array()
        $vars['class'] = get_class($this)
        $vars['id'] = $this->getId()
        $vars['store'] = $this->_store
        $vars['template'] = Vpc_Admin::getComponentFile(get_class($this), '', 'tpl')
        if (!$vars['template']) 
            throw new Vpc_Exception('Template not found for Component ' . get_class($this))
        
        return $vars
    

    /*
     * Informationen über den Aufbau der aktuellen Komponente
     
     * Falls eine Komponente Unterkomponenten hat, deren Informatione
     * einschließen. Für jede Komponente wird im Array ein Eintrag mi
     * dem Schlüssel id und dem Wert Klassenname angehängt
     
     * @return array ComponentInf
     *
    public function getComponentInfo(
    
        return array($this->getId() => get_class($this))
    

    /*
     * @return DAO der Komponent
     *
    public function getDao(
    
        return $this->_dao
    

    /*
     * @return arra
     *
    public function saveFrontendEditing(Zend_Controller_Request_Http $request
    
        return array()
    
   
    protected function _getParam($param
    
        return isset($_REQUEST[$param]) ? $_REQUEST[$param] : null
    

    /*
     * Shortcut, fragt vom Seitenbaum die Url für eine Komponente a
     
     * @param Vpc_Abstract Komponente, für die man die URL wissen wil
     * @return string URL der Seit
     *
    protected function getUrl($component = null
    
        if ($component == null) { $component = $this; 
        return $this->getPageCollection()->getUrl($component)
    

    /*
     * Shortcut, fragt vom Seitenbaum, ob die unsichtbaren Einträg
     * auch angezeige werde
     
     * @return boolea
     *
    protected function showInvisible(
    
        if ($this->getPageCollection()) 
            return $this->getPageCollection()->showInvisible()
        } else 
            return true
        
    

    /*
     * Shortcut für $this->_dao->getTable($tablename
     * @param string Name des Model
     *
    public function getTable($tablename = null
    
        if (!$tablename) 
            $tablename = $this->_getSetting('tablename')
            if (!$tablename) 
                return null
            
        
        try 
            if (!isset($this->_tables[$tablename])) 
                $this->_tables[$tablename] = new $tablename(array('componentClass'=>get_class($this)))
            
            return $this->_tables[$tablename]
        } catch (Vps_Dao_Exception $e) 
            return null
        
    
   
    public static function getTablename($class
    
        return self::$tablename
    
   
    public static function getSetting($class, $setting
    
        $settings = call_user_func(array($class, 'getSettings'))
        return isset($settings[$setting]) ? $settings[$setting] : null 
    

    public static function getSettings(
    
        return array()
    

    protected function _getSetting($setting
    
        return self::getSetting(get_class($this), $setting)
    

    protected function _getClassFromSetting($setting, $parentClass) 
        $classes = $this->_getSetting('childComponentClasses')
        if (!isset($classes[$setting])) 
            throw new Vpc_Exception("ChildComponentClass '$setting' is not defined in settings.")
        
        $class = $classes[$setting]
        if ($class != $parentClass && !is_subclass_of($class, $parentClass)) 
            throw new Vpc_Exception("$setting '$class' must be a subclass of $parentClass.")
        
        return $class
    

    public function store($key, $val
    
        $this->_store[$key] = $val
    

    public function getStore($key
    
        if (isset($this->_store[$key])) 
            return $this->_store[$key]
        } else 
            return null
        
    

    public function onDelete() {

