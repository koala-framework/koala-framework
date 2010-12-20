<?php
abstract class Vps_Component_Generator_Abstract
{
    protected $_class;
    protected $_settings;
    protected $_pluginBaseComponentClass = false;

    protected $_loadTableFromComponent = false;

    protected $_idSeparator;
    protected $_inherits = false;
    private $_model;
    private $_plugins;

    private static $instances = array();
    private static $_cachedGeneratorKeys = array();

    public function __sleep()
    {
        throw new Vps_Exception("serializing generators is not possible because you could have multiple instances of the same generators");
        $ret = array();
        foreach (array_keys(get_object_vars($this)) as $i) {
            if ($i != '_model' && $i != '_plugins') {
                $ret[] = $i;
            }
        }
        return $ret;
    }

    public function __wakeup()
    {
        throw new Vps_Exception("unserializing generators is not possible");
    }

    protected function __construct($class, $settings)
    {
        $this->_class = $class;
        $this->_settings = $settings;
        $this->_init();
        Vps_Benchmark::count('generators');
    }

    protected function _init()
    {
        if (!is_array($this->_settings['component'])) {
            $this->_settings['component'] = array($this->_settings['generator']
                                            => $this->_settings['component']);
        }
        foreach ($this->_settings['component'] as $k=>$i) {
            if (!$i) unset($this->_settings['component'][$k]);
        }
    }

    protected function _getModel()
    {
        if (!$this->_model) {
            if (isset($this->_settings['model'])) {
                if (is_string($this->_settings['model'])) {
                    $this->_model = Vps_Model_Abstract::getInstance($this->_settings['model']);
                } else {
                    $this->_model = $this->_settings['model'];
                }
            } else {
                if (isset($this->_settings['table'])) {
                    if (is_string($this->_settings['table'])) {
                        $table = new $this->_settings['table'];
                    } else {
                        $table = $this->_settings['table'];
                    }
                    if (!$table instanceof Zend_Db_Table_Abstract) {
                        throw new Vps_Exception("table setting for generator in $this->_class is not a Zend_Db_Table");
                    }
                    $this->_model = new Vps_Model_Db(array('table' => $table));
                } else if ($this->_loadTableFromComponent) {
                    $this->_model = Vpc_Abstract::createChildModel($this->_class);
                } else {
                    throw new Vps_Exception("Can't create model for generator '{$this->getGeneratorKey()}' in '$this->_class'");
                }
            }
        }
        return $this->_model;
    }

    public function getModel()
    {
        return $this->_getModel();
    }

    //um den speicherverbrauch zu reduzieren und fuer tests
    public static function clearInstances()
    {
        self::$instances = array();
        self::$_cachedGeneratorKeys = array();
    }

    /**
     * @return Vps_Component_Generator_Abstract
     */
    public static function getInstance($componentClass, $key, $settings = array(), $pluginBaseComponentClass = false)
    {
        $instanceKey = $componentClass . '_' . $key . '_' . $pluginBaseComponentClass;
        if (!isset(self::$instances[$instanceKey])) {
            if (empty($settings)) {
                $settings = Vpc_Abstract::getSetting($componentClass, 'generators');
                if (!isset($settings[$key])) {
                    throw new Vps_Exception("Generator with key '$key' for '$componentClass' not found.");
                }
                $settings = $settings[$key];
            }
            if (!isset($settings['class'])) {
                throw new Vps_Exception("No Generator-Class set: key '$key' for '$componentClass'");
            }
            if (!class_exists($settings['class'])) {
                throw new Vps_Exception("Generator-Class '{$settings['class']}' does not exist (used in '$componentClass')");
            }
            if (!is_subclass_of($settings['class'], 'Vps_Component_Generator_Abstract')) {
                throw new Vps_Exception("Generator-Class '{$settings['class']}' is not an Vps_Component_Generator_Abstract");
            }
            $settings['generator'] = $key;
            self::$instances[$instanceKey] = new $settings['class']($componentClass, $settings);
            if ($pluginBaseComponentClass) {
                self::$instances[$instanceKey]->_pluginBaseComponentClass = $pluginBaseComponentClass;
            }
        }
        return self::$instances[$instanceKey];
    }

    private static function _getGeneratorsForComponent($componentClass, $pluginBaseComponentClass)
    {
        $ret = array();
        $generators = Vpc_Abstract::getSetting($componentClass, 'generators');
        foreach ($generators as $key => $generator) {
            $ret[] = self::getInstance($componentClass, $key, $generator, $pluginBaseComponentClass);
        }
        return $ret;
    }

    public static function getInstances($component, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        return array_merge(
            self::getOwnInstances($component, $select),
            self::getInheritedInstances($component, $select)
        );
    }

    public static function getOwnInstances($component, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }

        if ($component instanceof Vps_Component_Data) {
            $componentClass = $component->componentClass;
        } else {
            $componentClass = $component;
            $component = null;
        }

        $generatorKeys = self::_getGeneratorKeys(null, $componentClass);

        //performance abkürzung: wenn direkt nach einer id gesucht wird, generator effizienter heraussuchen
        if (($id = $select->getPart(Vps_Component_Select::WHERE_ID)) && !is_numeric(substr($id, 1))) {
            foreach ($generatorKeys as $g) {
                if ($g['childComponentIds'] && in_array($id, $g['childComponentIds'])) {
                    return array(self::getInstance($g['componentClass'], $g['key'], null, $g['pluginBaseComponentClass']));
                }
            }
        }

        $generators = array();
        foreach ($generatorKeys as $g) {
            if (!$g['inherited']) {
                $generators[] = self::getInstance($g['componentClass'], $g['key'], null, $g['pluginBaseComponentClass']);
            }
        }
        return self::_filterGenerators($generators, $component, $componentClass, $select);
    }

    public static function getInheritedInstances($component, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }

        if ($component instanceof Vps_Component_Data) {
            $componentClass = $component->componentClass;
        } else {
            $componentClass = $component;
            $component = null;
        }

        $generators = array();
        foreach (self::_getGeneratorKeys($component, $componentClass) as $g) {
            if ($g['inherited']) {
                $generators[] = self::getInstance($g['componentClass'], $g['key'], null, $g['pluginBaseComponentClass']);
            }
        }
        return self::_filterGenerators($generators, $component, $componentClass, $select);
    }

    private static function _getGeneratorKeys($component, $componentClass)
    {
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix();
        $cacheId = $componentClass;
        if ($component) {
            foreach ($component->inheritClasses as $inheritComponent) {
                $cacheId .= '__' . $inheritComponent;
            }
        }

        if (isset(self::$_cachedGeneratorKeys[$cacheId])) {
            return self::$_cachedGeneratorKeys[$cacheId];
        }
        $ret = apc_fetch($prefix.'genInst-'.$cacheId, $success);
        if ($success) {
            self::$_cachedGeneratorKeys[$cacheId] = $ret;
            return $ret;
        }

        $generators = self::_getGeneratorsForComponent($componentClass, false);
        foreach (Vpc_Abstract::getSetting($componentClass, 'plugins') as $pluginClass) {
            $generators = array_merge($generators, self::_getGeneratorsForComponent($pluginClass, $componentClass));
        }
        $inheritedGenerators = array();
        if ($component && $component->inheritClasses) {
            foreach ($component->inheritClasses as $inheritClass) {
                $gs = Vpc_Abstract::getSetting($inheritClass, 'generators');
                foreach ($gs as $key => $inheritedGenerator) {
                    if (!$inheritedGenerator['component']) {
                        unset($gs[$key]);
                        continue;
                    }
                    if (!isset($inheritedGenerator['inherit']) || !$inheritedGenerator['inherit']) continue;
                    if (!is_instance_of($inheritedGenerator['class'], 'Vps_Component_Generator_Static')) continue;
                    /* Auskommentiert wegen memcached-cache, möglicherweise brauchen wir da noch ein bessere lösung dafür
                    if (is_array($inheritedGenerator['component']) && count($inheritedGenerator['component']) > 1) {
                        unset($gs[$key]);
                        foreach ($inheritedGenerator['component'] as $k=>$c) {
                            if (isset($gs[$k])) {
                                throw new Vps_Exception("Generator '$k' does already exist");
                            }
                            $gs[$k] = $inheritedGenerator;
                            $gs[$k]['component'] = $c;
                        }
                    }
                    */
                }
                foreach ($gs as $key => $inheritedGenerator) {
                    if (!isset($inheritedGenerator['inherit']) || !$inheritedGenerator['inherit']) continue;
                    $inheritedGenerator = self::getInstance($inheritClass, $key, $inheritedGenerator, false);
                    if (!$inheritedGenerator->getGeneratorFlag('box')) {
                        $generators[] = $inheritedGenerator;
                        $inheritedGenerators[] = $inheritedGenerator;
                        continue;
                    }
                    $inheritedBoxes = $inheritedGenerator->getBoxes();
                    foreach ($generators as $k=>$g) {
                        if (!$g->getGeneratorFlag('box')) continue;
                        foreach ($inheritedBoxes as $inheritedBox) {
                            if (in_array($inheritedBox, $g->getBoxes())) {
                                //geerbte box wurde überschrieben, ignorieren
                                continue 3;
                            }
                        }
                    }
                    $generators[] = $inheritedGenerator;
                    $inheritedGenerators[] = $inheritedGenerator;
                }
            }
/*
                foreach ($generators as $k=>$g) {
                    if ($g->getGeneratorFlag('box') && !$g->getBoxes()) {
                        unset($generators[$k]);
                    }
                }
*/
        }
        $ret = array();
        foreach ($generators as $g) {
            $childComponentIds = null;
            if ($g instanceof Vps_Component_Generator_Static) {
                $childComponentIds = array();
                foreach (array_keys($g->_settings['component']) as $c) {
                    $childComponentIds[] = $g->getIdSeparator().$c;
                }
            }
            $ret[] = array(
                'componentClass' => $g->_class,
                'key' => $g->_settings['generator'],
                'pluginBaseComponentClass' => $g->_pluginBaseComponentClass,
                'inherited' => in_array($g, $inheritedGenerators, true),
                'childComponentIds' => $childComponentIds
            );
        }
        apc_add($prefix.'genInst-'.$cacheId, $ret);

        self::$_cachedGeneratorKeys[$cacheId] = $ret;
        return $ret;
    }

    private static function _filterGenerators(array $generators, $component, $componentClass, Vps_Component_Select $select)
    {
        $selectParts = $select->getParts();

        $ret = array();
        foreach ($generators as $g) {
            //performance: page generator nur zurückgeben wenn: component eine aus pages-tabelle ist oder eine category
            if ($component && $g instanceof Vpc_Root_Category_Generator &&
                !is_numeric($component->componentId)
            ) {
                $hasPageGenerator = false;
                foreach (Vpc_Abstract::getSetting($component->componentClass, 'generators') as $i) {
                    static $isPageGenerator = array();
                    if (!isset($isPageGenerator[$i['class']])) {
                        $isPageGenerator[$i['class']] = is_instance_of($i['class'], 'Vpc_Root_Category_Generator');
                    }
                    if ($isPageGenerator[$i['class']]) {
                        $hasPageGenerator = true;
                        break;
                    }
                }
                if (!$hasPageGenerator) continue;
            }
            if (isset($selectParts[Vps_Component_Select::WHERE_GENERATOR_CLASS])) {
                $value = $selectParts[Vps_Component_Select::WHERE_GENERATOR_CLASS];
                if (!$g instanceof $value) {
                    continue;
                }
            }
            if (isset($selectParts[Vps_Component_Select::WHERE_GENERATOR])) {
                $value = $selectParts[Vps_Component_Select::WHERE_GENERATOR];
                if ($g->_class != $componentClass || $value != $g->_settings['generator']) {
                    continue;
                }
            }
            if (isset($selectParts[Vps_Component_Select::WHERE_FILENAME])) {
                if (!isset($selectParts[Vps_Component_Select::WHERE_PSEUDO_PAGE])) {
                    $selectParts[Vps_Component_Select::WHERE_PSEUDO_PAGE] = true;
                }
            }

            $flags = array(
                Vps_Component_Select::WHERE_PAGE_GENERATOR => 'pageGenerator',
                Vps_Component_Select::WHERE_PAGE => 'page',
                Vps_Component_Select::WHERE_PSEUDO_PAGE => 'pseudoPage',
                Vps_Component_Select::WHERE_BOX => 'box',
                Vps_Component_Select::WHERE_MULTI_BOX => 'multiBox'
            );

            foreach ($flags as $part=>$flag) {
                if (isset($selectParts[$part])) {
                    $value = $selectParts[$part];
                    $flags = $g->getGeneratorFlags();
                    if (isset($flags[$flag]) && $flags[$flag]) {
                        if (!$value) continue 2;
                    } else {
                        if ($value) continue 2;
                    }
                }
            }

            if (isset($selectParts[Vps_Component_Select::WHERE_GENERATOR_FLAGS])) {
                $flags = $g->getGeneratorFlags();
                foreach ($selectParts[Vps_Component_Select::WHERE_GENERATOR_FLAGS] as $flag=>$value) {
                    if (!isset($flags[$flag])) $flags[$flag] = null;
                    if ($flags[$flag] != $value) continue 2;
                }
            }

            if (isset($selectParts[Vps_Component_Select::WHERE_UNIQUE])) {
                $value = $selectParts[Vps_Component_Select::WHERE_UNIQUE];
                if (isset($g->_settings['unique']) && $g->_settings['unique']) {
                    if (!$value) continue;
                } else {
                    if ($value) continue;
                }
            }

            if (isset($selectParts[Vps_Component_Select::WHERE_INHERIT])) {
                $value = $selectParts[Vps_Component_Select::WHERE_INHERIT];
                if (isset($g->_settings['inherit']) && $g->_settings['inherit']) {
                    if (!$value) continue;
                } else {
                    if ($value) continue;
                }
            }
            if (isset($selectParts[Vps_Component_Select::WHERE_SHOW_IN_MENU])) {
                $value = $selectParts[Vps_Component_Select::WHERE_SHOW_IN_MENU];
                if (isset($g->_settings['showInMenu']) && $g->_settings['showInMenu']) {
                    if (!$value) continue;
                } else {
                    if ($value) continue;
                }
            }
            if (isset($selectParts[Vps_Component_Select::WHERE_HAS_EDIT_COMPONENTS]) && $selectParts[Vps_Component_Select::WHERE_HAS_EDIT_COMPONENTS]) {
                if (!Vpc_Abstract::hasSetting($g->_class, 'editComponents')) {
                    continue;
                }
                $editComponents = Vpc_Abstract::getSetting($g->_class, 'editComponents');
                if ($g->getGeneratorFlag('table')) {
                    //es kann entweder ein table generator angegeben werden
                    //da kommen dann alle unterkomponenten daher
                    if (!in_array($g->_settings['generator'], $editComponents)) {
                        continue;
                    }
                } else {

                    if (is_array($g->_settings['component'])) {
                        //oder eine komponente eines static generators
                        $continue = true;
                        foreach (array_keys($g->_settings['component']) as $componentKey) {
                            if (in_array($componentKey, $editComponents)) {
                                $continue = false;
                                break;
                            }
                        }
                        if ($continue) continue;
                    } else if (!in_array($key, $editComponents)) {
                        //oder ein static generator (wenn er nur eine unter komponente hat)
                        continue;
                    }
                    if (isset($g->_settings['unique']) && $g->_settings['unique']) {
                        //vererbte, unique nur bei eigener komponente zurückgeben
                        if ($g->_class != $componentClass) continue;
                    }
                }
            }
            if (isset($selectParts[Vps_Component_Select::WHERE_COMPONENT_CLASSES])) {
                $componentClasses = $selectParts[Vps_Component_Select::WHERE_COMPONENT_CLASSES];
                $generatorComponentClasses = $g->_settings['component'];
                if (!is_array($generatorComponentClasses)) {
                    $generatorComponentClasses = array($generatorComponentClasses);
                }
                $continue = true;
                foreach ($generatorComponentClasses as $cc) {
                    if (in_array($cc, $componentClasses)) {
                        $continue = false;
                        break;
                    }
                }
                if ($continue) { continue; }
            }
            if (isset($selectParts[Vps_Component_Select::WHERE_HOME]) && $selectParts[Vps_Component_Select::WHERE_HOME]) {
                if (!$g->getGeneratorFlag('hasHome')) continue;
            }
            if (!$g->getChildComponentClasses($select)) continue;

            $ret[] = $g;
        }

        return $ret;
    }

    public function getChildComponentClasses($select = array())
    {
        if ($select === array() ||
            ($select instanceof Vps_Model_Select &&
                !($select->hasPart(Vps_Component_Select::WHERE_FLAGS) ||
                    $select->hasPart(Vps_Component_Select::WHERE_COMPONENT_KEY)
                 )
            )
        ) {
            return $this->_settings['component']; //performance
        }

        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }

        $ret = $this->_settings['component'];

        if ($select->hasPart(Vps_Component_Select::WHERE_FLAGS)) {
            $flags = $select->getPart(Vps_Component_Select::WHERE_FLAGS);
            foreach ($ret as $k=>$c) {
                foreach ($flags as $f=>$v) {
                    if (Vpc_Abstract::getFlag($c, $f) != $v) {
                        unset($ret[$k]);
                    }
                }
            }
        }

        if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_KEY)) {
            $componentKey = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_KEY);
            if (isset($ret[$componentKey])) {
                $ret = array($ret[$componentKey]);
            } else {
                return array();
            }
        }
        return $ret;
    }

    /**
     * wennn man das select anpassen will _formatSelect überschreiben
     */
    final public function select($parentData, array $select = array())
    {
        $select = new Vps_Component_Select($select);
        $select->whereGenerator($this->_settings['generator']);
        return $select;
    }

    public function getIdSeparator()
    {
        return $this->_idSeparator;
    }

    public function getComponentByKey($key)
    {
        if ($this->_settings['generator'] == $key) {
            return $this->_settings['component'][$key];
        }
        if (isset($this->_settings['component'][$key])) {
            return $this->_settings['component'][$key];
        }
        return null;
    }

    abstract public function getChildData($parentData, $select = array());
    abstract public function getChildIds($parentData, $select = array());

    public function countChildData($parentData, $select = array())
    {
        //Wenn nicht effizient genug, fkt überschreiben!
        return count($this->getChildData($parentData, $select));
    }


    protected function _getChildComponentClass($key, $parentData)
    {
        $c = $this->_settings['component'];
        if ($key) {
            if (!isset($c[$key])) {
                throw new Vps_Exception("ChildComponent with type '$key' for Component '{$this->_class}' not found; set are ".implode(', ', array_keys($c)));
            }
            $componentClass = $this->_settings['component'][$key];
        } else {
            if (count($c) > 1) {
                throw new Vps_Exception("For multiple components you have to submit a key in generator " . get_class($this) . " ($this->_class).");
            }
            reset($this->_settings['component']);
            $componentClass = current($this->_settings['component']);
        }

        $alternativeComponent = Vpc_Abstract::getFlag($componentClass, "alternativeComponent");
        if ($alternativeComponent && call_user_func(array($componentClass, 'useAlternativeComponent'), $componentClass, $parentData, $this)) {
            $componentClass = $alternativeComponent;
        }

        return $componentClass;
    }

    protected function _formatSelectFilename(Vps_Component_Select $select)
    {
        if ($select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
            return null;
        }
        if ($select->hasPart(Vps_Component_Select::WHERE_SHOW_IN_MENU)) {
            return null;
        }
        return $select;
    }

    protected function _formatSelectHome(Vps_Component_Select $select)
    {
        if ($select->hasPart(Vps_Component_Select::WHERE_HOME)) {
            return null;
        }
        return $select;
    }

    protected function _formatSelect($parentData, $select)
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        } else {
            $select = clone $select;
        }

        if (is_null($select)) return null;
        $select = $this->_formatSelectFilename($select);
        if (is_null($select)) return null;
        $select = $this->_formatSelectHome($select);
        if (is_null($select)) return null;
        if ($select->hasPart(Vps_Component_Select::WHERE_FLAGS) || $select->hasPart(Vps_Component_Select::WHERE_COMPONENT_KEY)) {
            $classes = $this->getChildComponentClasses($select);
            $select->whereComponentClasses($classes);
            if ($select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES) === array()) {
                return null;
            }

        }
        return $select;
    }

    protected function _createData($parentData, $row, $select)
    {
        $id = $this->_getIdFromRow($row);
        if (is_array($select)) $select = new Vps_Component_Select($select);
        if ($select && $select->hasPart('whereSubroot')) {
            foreach ($select->getPart('whereSubroot') as $subroot) {
                $subrootLevel = 0;
                $c = $subroot;
                while ($c) {
                    $subrootLevel++;
                    $c = $c->parent;
                }
                $parentLevel = 0;
                $c = $parentData;
                while ($c) {
                    $parentLevel++;
                    $c = $c->parent;
                }
                if ($parentLevel >= $subrootLevel) {
                    $compareData = $parentData;
                    for ($x = 0; $x < ($parentLevel - $subrootLevel); $x++) {
                        $compareData = $compareData->parent;
                    }
                    if ($compareData->componentId != $subroot->componentId) {
                        return null;
                    }
                    break;
                }
            }
        }

        $componentId = $this->_getComponentIdFromRow($parentData, $row);
        $ret = Vps_Component_Data_Root::getInstance()->getFromDataCache($componentId);
        if (!$ret) {
            $config = $this->_formatConfig($parentData, $row);
            if (!$config['componentClass'] || !is_string($config['componentClass'])) {
                throw new Vps_Exception("no componentClass set (id $parentData->componentId $id)");
            }
            $config['id'] = $id;
            if (isset($config['inherits'])) {
                throw new Vps_Exception("You must set Generator::_inherits instead of magically modifying the config");
            }
            if ($this->_inherits) {
                $config['inherits'] = true;
            }
            $config['generator'] = $this; //wird benötigt für duplizieren
            $pageDataClass = $this->_getDataClass($config, $row);
            $ret = new $pageDataClass($config);
            Vps_Component_Data_Root::getInstance()->addToDataCache($ret, $select);
        }
        return $ret;
    }

    protected function _getDataClass($config, $row)
    {
        if (Vpc_Abstract::hasSetting($config['componentClass'], 'dataClass')) {
            return Vpc_Abstract::getSetting($config['componentClass'], 'dataClass');
        } else {
            return 'Vps_Component_Data';
        }
    }

    protected function _getComponentIdFromRow($parentData, $row)
    {
        throw new Vps_Exception('_getComponentIdFromRow has to be implemented for '.get_class($this));
    }

    protected function _formatConfig($parentData, $row) {
        throw new Vps_Exception('_formatConfig has to be implemented for '.get_class($this));
    }
    protected function _getIdFromRow($row) {
        throw new Vps_Exception('_getIdFromRow has to be implemented for '.get_class($this));
    }

    public function toDebug()
    {
        return "<pre>".str_replace('Array', $this->_class.': '.get_class($this),
                                        print_r($this->_settings, true))."</pre>";
    }

    public function getClass()
    {
        return $this->_class;
    }

    public function getGeneratorKey()
    {
        return $this->_settings['generator'];
    }

    /**
     * Wenn Generator von einem Plugin, gibt es Komponenten-Klasse der Komponente
     * die das Plugin beinhaltet zurück.
     * Wenn Generator in einer normalen Komponente false.
     */
    public function getPluginBaseComponentClass()
    {
        return $this->_pluginBaseComponentClass;
    }

    /**
     * OB dieser Generator erbt (normalerweise nur Pages)
     */
    public function getInherits()
    {
        return $this->_inherits;
    }

    public function duplicateChild($source, $parentTarget)
    {
        throw new Vps_Exception_NotYetImplemented();
    }

    public function makeChildrenVisible($source)
    {
        throw new Vps_Exception_NotYetImplemented();
    }

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $data = array();

        $data['icon'] = 'bullet_yellow';
        $data['iconEffects'] = array();
        $data['allowDrag'] = false;
        $data['allowDrop'] = false;

        if (!$generatorClass) $generatorClass = $this->getClass();
        $data['editControllerComponentId'] = $component->componentId;
        $data['editControllerUrl'] = Vpc_Admin::getInstance($generatorClass)
            ->getControllerUrl('Generator');

        $data['loadChildren'] = false;

        return $data;
    }

    public function getGeneratorFlags()
    {
        return array();
    }

    public final function getGeneratorFlag($flag)
    {
        $flags = $this->getGeneratorFlags();
        if (!isset($flags[$flag])) return null;
        return $flags[$flag];
    }

    public final function getGeneratorPlugins()
    {
        if (!isset($this->_plugins)) {
            $this->_plugins = array();
            if (isset($this->_settings['plugins'])) {
                foreach ($this->_settings['plugins'] as $k=>&$p) {
                    $this->_plugins[$k] = new $p($this);
                }
            }
        }
        return $this->_plugins;
    }

    public final function getGeneratorPlugin($key)
    {
        $plugins = $this->getGeneratorPlugins();
        if (isset($plugins[$key])) return $plugins[$key];
        return null;
    }

    public function getStaticCacheVarsForMenu()
    {
        return array();
    }
}
