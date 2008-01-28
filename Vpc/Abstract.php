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
    private $_dbId;
    private $_pageCollection;
    private $_parentComponent;

    private $_store;
    protected $_row;
    private $_tables = array();

    private $_pdfWriter;

    protected $_table;

    const LOREM_IPSUM = 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.';

    private $_childPagesDataCache;

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
        if (is_null($dao)) return;

        $this->_dao = $dao;
        $this->_pageCollection = $pageCollection;

        if (is_object($id)) {
            //vorübergehend für formular-felder
            foreach (Vpc_Abstract::getSetting(get_class($this), 'default') as $k=>$i) {
                if (!isset($id->$k)) $id->$k = $i;
            }
            $this->_row = $id;
        } else {
            $this->_id = $this->parseId($id);
        }

        $this->_init();

        if (Zend_Registry::isRegistered('infolog')) {
            if (!is_string($id)) $id = '(static)';
            Zend_Registry::get('infolog')->createComponent(get_class($this) . ' - ' . $id);
        }
    }

    public function setParentComponent($component)
    {
        $this->_parentComponent = $component;
    }

    public function getParentComponent()
    {
        return $this->_parentComponent;
    }

    protected function _getRow()
    {
        if (!isset($this->_row)) {
            $table = $this->getTable();
            if ($table && !isset($this->_row)) {
                $info = $table->info();
                if ($info['primary'] == array(1 => 'component_id')) {
                    $this->_row = $table->find($this->getDbId())->current();
                    if (!$this->_row) {
                        $this->_row = $table->createRow();
                    }
                }
            }
        }
        return $this->_row;
    }

    /**
     * Wird nach dem Konstruktor aufgerufen. Initialisierungscode in Unterklassen ist hier richtig.
     */
    protected function _init()
    {
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
     * Falls eine Komponente Unterkomponenten hat (zB. TextPic hat eine Textbox- und
     * ein Pic-Komponente), werden diese hier erstellt.
     *
     * @param string Klassenname der Komponente, die erstellt werden soll
     * @param int Falls bestehende Komponente aus DB erstellt werden soll (className wird bei Werten != 0 wirkungslos!)
     * @param int Für Unterscheidung des Komponenteninhalts
     * @return Vpc_Abstract Komponente
     */
    public function createComponent($class, $suffix)
    {
        $id = $this->getId();
        $id .= '-' . $suffix;

        // Komponente erstellen
        $component = self::_createInstance($this->getDao(), $class, $id, $this->getPageCollection());

        $component->setParentComponent($this);
        $component->setDbId($this->getDbId().'-'.$suffix);

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
        $parts = preg_split("/(_|-)/", $id, -1, PREG_SPLIT_DELIM_CAPTURE);
        if (!$parts) {
            throw new Vpc_Exception("ID '$id' doesn't match pattern for Id: $pattern");
        }
        $idParts = array();
        $idParts['id'] = $id;
        $idParts['pageId'] = $parts[0];
        $idParts['pageKeys'] = array($parts[0]);
        $idParts['componentKeys'] = array();
        $idParts['currentComponentKey'] = '';
        $idParts['currentPageKey'] = '';

        unset($parts[0]);
        $lastPart = null;
        foreach ($parts as $part) {
            if ($lastPart == '_') {
                $idParts['currentPageKey'] = $part;
                $idParts['pageId'] .= $lastPart . $part;
                $idParts['pageKeys'][] = $part;
            } else if ($lastPart == '-') {
                $idParts['componentKeys'][] = $part;
                $idParts['currentComponentKey'] = $part;
            }
            $lastPart = $part;
        }
        return $idParts;
    }

    /**
     * @return string id der Komponente.
     * @see parseId
     */
    public function getId()
    {
        return (string)$this->_id['id'];
    }

    public function getDbId()
    {
        $c = Vpc_Admin::getComponentFile($this, 'IdTranslator', 'php', true);
        $translator = new $c();
        if (isset($this->_dbId)) {
            $id = $this->_dbId;
        } else {
            $id = $this->getId();
        }
        return $translator->collapse($id);
    }
    public function setDbId($id)
    {
        $this->_dbId = $id;
    }
    /**
     * @return string pageId der Komponente.
     * @see parsePageId
     */
    public function getPageId()
    {
        return (string)$this->_id['pageId'];
    }

    public function getCurrentComponentKey()
    {
        return (string)$this->_id['currentComponentKey'];
    }

    public function getCurrentPageKey()
    {
        return (string)$this->_id['currentPageKey'];
    }

    /**
     * Durchsucht die aktuelle Komponente und deren Unterkomponenten nach der
     * Komponente mit der entsprechenden id.
     *
     * @param string id der Komponente
     * @return Vpc_Abstract/null
     */
    public function getComponentById($id)
    {
        if ($this->getId() == $id) {
            return $this;
        } else {
            foreach ($this->getChildComponents() as $childComponent) {
                $component = $childComponent->getComponentById($id);
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
    public function getComponentByClass($class)
    {
        if (get_class($this) == $class) {
            return $this;
        } else {
            foreach ($this->getChildComponents() as $childComponent) {
                $component = $childComponent->getComponentByClass($class);
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
        $vars = array();
        $vars['assets']['js'] = array();
        $vars['assets']['css'] = array();
        $vars['class'] = get_class($this);
        $vars['id'] = $this->getId();
        $vars['store'] = $this->_store;
        $vars['template'] = Vpc_Admin::getComponentFile(get_class($this), '', 'tpl');
        $vars['isOffline'] =
            isset($_SERVER['SERVER_NAME']) &&
            substr($_SERVER['SERVER_NAME'], -6) == '.vivid';
        if (!$vars['template']) {
            throw new Vpc_Exception('Template not found for Component ' . get_class($this));
        }
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

    protected function _getParam($param)
    {
        return isset($_REQUEST[$param]) ? $_REQUEST[$param] : null;
    }

    /**
     * Shortcut, fragt vom Seitenbaum die Url für eine Komponente ab
     *
     * @return string URL der Seite
     * @todo protected machen
     */
    public function getUrl()
    {
        return $this->getPageCollection()->getUrl($this);
    }

    /**
     * Shortcut, fragt vom Seitenbaum, ob die unsichtbaren Einträge
     * auch angezeige werden
     *
     * @return boolean
     */
    protected function _showInvisible()
    {
        if ($this->getPageCollection()) {
            return $this->getPageCollection()->showInvisible();
        } else {
            return true;
        }
    }

    /**
     * @deprecated
     */
    protected function showInvisible()
    {
        return $this->_showInvisible();
    }

    /**
     * Shortcut für $this->_dao->getTable($tablename)
     * @param string Name des Models
     */
    public function getTable($tablename = null)
    {
        if (!$tablename) {
            $tablename = $this->_getSetting('tablename');
            if (!$tablename) {
                return null;
            }
        }
        try {
            if (!isset($this->_tables[$tablename])) {
                $this->_tables[$tablename] = new $tablename(array('componentClass'=>get_class($this)));
            }
            return $this->_tables[$tablename];
        } catch (Vps_Dao_Exception $e) {
            return null;
        }
    }

    public static function getSetting($class, $setting)
    {
        if (!class_exists($class)) {
            $class = substr($class, 0, strrpos($class, '_')) . '_Component';
        }
        if (class_exists($class)) {
            $settings = call_user_func(array($class, 'getSettings'));
            return isset($settings[$setting]) ? $settings[$setting] : null ;
        } else {
            return null;
        }
    }

    public static function getSettings()
    {
        return array('assets'=>array('files'=>array(), 'dep'=>array()),
                     'assetsAdmin'=>array('files'=>array(), 'dep'=>array()),
        );
    }

    protected function _getSetting($setting)
    {
        return self::getSetting(get_class($this), $setting);
    }

    protected function _getClassFromSetting($setting, $parentClass) {
        $classes = $this->_getSetting('childComponentClasses');
        if (!isset($classes[$setting])) {
            throw new Vpc_Exception("ChildComponentClass '$setting' is not defined in settings.");
        }
        $class = $classes[$setting];
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

    public function onDelete()
    {
    }

    public function getPdfWriter($pdf)
    {
        if (!isset($this->_pdfWriter)) {
            $class = Vpc_Admin::getComponentFile(get_class($this), 'Pdf', 'php', true);
            $this->_pdfWriter = new $class($this, $pdf);
        }
        return $this->_pdfWriter;
    }

    public function getPageFactory()
    {
        if (!isset($this->_pageFactory)) {
            $c = Vpc_Admin::getComponentFile($this, 'PageFactory', 'php', true);
            $this->_pageFactory = new $c($this);
        }
        return $this->_pageFactory;
    }
}
