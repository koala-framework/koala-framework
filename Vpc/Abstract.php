<?php
/**
 * Vivid Planet Component (Vpc)
 * @package Vpc
 * @copyright Copyright (c) 2007, Vivid Planet Software GmbH
 */
abstract class Vpc_Abstract implements Vpc_Interface
{
    protected $_dao;
    private $_id;
    private $_hasGeneratedForFilename = array();
    private $_pageCollection = null;

    private $_store;
    protected $_row;

    protected $_table;
    
    const LOREM_IPSUM = 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.';

    /**
     * Sollte nicht direkt aufgerufen werden, sondern über statische Methoden der Klasse. Kann nicht
     * überschrieben werden, stattdessen sollte setup() verwendet werden.
     *
     * @see createInstance
     * @see createPage
     *
     * @param Vps_Dao DAO
     * @param int Falls Komponenten geschachtelt werden, die componentId der obersten Komponente
     * @param int Id der aktuellen Komponente
     * @param string Falls dynamische Unterseite
     * @param string Falls dynamische Unterkomponente
     */
    public final function __construct(Vps_Dao $dao = null, $id = null, $pageCollection = null)
    {
        if (is_null($dao)) { return; }
        
        $this->_dao = $dao;
        $this->_pageCollection = $pageCollection;
        $this->_id = $this->parseId($id);

        $table = $this->getTable();
        if ($table) {
            $info = $table->info();
            if ($info['primary'] == array(1 => 'page_id', 2 => 'component_key')) {
                $this->_row = $table->find($this->getPageId(), $this->getComponentKey())->current();
                if (!$this->_row) {
                    $this->_row = $table->createRow(get_class($this));
                }
            }
        }

        $this->_init();

        if (Zend_Registry::isRegistered('infolog')) {
            Zend_Registry::get('infolog')->createComponent(get_class($this) . ' - ' . $id);
        }
    }

    /**
     * Wird nach dem Konstruktor aufgerufen. Initialisierungscode in Unterklassen ist hier richtig.
     */
    protected function _init() {}

    public static function createStaticInstance($class)
    {
        return new $class();
    }

    /**
     * Erstellt aus der ID der Komponente die Komponente.
     *
     * @param Vps_Dao DAO
     * @param string ID der Komponente
     * @return Vpc_Abstract
     */
    public static function createInstance(Vps_Dao $dao, $class, $id, $pageCollection = null)
    {
        return self::_createInstance($dao, $class, $id, $pageCollection);
    }

    /**
     * Erstellt eine Komponente, die als neue Seite in den Seitenbaum eingefügt werden
     * kann.
     *
     * Wird von generateHierarchy() verwendet. Wenn eine Unterseite erstellt wird, muss dieser eine
     * neue pageId zugewiesen werden. Diese pageId besteht aus topComponentId und pageKeys/pageTags.
     * Die pageKeys/pageTags dienen zur Unterscheidung bei gleicher topComponentId. pageKeys werden
     * verwendet, wenn bei gleichen Seitenaufbau der Unterseite unterschiedliche Inhalte angezeigt
     * werden sollen, pageTags, wenn die gleichen Inhalte angezeigt werden sollen bzw. nur darauf
     * spezialisierte Komponenten auf den pageTag reagieren und unterschiedliche Inhalte liefern.
     *
     * @see generateHierarchy
     * @param string Klassenname der Komponente, die erstellt werden soll
     * @param int Falls bestehende Komponente aus DB erstellt werden soll (className wird bei Werten != 0 wirkungslos!)
     * @param int Für Unterscheidung in Seitenbaum und des Komponenteninhalts
     * @param int Für Unterscheidung in Seitenbaum ohne Unterscheidung des Komponenteninhalts
     * @param int Wie pageTag, wird jedoch nicht hierarchisch an die URL angehängt, sondern überschrieben
     * @return Vpc_Abstract Komponente, die als Seite im Seitenbaum hinzugefügt werden kann
     * @throws Vpc_Exception Falls pageKeySuffix und pageTagSuffix gleichzeit gesetzt werden
     */
    protected function createPage($class, $pageKeySuffix = '', $pageTagSuffix = '')
    {
        $id = $this->getId();
        if ($pageKeySuffix != '') {
            $id .= '_' . $pageKeySuffix;
        }

        if ($pageTagSuffix != '') {
            $id .= ',' . $pageTagSuffix;
        }

        // Page erstellen
        $page = self::_createInstance($this->getDao(), $class, $id, $this->getPageCollection());

        // Erstellte Komponente hinzufügen
        return $page;
    }
    
    /**
     * Falls eine Komponente Unterkomponenten hat (zB. TextPic hat eine Textbox- und
     * ein Pic-Komponente), werden diese hier erstellt.
     *
     * @param string Klassenname der Komponente, die erstellt werden soll
     * @param int Falls bestehende Komponente aus DB erstellt werden soll (className wird bei Werten != 0 wirkungslos!)
     * @param int Für Unterscheidung des Komponenteninhalts
     * @return Vpc_Abstract Komponente
     */
    protected function createComponent($class, $pageKeySuffix = '')
    {
        $id = $this->getId();
        if ($pageKeySuffix != '') {
            $id .= '-' . $pageKeySuffix;
        }

        // Komponente erstellen
        $component = self::_createInstance($this->getDao(), $class, $id, $this->getPageCollection());

        // Erstellte Komponente hinzufügen
        return $component;
    }

    /**
     * Erstellt die Komponente tatsächlich.
     * @throws Vpc_ComponentNotFoundException Falls Klasse für Komponente nicht gefunden wird
     */
    private static function _createInstance(Vps_Dao $dao, $class, $id, $pageCollection = null)
    {
        // Komponente erstellen
        if (class_exists($class)) {
            $component = new $class($dao, $id, $pageCollection);
        } else {
            throw new Vpc_ComponentNotFoundException("Component '$class' not found.");
        }

        // Decorators hinzufügen
        if (!is_null($component)) {
            $decoratorData = $dao->getTable('Vps_Dao_Pages')->retrieveDecoratorData($component->getId());
            foreach ($decoratorData as $decoratorClass) {
                if (class_exists($decoratorClass)) {
                    $component = new $decoratorClass($dao, $component);
                }
            }
        }

        return $component;
    }

    /**
     * Die id identifiziert jede Komponente (auch Unterkomponente) und kann
     * hier in ihre Bestandteile zerlegt werden.
     *
     * Die id besteht aus componentId_pageKey-componentKey, wobei der pageKey und
     * der componentKey optional sein können und der pageKey aus pageKey und pageTag
     * zusammengesetzt wird. Der pageKey und der componentKey können bei geschachtelt
     * werden (Trennzeichen .).
     *
     * @param string id
     * @return array Array mit Bestandteilen der id
     * @throws Vpc_Exception Falls id nicht auf Muster passt
     */
    public static function parseId($id)
    {
        $keys = array();
        $pattern = self::getIdPattern();
        preg_match("#^$pattern\$#", $id, $keys);

        if ($keys == null) {
            throw new Vpc_Exception("ID $id doesn't match pattern for Id: $pattern");
        }

        $parts['id'] = $keys[0];
        $parts['dbId'] = (int)$keys[1];
        $parts['componentId'] = '';
        $parts['pageId'] = '';
        $parts['componentKey'] = '';
        $parts['pageKey'] = '';
        $parts['pageKeys'] = array();
        $parts['currentComponentKey'] = '';
        $parts['currentPageKey'] = '';
        $parts['currentPageTag'] = '';

        $pageKey = isset($keys[2]) ? $keys[2] : '';
        $pageKeys = array();
        $currentPageKey = '';
        foreach (str_split($pageKey) as $pos => $key) {
            if ($key == ',' || $key == '-' || $key == '_') {
                if ($currentPageKey != '') {
                    $pageKeys[substr($pageKey, 0, $pos)] = $currentPageKey;
                }
                $currentPageKey = $key;
            } else {
                $currentPageKey .= $key;
            }
        }
        if ($currentPageKey != '') {
            $pageKeys[$pageKey] = $currentPageKey;
        }
        foreach ($pageKeys as $currentPageKey => $value) {
            $key = substr($value, 0, 1);
            $val = substr($value, 1);
            if ($key != '-') {
                $parts['pageKeys'][$currentPageKey] = $val;
                $parts['pageKey'] = $currentPageKey;
                if ($key == ',') {
                    $parts['currentPageTag'] = $val;
                    $parts['currentPageKey'] = '';
                } else if ($key == '_') {
                    $parts['currentPageKey'] = $val;
                    $parts['currentPageTag'] = '';
                }
            }
            if ($key != ',') {
                $parts['componentKey'] .= $value;
            }
            if ($key == '-') {
                $parts['currentComponentKey'] = $value;
            }
        }
        $parts['componentId'] = $parts['dbId'] . $parts['componentKey'];
        $parts['pageId'] = $parts['dbId'] . $parts['pageKey'];
        return $parts;
    }

    public static function getIdPattern()
    {
        $pattern = '(\d+)'; // PageId
        $pattern .= '(((-|_|,)\d+)*)?'; // PageKey
        return $pattern;
    }

    /**
     * @return string id der Komponente.
     * @see parseId
     */
    public function getId()
    {
        return (string)$this->_id['id'];
    }

    /**
     * @return string pageId der Komponente.
     * @see parsePageId
     */
    public function getPageId()
    {
        return (string)$this->_id['pageId'];
    }

    /**
     * @return string pageId der Komponente.
     * @see parsePageId
     */
    public function getDbId()
    {
        return (int)$this->_id['dbId'];
    }

    /**
     * Da der pageKey in der URL auch die pageTags beinhalten kann,
     * wird er hier zerlegt und nur die pageKeys zurückgegeben.
     *
     * @return string pageKey, falls es mehrere gibt, durch . aneinandergekettet
     */
    public function getPageKey()
    {
        return (string)$this->_id['pageKey'];
    }

    public function getComponentKey()
    {
        return (string)$this->_id['componentKey'];
    }

    public function getCurrentComponentKey()
    {
        return (string)$this->_id['currentComponentKey'];
    }

    /**
     * Der pageKey wird in Normalfall hierarchisch gespeichert. Hier
     * wird nur der letzte pageKey zurückgegeben.
     *
     * @return string pageTag
     */
    public function getCurrentPageKey()
    {
        return (string)$this->_id['currentPageKey'];
    }

    /**
     * Der pageKey wird in Normalfall hierarchisch gespeichert. Hier
     * wird nur der letzte pageTag zurückgegeben.
     *
     * @return string pageTag
     */
    public function getCurrentPageTag()
    {
        return (string)$this->_id['currentPageTag'];
    }

    /**
     * Durchsucht die aktuelle Komponente und deren Unterkomponenten nach der
     * Komponente mit der entsprechenden id.
     *
     * @param string id der Komponente
     * @return Vpc_Abstract/null
     */
    public function findComponent($id)
    {
        if ($this->getId() == $id) {
            return $this;
        } else {
            foreach ($this->getChildComponents() as $childComponent) {
                $component = $childComponent->findComponent($id);
                if ($component != null) {
                    return $component;
                }
            }
        }
        return null;
    }

    /**
     * Durchsucht die aktuelle Komponente und deren Unterkomponenten nach der
     * Komponente mit der entsprechenden Klasse.
     *
     * @param string Klassenname der gesuchten Komponente
     * @return Vpc_Abstract/null
     */
    public function findComponentByClass($class)
    {
        if (get_class($this) == $class) {
            return $this;
        } else {
            foreach ($this->getChildComponents() as $childComponent) {
                $component = $childComponent->findComponentByClass($class);
                if ($component != null) {
                    return $component;
                }
            }
        }
        return null;
    }

    /**
     * Wird von extern gesetzt, wenn es einen Seitenbaum gibt.
     * @param Vps_PageCollection_Abstract
     */
    public function setPageCollection(Vps_PageCollection_Abstract $pageCollection)
    {
        $this->_pageCollection = $pageCollection;
    }

    /**
     * @return Vpc_PageCollection_Abstract/null Vorsicht! In einer Komponente nicht darauf verlassen, dass es die PageCollection gibt!
     */
    public function getPageCollection()
    {
        return $this->_pageCollection;
    }

    /**
     * Falls eine Komponente Unterseiten im Seitenbaum erstellt, wird das hier gemacht.
     *
     * Standardmäßig werden die Seiten aus dem als Unterseite im Seitenbaum hinzugefügt. Falls
     * eine Komponente dynamisch Unterseiten erstellen will, sollte das in dieser Methode erfolgen.
     * parent::generateHierarchy sollte dennoch aufgerufen werden.
     *
     * Der zweite Parameter bestimmt, ob die Seite als Home ausgeführt wird. Falls die Seite
     * also Home ausgeführt wird, werden die Unterseiten der obersten Ebene hinzugefügt, die
     * Seite fungiert also als Rootpage.
     *
     * @param string Nächster Bestandteil der URL für lazy loading, damit nicht immer alle Unterseiten erstellt werden müssen
     * @param boolean Hierarchie wird im Kontext der Homepage erstellt
     * @return Array mit erstellten Unterseiten
     */
    public function generateHierarchy($filename = '')
    {
        $return = array();
        if (!in_array('', $this->_hasGeneratedForFilename) && !in_array($filename, $this->_hasGeneratedForFilename)) {

            $rows = $this->_dao->getTable('Vps_Dao_Pages')->retrieveChildPagesData($this->getId());
            foreach($rows as $pageRow) {
                if ($filename != '' && $filename != $pageRow['filename']) { continue; }
                $page = self::createInstance($this->getDao(), $pageRow['component_class'], $pageRow['id'], $this->getPageCollection());
                $this->getPageCollection()->addTreePage($page, $pageRow['filename'], $pageRow['name'], $this);
                $r['page'] = $page;
                $r['filename'] = $pageRow['filename'];
                $return[] = $r;
            }

            $this->_hasGeneratedForFilename[] = $filename;
        }

        return $return;
    }

    /**
     * Falls eine Komponente Unterkomponente erstellt, wird das hier gemacht.
     *
     * @return Array mit erstellten Unterkomponente
     */
    public function getChildComponents()
    {
        return array();
    }

    /**
     * Gibt die Variablen für View zurück.
     *
     * Variable 'template' muss immer gesetzt werden.
     *
     * @return array Template-Variablen
     */
    public function getTemplateVars()
    {
        // Template rausfinden
        $template = null;
        $class = get_class($this);
        while (!$template && $class != 'Vpc_Abstract') {
            $file = str_replace('_', DIRECTORY_SEPARATOR, $class) . '.tpl';
            $dirs = explode(PATH_SEPARATOR, get_include_path());
            $x = 0;
            foreach ($dirs as $dir) {
                $x++;
                if ($dir == '.') { $dir = getcwd(); }
                $path = $dir . '/' . $file;
                if (is_file($path)) {
                    $template = $path;
                    break;
                }
            }
            $class = get_parent_class($class);
        }
        if (!$template) {
            throw new Vpc_Exception('Template not found for Component ' . get_class($this));
        }
        
        $vars = array();
        $vars['class'] = get_class($this);
        $vars['id'] = $this->getId();
        $vars['store'] = $this->_store;
        $vars['template'] = $template;
        return $vars;
    }

    /**
     * Informationen über den Aufbau der aktuellen Komponente.
     *
     * Falls eine Komponente Unterkomponenten hat, deren Informationen
     * einschließen. Für jede Komponente wird im Array ein Eintrag mit
     * dem Schlüssel id und dem Wert Klassenname angehängt.
     *
     * @return array ComponentInfo
     */
    public function getComponentInfo()
    {
        return array($this->getId() => get_class($this));
    }

    /**
     * @return DAO der Komponente
     */
    public function getDao()
    {
        return $this->_dao;
    }

    /**
     * @return array
     */
    public function saveFrontendEditing(Zend_Controller_Request_Http $request)
    {
        return array();
    }

    /**
     * Shortcut, fragt vom Seitenbaum die Url für eine Komponente ab
     *
     * @param Vpc_Abstract Komponente, für die man die URL wissen will
     * @return string URL der Seite
     */
    protected function getUrl($component = null)
    {
        if ($component == null) { $component = $this; }
        return $this->getPageCollection()->getUrl($component);
    }

    /**
     * Shortcut, fragt vom Seitenbaum, ob die unsichtbaren Einträge
     * auch angezeige werden
     *
     * @return boolean
     */
    protected function showInvisible()
    {
        if ($this->getPageCollection()) {
            return $this->getPageCollection()->showInvisible();
        } else {
            return true;
        }
    }

    /**
     * Shortcut für $this->_dao->getTable($tablename)
     * @param string Name des Models
     */
    public function getTable($tablename = '')
    {
        if ($tablename == '') {
            $tablename = $this->_getSetting('tablename');
        }
        try {
            return $this->_dao->getTable($tablename);
        } catch (Vps_Dao_Exception $e) {
            return null;
        }
    }
    
    public static function getTablename($class)
    {
        return self::$tablename;
    }
    
    public static function getSetting($class, $setting)
    {
        $settings = call_user_func(array($class, 'getSettings'));
        return isset($settings[$setting]) ? $settings[$setting] : null ;
    }

    public static function getSettings()
    {
        return array();
    }

    protected function _getSetting($setting)
    {
        return self::getSetting(get_class($this), $setting);
    }

    protected function _getClassFromSetting($setting, $parentClass) {
        $class = $this->_getSetting($setting);
        if ($class != $parentClass && !is_subclass_of($class, $parentClass)) {
            throw new Vpc_Exception("$setting '$class' must be a subclass of $parentClass.");
        }
        return $class;
    }

    public function store($key, $val)
    {
        $this->_store[$key] = $val;
    }

    public function getStore($key)
    {
        if (isset($this->_store[$key])) {
            return $this->_store[$key];
        } else {
            return null;
        }
    }

    public function onDelete() {}
}
