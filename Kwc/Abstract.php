<?php
abstract class Kwc_Abstract extends Kwf_Component_Abstract
{
    private $_data;
    protected $_row;
    private $_pdfWriter;
    const LOREM_IPSUM = 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.';

    public function __construct(Kwf_Component_Data $data)
    {
        $this->_data = $data;
        parent::__construct();
        Kwf_Benchmark::count('components', $data->componentClass.' '.$data->componentId);
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getData()
    {
        return $this->_data;
    }

    public function getDbId()
    {
        return $this->getData()->dbId;
    }

    public function getComponentId()
    {
        return $this->getData()->componentId;
    }

    protected function _getParam($param)
    {
        return isset($_REQUEST[$param]) ? $_REQUEST[$param] : null;
    }

    /**
     * Shortcut, fragt vom Seitenbaum die Url für eine Komponente ab
     *
     * @return string URL der Seite
     */
    public function getUrl()
    {
        return $this->getData()->getPage()->url;
    }

    public function getName()
    {
        return $this->getData()->getPage()->name;
    }

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = true;
        $ret['allowIsolatedRender'] = false;
        $ret['contentSender'] = 'Kwf_Component_Abstract_ContentSender_Default';
        return $ret;
    }

    /**
     * get child component classes of a componentclass or a componentData
     *
     * @param string/Kwf_Component_Data if data inherited generators are returned as well
     * @param array Optional filtering (string to get for one generator)
     */
    public static function getChildComponentClasses($class, $select = array())
    {
        if (is_string($select) && is_string($class)) {
            //simple case no. 1: get from specific generator
            $g = Kwc_Abstract::getSetting($class, 'generators');
            $ret = $g[$select]['component'];
            if (!is_array($ret)) $ret = array($select => $ret);
            foreach ($ret as $k=>$i) {
                if (!$i) unset($ret[$k]);
            }
            return $ret;
        } else if (!$select && is_string($class)) {
            //simple case no. 2: get 'em all
            $ret = array();
            foreach (Kwc_Abstract::getSetting($class, 'generators') as $g) {
                if (is_array($g['component'])) {
                    foreach ($g['component'] as $c) {
                        if ($c) $ret[] = $c;
                    }
                } else if ($g['component']) {
                    $ret[] = $g['component'];
                }
            }
            return array_unique($ret);
        } else if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }

        //not so simple, else we ask Generator_Abstract::getInstances for help
        $ret = array();
        $generators = Kwf_Component_Generator_Abstract::getInstances($class, $select);
        if (!$generators) {
            return $ret;
        }
        foreach ($generators as $generator) {
            $c = $generator->getChildComponentClasses($select);
            if (!$select->hasPart(Kwf_Component_Select::WHERE_GENERATOR)) {
                $c = array_values($c);
            }
            $ret = array_merge($ret, $c);
        }
        if (!$select->hasPart(Kwf_Component_Select::WHERE_GENERATOR)) {
            $ret = array_unique($ret);
        }
        return $ret;
    }

    public static function getIndirectChildComponentClasses($class, $select = array())
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }
        $cacheId = $select->getHash();
        $ret = self::_getIndirectChildComponentClasses($class, $select, $cacheId);
        return $ret;
    }

    private static function _getIndirectChildComponentClasses($class, $select, $cacheId)
    {
        static $ccc = array();

        $currentCacheId = 'iccc-'.md5($class.$cacheId);

        if (isset($ccc[$class.$cacheId])) {
            Kwf_Benchmark::count('iccc cache hit');
            return $ccc[$class.$cacheId];
        }
        $ret = Kwf_Cache_Simple::fetch($cacheId, $success);
        if ($success) {
            $ccc[$class.$cacheId] = $ret;
            Kwf_Benchmark::count('iccc cache semi-hit');
            return $ret;
        }

        Kwf_Benchmark::count('iccc cache miss', $class.' '.print_r($select->getParts(), true));
        $childConstraints = array('page' => false);
        $ccc[$class.$cacheId] = array();
        foreach (Kwc_Abstract::getChildComponentClasses($class, $childConstraints) as $childClass) {
            if (Kwc_Abstract::getChildComponentClasses($childClass, $select, $cacheId)) {
                $ccc[$class.$cacheId][] = $childClass;
                continue;
            }
            $classes = Kwc_Abstract::_getIndirectChildComponentClasses($childClass, $select, $cacheId);
            if ($classes) {
                $ccc[$class.$cacheId][] = $childClass;
            }
        }
        $ccc[$class.$cacheId] = array_unique(array_values($ccc[$class.$cacheId]));

        Kwf_Cache_Simple::add($currentCacheId, $ccc[$class.$cacheId]);
        return $ccc[$class.$cacheId];

    }

    public static function getChildComponentClass($class, $generator, $componentKey = null)
    {
        $constraints = array(
            'generator' => $generator,
        );
        if ($componentKey) $constraints['componentKey'] = $componentKey;
        $classes = array_values(self::getChildComponentClasses($class, $constraints));
        if (!isset($classes[0])) {
            throw new Kwf_Exception("childComponentClass '$componentKey' for generator '$generator' not set for '$class'");
        }
        return $classes[0];
    }

    public static function hasChildComponentClass($class, $generator, $componentKey = null)
    {
        $constraints = array(
            'generator' => $generator,
            'componentKey' => $componentKey
        );
        $classes = self::getChildComponentClasses($class, $constraints);
        return isset($classes[0]);
    }

    public function getRow()
    {
        return $this->_getRow();
    }

    protected function _getRow()
    {
        if (!isset($this->_row)) {
            $model = $this->getModel();
            if (!$model) return null;
            if ($model instanceof Kwf_Model_Interface) {

                $dbId = $this->getDbId();
                $sharedDataClass = self::getFlag($this->getData()->componentClass, 'sharedDataClass');
                if ($sharedDataClass) {
                    $component = $this->getData();
                    while ($component) {
                        if (is_instance_of($component->componentClass, $sharedDataClass))
                            $dbId = $component->dbId;
                        $component = $component->parent;
                    }
                }

                $this->_row = $model->getRow($dbId);
                if (!$this->_row) {
                    $this->_row = $model->createRow();
                    $this->_row->component_id = $dbId;
                }
            } else {
                $this->_row = $model->find($this->getDbId())->current();
            }
        }
        return $this->_row;
    }

    /**
     * Gibt an, ob eine Komponente Inhalt hat
     *
     * Wird verwendet in Templates um zu prüfen, ob eine Komponente einen Inhalt
     * hat (z.B. Text oder Download)
     *
     * @return boolean $hasContent Ob die Komponente Inhalt hat (true) oder nicht (false)
     */
    public function hasContent()
    {
        return true;
    }

    public function getPdfWriter($pdf)
    {
        if (!isset($this->_pdfWriter)) {
            $class = Kwc_Admin::getComponentFile(get_class($this), 'Pdf', 'php', true);
            $this->_pdfWriter = new $class($this, $pdf);
        }
        return $this->_pdfWriter;
    }

    /**
     * Gibt die Variablen für View zurück.
     *
     * @return array Template-Variablen
     */
    public function getTemplateVars()
    {
        $ret = array();
        $ret['placeholder'] = $this->_getPlaceholder();
        $ret['cssClass'] = self::getCssClass($this);
        $ret['data'] = $this->getData();
        $ret['row'] = $this->_getRow();
        return $ret;
    }

    protected function _getPlaceholder($placeholder = null)
    {
        $ret = $this->_getSetting('placeholder');
        if ($placeholder) {
            return $this->getData()->trlStaticExecute($ret[$placeholder]);
        }
        foreach ($ret as $k => $v) {
            $ret[$k] = $this->getData()->trlStaticExecute($v);
        }
        return $ret;
    }

    /**
     * Data i.e. for json or xml output
     */
    public function getExportData()
    {
        throw new Kwf_Exception_NotYetImplemented("getExportData is not yet implemented for component '".get_class($this)."'");
    }

    public function getMailVars($user = null)
    {
        return $this->getTemplateVars();
    }

    // deprecated
    public function getCacheVars() {
        throw new Kwf_Exception('getCacheVars is not supported anymore.');
    }

    public static function getStaticCacheVars() {
        throw new Kwf_Exception('getStaticCacheVars is not supported anymore.');
    }

    public function getCacheMeta()
    {
        return array();
    }

    public static function getStaticCacheMeta($componentClass) {
        $ret = array();
        if (Kwc_Abstract::hasSetting($componentClass, 'ownModel')) {
            $ret[] = new Kwf_Component_Cache_Meta_Static_OwnModel();
        }
        return $ret;
    }

    public function onCacheCallback($row) {}

    public static function getTemplateFile($componentClass, $filename = 'Component')
    {
        return Kwc_Admin::getComponentFile($componentClass, $filename, 'tpl');
    }

    static public function getCssClass($component)
    {
        if (!is_string($component)) $component = $component->getData()->componentClass;
        return self::getSetting($component, 'processedCssClass');
    }

    static public function getShortcutUrl($componentClass, Kwf_Component_Data $data)
    {
        if (!Kwc_Abstract::hasSetting($componentClass, 'shortcutUrl')) {
            throw new Kwf_Exception("You must either have the setting 'shortcutUrl' or reimplement getShortcutUrl method for '$componentClass'");
        }
        return Kwc_Abstract::getSetting($componentClass, 'shortcutUrl');
    }

    public static function getDataByShortcutUrl($componentClass, $url)
    {
        if (!Kwc_Abstract::hasSetting($componentClass, 'shortcutUrl')) {
            throw new Kwf_Exception("You must either have the setting 'shortcutUrl' or reimplement getDataByShortcutUrl method for '$componentClass'");
        }
        $sc = Kwc_Abstract::getSetting($componentClass, 'shortcutUrl');
        $parts = explode('/', $url);
        $constraints = array();
        $isDomain = is_instance_of(
            Kwf_Component_Data_Root::getInstance()->componentClass,
           'Kwc_Root_DomainRoot_Component'
        );
        if ($isDomain) {
            $pos = strpos($url, '/', 1);
            $domain = substr($url, 0, $pos);
            $url = substr($url, $pos + 1);
        }
        $shortcut = substr($url, 0, strpos($url, '/', 1));
        if ($shortcut != $sc) return false;
        if ($isDomain) {
            $components = Kwf_Component_Data_Root::getInstance()->
                getComponentsByClass('Kwc_Root_DomainRoot_Domain_Component', array('id' => '-' . $domain));
            foreach ($components as $c) {
                if ($c->row->id == $domain) $constraints = array('subroot' => $c);
            }
        }
        $component = Kwf_Component_Data_Root::getInstance()
            ->getComponentBySameClass($componentClass, $constraints);
        if ($component) {
            return $component->getChildPageByPath(substr($url, strlen($sc) + 1));
        }
        return false;
    }

    public static function getComponentClassesByParentClass($class)
    {
        if (!is_array($class)) $class = array($class);

        static $prefix;
        $cacheId = 'cclsbpc-'.implode('-', $class);
        $ret = Kwf_Cache_Simple::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }
        $ret = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (in_array($c, $class) || in_array((strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c), $class)) {
                $ret[] = $c;
                continue;
            }
            foreach (Kwc_Abstract::getParentClasses($c) as $p) {
                if (in_array($p, $class)) {
                    $ret[] = $c;
                    break;
                }
            }
        }
        Kwf_Cache_Simple::add($cacheId, $ret);
        return $ret;
    }
    public static function getComponentClassByParentClass($class)
    {
        $ret = self::getComponentClassesByParentClass($class);
        if (count($ret) != 1) {
            if (!$ret) {
                throw new Kwf_Exception("No Component with class '$class' found");
            }
            throw new Kwf_Exception("More then one component with class '$class' found, there should exist only one");
        }
        return $ret[0];
    }

    public function getViewCacheLifetime()
    {
        return null;
    }

    public function getViewCacheSettings()
    {
        return array(
            'enabled' => $this->_getSetting('viewCache'),
            'lifetime' => $this->getViewCacheLifetime()
        );
    }

    /*
     * Breite dieser Komponente zu ermitteln
     */
    public function getContentWidth()
    {
        if ($this->_hasSetting('contentWidth')) return $this->_getSetting('contentWidth');

        if ($this->getData()->isPage) {
            $p = $this->getData();
            while ($p->parent) $p = $p->parent; //root suchen TODO: wenn mehrere Master-tpl da stoppen
            return $p->getComponent()->_getMasterChildContentWidth($this->getData());
        } else {
            return $this->getData()->parent->getComponent()->_getChildContentWidth($this->getData());
        }
    }

    /*
     * Kann überschrieben werden, um die verfügbare Breite für eine Kindkomponente anzupassen.
     *
     * In den meisten Fällen wird contentWidthSubtract setting reichen
     */
    protected function _getChildContentWidth(Kwf_Component_Data $child)
    {
        $ret = $this->getContentWidth();
        if ($this->_hasSetting('contentWidthSubtract')) {
            $ret -= $this->_getSetting('contentWidthSubtract');
        }
        return $ret;
    }

    /**
     * @deprecated use ContentSender instead
     */
    final public function sendContent() {}
    final protected function _callProcessInput() {}
    final protected function _callPostProcessInput($process) {}

    /**
     * @internal
     */
    public function freeMemory()
    {
        //unset($this->_data);
        if (isset($this->_row)) unset($this->_row);
        if (isset($this->_pdfWriter)) unset($this->_pdfWriter);
    }
}
