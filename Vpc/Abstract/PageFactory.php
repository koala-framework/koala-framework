<?php
class Vpc_Abstract_PageFactory
{
    protected $_component;
    protected $_additionalFactories = array('Vpc_Abstract_PagesFactory');

    public function __construct($component)
    {
        $this->_component = $component;
        $this->_init();
    }

    protected function _init()
    {
        foreach ($this->_additionalFactories as $k=>$f) {
            if (is_string($f)) {
                $this->_additionalFactories[$k] = new $f($this->_component);
            }
        }
    }

    public function getPageCollection()
    {
        return $this->_component->getPageCollection();
    }

    public function getChildPages()
    {
        $ret = array();
        foreach ($this->_additionalFactories as $f) {
            $ret = array_merge($ret, $f->getChildPages());
        }
        return $ret;
    }

    public function getMenuChildPages()
    {
        $ret = array();
        foreach ($this->_additionalFactories as $f) {
            $ret = array_merge($ret, $f->getMenuChildPages());
        }
        return $ret;
    }

    public function getChildPageById($id)
    {
        foreach ($this->_additionalFactories as $f) {
            if ($page = $f->getChildPageById($id)) {
                return $page;
            }
        }
        return null;
    }

    public function getChildPageByFilename($filename)
    {
        foreach ($this->_additionalFactories as $f) {
            if ($page = $f->getChildPageByFilename($filename)) {
                return $page;
            }
        }
        return null;
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

    protected function _getComponentSetting($setting)
    {
        return Vpc_Abstract::getSetting(get_class($this->_component), $setting);
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
    protected final function _createPage($class, $suffix)
    {
        $id = $this->_component->getId();
        $id .= '_' . $suffix;

        $page = new $class($this->_component->getDao(), $id, $this->getPageCollection());
        $page->setParentComponent($this->_component);
        $page->setDbId($this->_component->getDbId().'_'.$suffix);
        return $page;
    }
}
