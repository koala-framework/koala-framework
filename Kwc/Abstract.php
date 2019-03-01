<?php
abstract class Kwc_Abstract extends Kwf_Component_Abstract
{
    private $_data;
    protected $_row;
    private $_pdfWriter;
    const LOREM_IPSUM = 'Lorem ipsum vix at error vocibus, sit at autem liber? Qui eu odio moderatius, populo pericula ex his. Mea hinc decore tempor ei, postulant honestatis eum ut. Eos te assum elaboraret, in ius fastidii officiis electram.';

    /**
     * Constructor; don't create component objects directly, always use Kwf_Component_Data::getComponent()
     */
    public function __construct(Kwf_Component_Data $data)
    {
        $this->_data = $data;
        parent::__construct();
        Kwf_Benchmark::count('components', $data->componentClass.' '.$data->componentId);
    }

    public static function validateSettings($settings, $componentClass)
    {
        if (isset($settings['throwHasContentChangedOnRowColumnsUpdate'])
            && $settings['throwHasContentChangedOnRowColumnsUpdate'] === true
        ) {
            throw new Kwf_Exception("throwHasContentChangedOnRowColumnsUpdate for '$componentClass' has to match column");
        }

        if (isset($settings['componentIcon']) && !is_string($settings['componentIcon'])) {
            throw new Kwf_Exception("componentIcon must be string");
        }
    }

    /**
     * Returns the data object of this component
     *
     * @return Kwf_Component_Data
     */
    public function getData()
    {
        return $this->_data;
    }

    /**
     * Returns the dbId of this component
     *
     * shortcut for ::getData()->dbId;
     *
     * @return string
     * @internal
     * @deprecated
     */
    public function getDbId()
    {
        return $this->getData()->dbId;
    }

    /**
     * Returns the componentId of this component
     *
     * shortcut for ::getData()->componentId;
     *
     * @return string
     * @internal
     * @deprecated
     */
    public function getComponentId()
    {
        return $this->getData()->componentId;
    }

    /**
     * Returns url of this component in the component tree
     *
     * shortcut for ::getData->getPage()->url
     *
     * @return string
     * @internal
     * @deprecated
     */
    public function getUrl()
    {
        return $this->getData()->getPage()->url;
    }

    /**
     * Returns page name of this component in the component tree
     *
     * shortcut for ::getData->getPage()->name
     *
     * @return string
     * @internal
     * @deprecated
     */
    public function getName()
    {
        return $this->getData()->getPage()->name;
    }

    /**
     * Returns static settings of this component
     *
     * Override to change settings
     *
     * @return array
     */
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['viewCache'] = true;
        $ret['contentSender'] = 'Kwf_Component_Abstract_ContentSender_Default';
        $ret['layoutClass'] = 'Kwf_Component_Layout_Default';
        $ret['plugins'] = array();
        return $ret;
    }

    /**
     * Returns child component classes of a componentclass or a componentData
     *
     * @param string|Kwf_Component_Data if data inherited generators are returned as well
     * @param array Optional filtering (string to get for one generator)
     */
    public static function getChildComponentClasses($class, $select = array())
    {
        $selectType = gettype($select);
        if ($selectType == 'string' && is_string($class)) {
            //simple case no. 1: get from specific generator
            $g = Kwc_Abstract::getSetting($class, 'generators');
            if (!isset($g[$select])) return array();
            return $g[$select]['component'];
        } else if (!$select && is_string($class)) {
            //simple case no. 2: get 'em all
            $ret = array();
            foreach (Kwc_Abstract::getSetting($class, 'generators') as $g) {
                $ret = array_merge($ret, array_values($g['component']));
            }
            return array_unique($ret);

        } else if ($selectType == 'array' && is_string($class) && count($select) == 1 &&
            isset($select['componentClass']) && is_string($select['componentClass'])
        ) {
            //simple case no 3: looking for a single comopnentClass
            foreach (Kwc_Abstract::getSetting($class, 'generators') as $g) {
                foreach ($g['component'] as $c) {
                    if ($c == $select['componentClass']) return array($c);
                }
            }
            return array();
        } else if ($selectType == 'array' && is_string($class) && count($select) == 1 &&
            isset($select['generatorFlags']) && $select['generatorFlags'] == array('static'=>true)
        ) {
            //simple case no 4: looking for a generator by flag
            $ret = array();
            foreach (Kwc_Abstract::getSetting($class, 'generators') as $key=>$gSettings) {
                $g = Kwf_Component_Generator_Abstract::getInstance($class, $key, $gSettings);
                if ($g->getGeneratorFlag('static')) {
                    foreach ($gSettings['component'] as $c) {
                        $ret[] = $c;
                    }
                }
            }
            return array_unique($ret);
        } else if ($selectType == 'array' && is_string($class) && count($select) == 2 &&
            isset($select['generator']) && isset($select['componentKey'])
        ) {
            //simple case no 5: looking for a generator plus componentKey
            $ret = array();
            $generators = Kwc_Abstract::getSetting($class, 'generators');
            if (isset($generators[$select['generator']]['component'][$select['componentKey']])) {
                $ret[] = $generators[$select['generator']]['component'][$select['componentKey']];
            }
            return $ret;

        } else if ($selectType == 'array') {
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

    /**
     * Returns indirect child component classes of a componentclass or a componentData
     *
     * @param string|Kwf_Component_Data if data inherited generators are returned as well
     * @param array Optional filtering (string to get for one generator)
     */
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
        $ret = Kwf_Cache_SimpleStatic::fetch($currentCacheId, $success);
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

        Kwf_Cache_SimpleStatic::add($currentCacheId, $ccc[$class.$cacheId]);
        return $ccc[$class.$cacheId];

    }

    /**
     * Returns a single child component class of a componentClass
     *
     * throws an exception if not found
     *
     * @param string componentClass
     * @param string generator key
     * @param string component key
     * @param string
     */
    public static function getChildComponentClass($class, $generator, $componentKey = null)
    {
        $constraints = array(
            'generator' => $generator,
        );
        if ($componentKey) $constraints['componentKey'] = $componentKey;
        $classes = array_values(self::getChildComponentClasses($class, $constraints));
        if (!isset($classes[0])) {
            if (!$componentKey) {
                throw new Kwf_Exception("no component for generator '$generator' not set for '$class'");
            } else {
                throw new Kwf_Exception("childComponentClass '$componentKey' for generator '$generator' not set for '$class'");
            }
        }
        return $classes[0];
    }

    /**
     * Return if a child component class with a given generator key and componentKey exists
     *
     * if returnf false getChildComponentClass will give an exception.
     *
     * @param string componentClass
     * @param string generator key
     * @param string component key
     * @param bool
     */
    public static function hasChildComponentClass($class, $generator, $componentKey = null)
    {
        $constraints = array(
            'generator' => $generator,
            'componentKey' => $componentKey
        );
        $classes = self::getChildComponentClasses($class, $constraints);
        return count($classes) > 0;
    }

    /**
     * @internal
     */
    public function getRow()
    {
        return $this->_getRow();
    }

    /**
     * Returns the row from the ownModel of this component
     *
     * @return Kwf_Model_Row_Abstract
     */
    protected function _getRow()
    {
        if (!isset($this->_row)) {
            $model = $this->getOwnModel();
            if (!$model) return null;
            $dbId = $this->getData()->dbId;
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
        }
        return $this->_row;
    }

    /**
     * Returns if the component has content
     *
     * Can be used to hide eg. empty boxes
     *
     * @return bool if this component has content
     */
    public function hasContent()
    {
        return true;
    }

    /**
     * Returns the Pdf Writer object associated with this component.
     */
    public function getPdfWriter($pdf)
    {
        if (!isset($this->_pdfWriter)) {
            $class = Kwc_Admin::getComponentFile(get_class($this), 'Pdf', 'php', true);
            $this->_pdfWriter = new $class($this, $pdf);
        }
        return $this->_pdfWriter;
    }

    /**
     * Returns variables that can be used in Component.tpl
     * @param e.g. for accessing recipient in Mail_Renderer
     * @return array
     */
    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = array();
        $ret['placeholder'] = $this->_getPlaceholder();
        $ret['rootElementClass'] = self::getRootElementClass($this);
        $ret['bemClass'] = $this->_getBemClass('');

        $ret['data'] = $this->getData();
        $ret['row'] = $this->_getRow();

        $supportedContexts = Kwf_Component_Layout_Abstract::getInstance($this->getData()->componentClass)->getSupportedContexts();
        if ($supportedContexts) {
            $contexts = Kwf_Component_Layout_Abstract::getInstance($this->getData()->componentClass)->getContexts($this->getData());
            if (is_null($contexts)) {
                $e = new Kwf_Exception("Didn't get layout contexts for ".$this->getData()->componentId);
                $e->logOrThrow();
            }
            foreach ($contexts as $ctx) {
                if (!in_array($ctx, $supportedContexts)) {
                    $e = new Kwf_Exception("Master Layout Context ".json_encode($ctx)." is not supported by ".$this->getData()->componentClass." for '".$this->getData()->componentId."'");
                    $e->logOrThrow();
                }
            }
        }
        return $ret;
    }

    public static function getBemClass($component, $class, $nonBemFallback = null)
    {
        if (!is_string($component)) $component = $component->getData()->componentClass;
        static $up;
        if (!isset($up)) $up = Kwf_Config::getValue('application.uniquePrefix');
        if (!$up) {
            if ($nonBemFallback) return $nonBemFallback;
            if (substr($class, 0, 2) == '--') $class = substr($class, 2);
            return $class;
        } else {
            if (substr($class, 0, 2) != '--') $class = '__'.$class;
            return Kwf_Component_Abstract::formatRootElementClass($component, '').$class;
        }
    }

    protected function _getBemClass($class, $nonBemFallback = null)
    {
        return self::getBemClass($this, $class, $nonBemFallback);
    }

    public static function getMasterBemClass($component, $class)
    {
        if (!is_string($component)) $component = $component->getData()->componentClass;
        static $up;
        if (!isset($up)) $up = Kwf_Config::getValue('application.uniquePrefix');
        if (!$up) {
            throw new Kwf_Exception_NotYetImplemented();
        } else {
            if (substr($class, 0, 2) != '--') $class = '__'.$class;
            return Kwf_Component_Abstract::formatRootElementClass($component, '').'Master'.$class;
        }
    }

    protected function _getMasterBemClass($class)
    {
        return self::getMasterBemClass($this, $class);
    }

    public function hasMasterTemplate()
    {
        return (bool)self::getMasterTemplateFile($this->getData()->componentClass);
    }

    /**
     * Returns variables that can be used in Master.tpl
     * @param e.g. for accessing recipient in Mail_Renderer
     * @return array
     */
    public function getMasterTemplateVars(Kwf_Component_Data $innerComponent, Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = array();
        $ret['component'] = $innerComponent;
        $ret['data'] = $innerComponent;
        $ret['template'] = self::getMasterTemplateFile($this->getData()->componentClass);
        $ret['pageLanguage'] = $innerComponent->getLanguage();
        $ret['boxes'] = array();
        foreach ($innerComponent->getPageOrRoot()->getChildBoxes() as $box) {
            $ret['boxes'][$box->box] = $box;
        }

        $ret['multiBoxes'] = array();
        foreach ($innerComponent->getPageOrRoot()->getRecursiveChildComponents(array('multiBox'=>true)) as $box) {
            $ret['multiBoxes'][$box->box][] = $box;
        }
        //sort by priority
        foreach ($ret['multiBoxes'] as $box=>$components) {
            usort($ret['multiBoxes'][$box], array('Kwf_Component_View_Helper_ComponentWithMaster', '_sortByPriority'));
        }


        $up = Kwf_Config::getValue('application.uniquePrefix');
        if (!$up) {
            $ret['bemClass'] = '';
        } else {
            $ret['bemClass'] = Kwf_Component_Abstract::formatRootElementClass($this->getData()->componentClass, '').'Master__';
        }

        $ret['rootElementClass'] = 'kwfUp-frontend '.Kwf_Component_Abstract::formatRootElementClass($this->getData()->componentClass, '').'Master';

        return $ret;
    }

    /**
     * Returns a placeholder text, placeholders are set in settings
     *
     * @return string
     */
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

    /**
     * @deprecated
     */
    public final function getMailVars($user = null)
    {
        throw new Kwf_Exception('not supported anymore, replace by getTemplateVars($renderer)');
    }

    /**
     * Returns path of a template file for a given component
     *
     * @param string componentClass
     * @param string template filename without extension
     * @return string
     */
    public static function getTemplateFile($componentClass, $filename = 'Component')
    {
        return Kwc_Admin::getComponentFile($componentClass, $filename, array('tpl', 'twig'));
    }

    public static function getMasterTemplateFile($componentClass)
    {
        if (self::hasSetting($componentClass, 'masterTemplate')) {
            return self::getSetting($componentClass, 'masterTemplate');
        }
        return self::getTemplateFile($componentClass, 'Master');
    }

    /**
     * Returns the processed rootElementClass used in various places for a component
     *
     * @param string|Kwf_Component_Data
     * @return string
     */
    static public function getRootElementClass($component)
    {
        if (!is_string($component)) $component = $component->getData()->componentClass;
        $ret = '';
        if (self::hasSetting($component, 'rootElementClass')) {
            $ret .= self::getSetting($component, 'rootElementClass').' ';
        }
        $ret .= Kwf_Component_Abstract::formatRootElementClass($component, '');
        return $ret;
    }

    /**
     * Returns componentClasses that match a given class in their inheritance chain
     *
     * Fast, as the result is static and will be cached
     *
     * @param string
     * @return string[]
     */
    public static function getComponentClassesByParentClass($class)
    {
        if (!is_array($class)) $class = array($class);

        static $prefix;
        $cacheId = 'cclsbpc-'.implode('-', $class).'-'.Kwf_Component_Data_Root::getComponentClass();
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
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
        Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        return $ret;
    }

    /**
     * Returns a componentClass that match a given class in their inheritance chain
     *
     * Fast, as the result is static and will be cached
     *
     * will throw an error if multiple are found
     *
     * @param string
     * @return string
     */
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


    /**
     * Returns the view cache lifetime of this component (s)
     *
     * if null (the default) infinite lifetime
     *
     * @return int
     */
    public function getViewCacheLifetime()
    {
        return null;
    }

    /**
     * Returns the view cache settings of this component
     *
     * @return array
     */
    public function getViewCacheSettings()
    {
        return array(
            'enabled' => $this->_getSetting('viewCache'),
            'lifetime' => $this->getViewCacheLifetime()
        );
    }

    /**
     * Returns the master view cache settings of this component
     *
     * @return array
     */
    public function getMasterViewCacheSettings()
    {
        return array(
            'enabled' => true,
            'lifetime' => null
        );
    }

    /**
     * Returns the view plugins that should be used for this component instance
     *
     * Must only return plugins defined in 'plugins' setting.
     * Can also return non-view plugins, those will silently be ignored.
     *
     * Default implementation returns 'plugins' setting.
     */
    public function getActiveViewPlugins()
    {
        $ret = $this->_getSetting('plugins');
        $d = $this->getData();
        if ($d->isPage) {
            while ($d) {
                foreach (Kwc_Abstract::getSetting($d->componentClass, 'pluginsInherit') as $i) {
                    if (!in_array($i, $ret)) $ret[] = $i;
                }
                $d = $d->parent;
            }
        }
        return $ret;
    }

    /**
     * Returns available width of this component
     *
     * use 'contentWidth' setting to set a fixed with
     *
     * @return int
     */
    public function getContentWidth()
    {
        return Kwf_Component_Layout_Abstract::getInstance($this->getData()->componentClass)
            ->getContentWidth($this->getData());
    }

    /**
     * @deprecated use Layout class instead
     * @internal
     */
    protected final function _getChildContentWidth(Kwf_Component_Data $child) {}

    public function getMasterLayoutContexts()
    {
        return Kwf_Component_Layout_Abstract::getInstance($this->getData()->componentClass)
            ->getContexts($this->getData());
    }

    /**
     * @deprecated use ContentSender instead
     * @internal
     */
    final public function sendContent() {}
    /**
     * @deprecated
     * @internal
     */
    final protected function _callProcessInput() {}
    /**
     * @deprecated
     * @internal
     */
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
