<?php
/**
 * Vivid Planet Component (Vpc)
 * @package Vpc
 * @copyright Copyright (c) 2007, Vivid Planet Software GmbH
 */
abstract class Vpc_Abstract implements Vpc_Interface
{
    protected $_dao;
    private $_topComponentId;
    private $_componentId;
    protected $_pageKey;
    private $_componentKey;
    private $_hasGeneratedForFilename = array();
    private $_pageCollection = null;
    private $_params = array();

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
    public final function __construct(Vps_Dao $dao, $topComponentId, $componentId, $pageKey = '', $componentKey = '')
    {
        $this->_dao = $dao;
        $this->_topComponentId = (int)$topComponentId;
        $this->_componentId = (int)$componentId;
        $this->_pageKey = $pageKey;
        $this->_componentKey = $componentKey;
        $params = $this->getParams();
        // TODO: aus components.ini Parameter auslesen
        $this->_params = $params;
        $this->setup();
    }

    /**
     * Wird nach dem Konstruktor aufgerufen. Initialisierungscode in Unterklassen ist hier richtig.
     */
    protected function setup() {}

    /**
     * Erstellt aus der ID der Komponente die Komponente.
     *
     * @param Vps_Dao DAO
     * @param string ID der Komponenten (nicht die componentId, sonder die gesamte ID)
     * @return Vpc_Abstract
     */
    public static function createInstance(Vps_Dao $dao, $id)
    {
        $parsedId = self::parseId($id);
        return self::_createInstance($dao, $parsedId['componentId'], $parsedId['componentId'], $parsedId['pageKey'], '', $parsedId['componentKey']);
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
    protected function createPage($className, $componentId = 0, $pageKeySuffix = '', $pageTagSuffix = '', $pageTag = '')
    {
        // Benötige Daten ggf. holen
        if ($componentId == 0) {
            $componentId = $this->getTopComponentId();
        }

        if ($pageKeySuffix != '' && $pageTagSuffix != '') {
            throw new Vpc_Exception('Only one of $pageKeySuffix and $pageTagSuffix can have a value.');
        }

        if ($pageTag != '') {
            $pageKey = $pageTag . 't';
        } else {
            $pageKey = $this->_pageKey;
            if ($pageKeySuffix != '') {
                if ($pageKey != '') { $pageKey .= '.'; }
                $pageKey .= $pageKeySuffix;
            }
            if ($pageTagSuffix != '') {
                if ($pageKey != '') { $pageKey .= '.'; }
                $pageKey .= $pageTagSuffix . 't';
            }
        }

        // Page erstellen
        $page = self::_createInstance($this->getDao(), $componentId, $componentId, $pageKey, '', $className);

        // Zu Komponente ggf. PageCollection hinzufügen
        if (!is_null($page) && !is_null($this->_pageCollection)) {
            $page->setPageCollection($this->_pageCollection);
        }

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
    protected function createComponent($className, $componentId = 0, $componentKeySuffix = '')
    {
        // Benötige Daten ggf. holen
        if ($componentId == 0) {
            $componentId = $this->getComponentId();
        }

        $componentKey = $this->getComponentKey();
        if ($componentKey != '' && $componentKeySuffix != '') { $componentKey .= '_'; }
        $componentKey .= $componentKeySuffix;

        // Komponente erstellen
        $component = self::_createInstance($this->getDao(), $this->getTopComponentId(), $componentId, $this->_pageKey, $componentKey, $className);

        // Zu Komponente ggf. PageCollection hinzufügen
        if (!is_null($component) && !is_null($this->_pageCollection)) {
            $component->setPageCollection($this->_pageCollection);
        }

        // Erstellte Komponente hinzufügen
        return $component;
    }

    /**
     * Erstellt die Komponente tatsächlich.
     * @throws Vpc_ComponentNotFoundException Falls Klasse für Komponente nicht gefunden wird
     */
    private static function _createInstance(Vps_Dao $dao, $topComponentId, $componentId, $pageKey = '', $componentKey = '', $className = '')
    {
        if ($className == '') {
            $data = $dao->getTable('Vps_Dao_Pages')->retrievePageData($componentId);
            if ($data) {
                $className = $data['component'];
            }
        }

        // Komponente erstellen
        try {

            if (class_exists($className)) {
                $component = new $className($dao, $topComponentId, $componentId, $pageKey, $componentKey);
            }

            // Decorators hinzufügen
            if (!is_null($component)) {
                $decoratorData = $dao->getTable('Vps_Dao_Pages')->retrieveDecoratorData($component->getId());
                foreach ($decoratorData as $className) {
                    if (class_exists($className)) {
                        $component = new $className($dao, $component);
                    }
                }
            }

        } catch (Zend_Exception $e) {
            throw new Vpc_ComponentNotFoundException("Component '$className' not found.");
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
        $pattern  = '^';
        $pattern .= '(\d+)'; // ComponentId
        $pattern .= '(_\d+(\.\d+)*)?'; // PageKey
        $pattern .= '(-\d+(\.\d+)*)?'; // ComponentKey
        $pattern .= '$';
        preg_match("#$pattern#", $id, $keys);

        if ($keys == null) {
            throw new Vpc_Exception("ID $id doesn't match pattern for Id: $pattern");
        }

        $parts['id'] = $keys[0];
        $parts['componentId'] = (int)$keys[1];
        $parts['pageKey'] = isset($keys[2]) ? substr($keys[2], 1) : '';
        $parts['componentKey'] = isset($keys[4]) ? substr($keys[4], 1) : '';
        $parts['pageKeys'] = $parts['pageKey'] != '' ? explode('.', $parts['pageKey']) : array();
        $parts['componentKeys'] = $parts['componentKey'] != '' ? explode('.', $parts['componentKey']) : array();
        return $parts;
    }

    /**
     * Da das pageIdPattern auch zum Parsen der URL im Seitenbaum verwendet wird,
     * hier zentral gespeichert.
     *
     * @return string Regulärer Ausdruck für das Parsen der pageId
     */
    public static function getPageIdPattern()
    {
        $pattern = '(\d+)'; // TopComponentId
        $pattern .= '(_\d+t?(\.\d+t?)*)?'; // PageKey
        return $pattern;
    }

    /**
     * Die pageId identifiziert jede Seite im Seitenbaum und kann hier in ihre
     * Bestandteile zerlegt werden.
     *
     * Die pageId besteht aus topComponentId_pageKey, wobei der pageKey optional sein
     * und aus pageKey und pageTag zusammgengesetzt sein kann. Der pageKey kann bei
     * mehreren Ebenen von dynamischen Unterseiten geschachtelt werden (Trennzeichen .).
     *
     * @param string pageId
     * @return array Array mit Bestandteilen der pageId
     * @throws Vpc_Exception Falls pageId nicht auf Muster passt
     */
    public static function parsePageId($id)
    {
        $keys = array();
        preg_match('#^' . self::getPageIdPattern() . '$#', $id, $keys);

        if ($keys == null) {
            throw new Vpc_Exception("ID $id doesn't match pattern for pageId: $pattern");
        }

        $parts['id'] = $keys[0];
        $parts['topComponentId'] = (int)$keys[1];
        $parts['pageKey'] = isset($keys[2]) && $keys[2] != '' ? substr($keys[2], 1) : '';
        $parts['pageKeys'] = $parts['pageKey'] != '' ? explode('.', $parts['pageKey']) : array();
        return $parts;
    }

    /**
     * @return string id der Komponente.
     * @see parseId
     */
    public function getId()
    {
        $ret = (string)$this->getComponentId();
        if ($this->getPageKey() != '') {
            $ret .= '_' . $this->getPageKey();
        }
        if ($this->getComponentKey() != '') {
            $ret .= '-' . $this->getComponentKey();
        }
        return $ret;
    }

    /**
     * @return string pageId der Komponente.
     * @see parsePageId
     */
    public function getPageId()
    {
        $pageId = (string)$this->_topComponentId;
        if ($this->_pageKey != '') {
            $pageId .= '_' . $this->_pageKey;
        }
        return $pageId;
    }

    /**
     * @return int componentId in der Tabelle vps_components
     */
    protected function getComponentId()
    {
        return (int)$this->_componentId;
    }

    /**
     * @return int Falls Komponenten verschachtelt wurden, componentId der obersten Komponente
     */
    protected function getTopComponentId()
    {
        return (int)$this->_topComponentId;
    }

    /**
     * @return string componentKey
     */
    protected function getComponentKey()
    {
        return $this->_componentKey;
    }

    /**
     * Da der pageKey in der URL auch die pageTags beinhalten kann,
     * wird er hier zerlegt und nur die pageKeys zurückgegeben.
     *
     * @return string pageKey, falls es mehrere gibt, durch . aneinandergekettet
     */
    protected function getPageKey()
    {
        $parts = array();
        foreach (explode('.', $this->_pageKey) as $part) {
            if (substr($part, -1) != 't') {
                $parts[] = $part;
            }
        }
        return implode('.', $parts);
    }

    /**
     * Da der pageKey in der URL auch die pageTags beinhaltet,
     * wird er hier zerlegt und nur die pageTags zurückgegeben.
     *
     * @return string pageTag, falls es mehrere gibt, durch . aneinandergekettet
     */
    protected function getPageTag()
    {
        $parts = array();
        foreach (explode('.', $this->_pageKey) as $part) {
            if (substr($part, -1) == 't') {
                $parts[] = substr($part, 0, -1);
            }
        }
        return implode('.', $parts);
    }

    /**
     * Der pageKey wird in Normalfall hierarchisch gespeichert. Hier
     * wird nur der letzte pageKey zurückgegeben.
     *
     * @return string pageTag
     */
    protected function getCurrentPageKey()
    {
        $pageKeys = explode('.', $this->getPageKey());
        return array_pop($pageKeys);
    }

    /**
     * Der pageKey wird in Normalfall hierarchisch gespeichert. Hier
     * wird nur der letzte pageTag zurückgegeben.
     *
     * @return string pageTag
     */
    protected function getCurrentPageTag()
    {
        $pageKeys = explode('.', $this->getPageTag());
        return array_pop($pageKeys);
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
    protected function getPageCollection()
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
     * @param string Nächster Bestandteil der URL für lazy loading, damit nicht immer alle Unterseiten erstellt werden müssen
     * @return Array mit erstellten Unterseiten
     */
    public function generateHierarchy($filename = '')
    {
        $return = array();
        if (!in_array('', $this->_hasGeneratedForFilename) && !in_array($filename, $this->_hasGeneratedForFilename)) {

            $rows = $this->_dao->getTable('Vps_Dao_Pages')->retrieveChildPagesData($this->getComponentId());
            foreach($rows as $pageRow) {
                if ($filename != '' && $filename != $pageRow['filename']) { continue; }
                $page = $this->createPage('', $pageRow['component_id']);
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
     * @param mode Für Frontend-Editing, noch nicht fertig
     * @return array Template-Variablen
     */
    public function getTemplateVars($mode)
    {
        $ret['id'] = $this->getId();
        return $ret;
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
    protected function getDao()
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
     * @return Array mit Schlüssel Parameter und Wert Parameterwert
     */
    public static function getParams()
    {
        return array();
    }

    /**
     * Überschreibt manuell einen Wert für einen Parameter
     *
     * @param string Parameter
     * @param mixed Wert
     */
    public final function setParam($key, $val)
    {
        $params = $this->getParams();
        if (!isset($params[$key])) {
            throw new Vpc_Exception('Parameter for Component ' . get_class($this) . ' not valid: ' . $key);
        }
        
        $this->_params[$key] = $val;
    }
}
