<?php
abstract class Kwf_Component_Generator_Abstract
{
    protected $_class;
    protected $_settings;
    protected $_pluginBaseComponentClass = false;

    protected $_loadTableFromComponent = false;

    protected $_idSeparator;
    protected $_inherits = false;
    private $_model;
    private $_plugins;
    private $_getPossibleIndirectDbIdShortcutsCache = array();

    private static $instances = array();
    private static $_cachedGeneratorKeys = array();

    protected $_addUrlPart = false;

    protected $_eventsClass;

    //public static $objectsCount;

    public function getEventsClass()
    {
        return $this->_eventsClass;
    }

    public function __sleep()
    {
        throw new Kwf_Exception("serializing generators is not possible because you could have multiple instances of the same generators");
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
        throw new Kwf_Exception("unserializing generators is not possible");
    }

    protected function __construct($class, $settings)
    {
        //self::$objectsCount++;
        $this->_class = $class;
        $this->_settings = $settings;
        $this->_init();
        Kwf_Benchmark::count('generators');
    }

    public function __destruct()
    {
        //self::$objectsCount--;
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

        if (array_key_exists('addUrlPart', $this->_settings)) {
            $this->_addUrlPart = (bool)$this->_settings['addUrlPart'];
            unset($this->_settings['addUrlPart']);
        }
    }

    protected function _getModel()
    {
        if (!$this->_model) {
            if (isset($this->_settings['model'])) {
                if (is_string($this->_settings['model'])) {
                    $this->_model = Kwf_Model_Abstract::getInstance($this->_settings['model']);
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
                        throw new Kwf_Exception("table setting for generator in $this->_class is not a Zend_Db_Table");
                    }
                    $this->_model = new Kwf_Model_Db(array('table' => $table));
                } else if ($this->_loadTableFromComponent) {
                    $this->_model = Kwc_Abstract::createChildModel($this->_class);
                } else {
                    throw new Kwf_Exception("Can't create model for generator '{$this->getGeneratorKey()}' in '$this->_class'");
                }
            }
            if (!$this->_model) {
                throw new Kwf_Exception('model has to be set for table generator in '.$this->_class);
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
     * @return Kwf_Component_Generator_Abstract
     */
    public static function getInstance($componentClass, $key, $settings = array(), $pluginBaseComponentClass = false)
    {
        $instanceKey = $componentClass . '_' . $key . '_' . $pluginBaseComponentClass;
        if (!isset(self::$instances[$instanceKey])) {
            if (empty($settings)) {
                $settings = Kwc_Abstract::getSetting($componentClass, 'generators');
                if (!isset($settings[$key])) {
                    throw new Kwf_Exception("Generator with key '$key' for '$componentClass' not found.");
                }
                $settings = $settings[$key];
            }
            if (!isset($settings['class'])) {
                throw new Kwf_Exception("No Generator-Class set: key '$key' for '$componentClass'");
            }
            if (!class_exists($settings['class'])) {
                throw new Kwf_Exception("Generator-Class '{$settings['class']}' does not exist (used in '$componentClass')");
            }
            if (!is_subclass_of($settings['class'], 'Kwf_Component_Generator_Abstract')) {
                throw new Kwf_Exception("Generator-Class '{$settings['class']}' is not an Kwf_Component_Generator_Abstract");
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
        $generators = Kwc_Abstract::getSetting($componentClass, 'generators');
        foreach ($generators as $key => $generator) {
            $ret[] = self::getInstance($componentClass, $key, $generator, $pluginBaseComponentClass);
        }
        return $ret;
    }

    public static function getInstances($component, $select = array())
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }
        return array_merge(
            self::getOwnInstances($component, $select),
            self::getInheritedInstances($component, $select)
        );
    }

    public static function getOwnInstances($component, $select = array())
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }

        if ($component instanceof Kwf_Component_Data) {
            $componentClass = $component->componentClass;
        } else if (is_array($component)) {
            $componentClass = $component['componentClass'];
            $component = null;
        } else {
            $componentClass = $component;
            $component = null;
        }

        $generatorKeys = self::_getGeneratorKeys(array(), $componentClass);

        //performance abkürzung: wenn direkt nach einer id gesucht wird, generator effizienter heraussuchen
        if (($id = $select->getPart(Kwf_Component_Select::WHERE_ID)) && !is_numeric(substr($id, 1))) {
            $filteredGeneratorKeys = array();
            foreach ($generatorKeys as $g) {
                if ($g['childComponentIds']) {
                    if (in_array($id, $g['childComponentIds'])) {
                        return array(self::getInstance($g['componentClass'], $g['key'], null, $g['pluginBaseComponentClass']));
                    }
                } else {
                    $filteredGeneratorKeys[] = $g;
                }
            }
            $generatorKeys = $filteredGeneratorKeys;
        }

        //performance abkürzung: wenn direkt nach einem generator gesucht wird, effizienter heraussuchen
        if (($genKey = $select->getPart(Kwf_Component_Select::WHERE_GENERATOR))) {
            foreach ($generatorKeys as $g) {
                if ($g['key'] == $genKey && $g['componentClass']==$componentClass) {
                    return array(self::getInstance($g['componentClass'], $g['key'], null, $g['pluginBaseComponentClass']));
                }
            }
            return array();
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
            $select = new Kwf_Component_Select($select);
        }
        //performance abkürzung: wenn direkt nach einem generator gesucht wird, ist das nie ein inherited
        if (($genKey = $select->getPart(Kwf_Component_Select::WHERE_GENERATOR))) {
            return array();
        }


        if ($component instanceof Kwf_Component_Data) {
            $componentClass = $component->componentClass;
            $inheritClasses = $component->inheritClasses;
        } else if (is_array($component)) {
            $componentClass = $component['componentClass'];
            $inheritClasses = $component['inheritClasses'];
            $component = null;
        } else {
            $componentClass = $component;
            $inheritClasses = array();
            $component = null;
        }

        $generatorKeys = self::_getGeneratorKeys($inheritClasses, $componentClass);

        //performance abkürzung: wenn direkt nach einer id gesucht wird, generator effizienter heraussuchen
        // attention: if select consists of additional parts they won't be recognized - it might be that the generator is returned although it isn't needed
        if (($id = $select->getPart(Kwf_Component_Select::WHERE_ID)) && !is_numeric(substr($id, 1))) {
            $filteredGeneratorKeys = array();
            foreach ($generatorKeys as $g) {
                if ($g['childComponentIds']) {
                    if (in_array($id, $g['childComponentIds'])) {
                        return array(self::getInstance($g['componentClass'], $g['key'], null, $g['pluginBaseComponentClass']));
                    }
                } else {
                    $filteredGeneratorKeys[] = $g;
                }
            }
            $generatorKeys = $filteredGeneratorKeys;
        }

        $generators = array();
        foreach ($generatorKeys as $g) {
            if ($g['inherited']) {
                $generators[] = self::getInstance($g['componentClass'], $g['key'], null, $g['pluginBaseComponentClass']);
            }
        }
        return self::_filterGenerators($generators, $component, $componentClass, $select);
    }

    private static function _getGeneratorKeys(array $inheritClasses, $componentClass)
    {
        $cacheId = $componentClass;
        foreach ($inheritClasses as $inheritComponent) {
            $cacheId .= '__' . $inheritComponent;
        }

        if (isset(self::$_cachedGeneratorKeys[$cacheId])) {
            return self::$_cachedGeneratorKeys[$cacheId];
        }
        $ret = Kwf_Cache_SimpleStatic::fetch('genInst-'.$cacheId, $success);
        if ($success) {
            self::$_cachedGeneratorKeys[$cacheId] = $ret;
            return $ret;
        }

        $generators = self::_getGeneratorsForComponent($componentClass, false);
        foreach (Kwc_Abstract::getSetting($componentClass, 'plugins') as $pluginClass) {
            $generators = array_merge($generators, self::_getGeneratorsForComponent($pluginClass, $componentClass));
        }
        $inheritedGenerators = array();
        if ($inheritClasses) {
            foreach ($inheritClasses as $inheritClass) {
                $gs = Kwc_Abstract::getSetting($inheritClass, 'generators');
                foreach ($gs as $key => $inheritedGenerator) {
                    if (!$inheritedGenerator['component']) {
                        unset($gs[$key]);
                        continue;
                    }
                    if (!isset($inheritedGenerator['inherit']) || !$inheritedGenerator['inherit']) continue;
                    if (!is_instance_of($inheritedGenerator['class'], 'Kwf_Component_Generator_Static')) continue;
                    /* Auskommentiert wegen memcached-cache, möglicherweise brauchen wir da noch ein bessere lösung dafür
                    if (is_array($inheritedGenerator['component']) && count($inheritedGenerator['component']) > 1) {
                        unset($gs[$key]);
                        foreach ($inheritedGenerator['component'] as $k=>$c) {
                            if (isset($gs[$k])) {
                                throw new Kwf_Exception("Generator '$k' does already exist");
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
            $ret[] = array(
                'componentClass' => $g->_class,
                'key' => $g->_settings['generator'],
                'pluginBaseComponentClass' => $g->_pluginBaseComponentClass,
                'inherited' => in_array($g, $inheritedGenerators, true),
                'childComponentIds' => $g->getStaticChildComponentIds()
            );
        }
        Kwf_Cache_SimpleStatic::add('genInst-'.$cacheId, $ret);

        self::$_cachedGeneratorKeys[$cacheId] = $ret;
        return $ret;
    }

    private static function _filterGenerators(array $generators, $component, $componentClass, Kwf_Component_Select $select)
    {
        $selectParts = $select->getParts();

        $ret = array();
        foreach ($generators as $g) {
            //performance: page generator nur zurückgeben wenn: component eine aus pages-tabelle ist oder eine category
            if ($component && $g instanceof Kwc_Root_Category_Generator &&
                !is_numeric($component->componentId)
            ) {
                $hasPageGenerator = false;
                foreach (Kwc_Abstract::getSetting($component->componentClass, 'generators') as $i) {
                    static $isPageGenerator = array();
                    if (!isset($isPageGenerator[$i['class']])) {
                        $isPageGenerator[$i['class']] = is_instance_of($i['class'], 'Kwc_Root_Category_Generator');
                    }
                    if ($isPageGenerator[$i['class']]) {
                        $hasPageGenerator = true;
                        break;
                    }
                }
                if (!$hasPageGenerator) continue;
            }
            if (isset($selectParts[Kwf_Component_Select::WHERE_GENERATOR_CLASS])) {
                $value = $selectParts[Kwf_Component_Select::WHERE_GENERATOR_CLASS];
                if (!$g instanceof $value) {
                    continue;
                }
            }
            if (isset($selectParts[Kwf_Component_Select::WHERE_GENERATOR])) {
                $value = $selectParts[Kwf_Component_Select::WHERE_GENERATOR];
                if ($g->_class != $componentClass || $value != $g->_settings['generator']) {
                    continue;
                }
            }
            if (isset($selectParts[Kwf_Component_Select::WHERE_FILENAME])) {
                if (!isset($selectParts[Kwf_Component_Select::WHERE_PSEUDO_PAGE])) {
                    $selectParts[Kwf_Component_Select::WHERE_PSEUDO_PAGE] = true;
                }
            }

            $flags = array(
                Kwf_Component_Select::WHERE_PAGE_GENERATOR => 'pageGenerator',
                Kwf_Component_Select::WHERE_PAGE => 'page',
                Kwf_Component_Select::WHERE_PSEUDO_PAGE => 'pseudoPage',
                Kwf_Component_Select::WHERE_BOX => 'box',
                Kwf_Component_Select::WHERE_MULTI_BOX => 'multiBox'
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

            if (isset($selectParts[Kwf_Component_Select::WHERE_GENERATOR_FLAGS])) {
                $flags = $g->getGeneratorFlags();
                foreach ($selectParts[Kwf_Component_Select::WHERE_GENERATOR_FLAGS] as $flag=>$value) {
                    if (!isset($flags[$flag])) $flags[$flag] = null;
                    if ($flags[$flag] != $value) continue 2;
                }
            }

            if (isset($selectParts[Kwf_Component_Select::WHERE_UNIQUE])) {
                $value = $selectParts[Kwf_Component_Select::WHERE_UNIQUE];
                if (isset($g->_settings['unique']) && $g->_settings['unique']) {
                    if (!$value) continue;
                } else {
                    if ($value) continue;
                }
            }

            if (isset($selectParts[Kwf_Component_Select::WHERE_INHERIT])) {
                $value = $selectParts[Kwf_Component_Select::WHERE_INHERIT];
                if (isset($g->_settings['inherit']) && $g->_settings['inherit']) {
                    if (!$value) continue;
                } else {
                    if ($value) continue;
                }
            }
            if (isset($selectParts[Kwf_Component_Select::WHERE_SHOW_IN_MENU])) {
                $value = $selectParts[Kwf_Component_Select::WHERE_SHOW_IN_MENU];
                if (isset($g->_settings['showInMenu']) && $g->_settings['showInMenu']) {
                    if (!$value) continue;
                } else {
                    if ($value) continue;
                }
            }
            if (isset($selectParts[Kwf_Component_Select::WHERE_HAS_EDIT_COMPONENTS]) && $selectParts[Kwf_Component_Select::WHERE_HAS_EDIT_COMPONENTS]) {
                if (!Kwc_Abstract::hasSetting($g->_class, 'editComponents')) {
                    continue;
                }
                $editComponents = Kwc_Abstract::getSetting($g->_class, 'editComponents');
                if ($g->getGeneratorFlag('table')) {
                    //es kann entweder ein table generator angegeben werden
                    //da kommen dann alle unterkomponenten daher
                    if (!in_array($g->_settings['generator'], $editComponents)) {
                        continue;
                    }
                } else {
                    if ($g instanceof Kwf_Component_Generator_Box_StaticSelect) {
                        if (!in_array($g->getGeneratorKey(), $editComponents)) {
                            //oder ein static generator (wenn er nur eine unter komponente hat)
                            continue;
                        }
                    } else {
                        //oder eine komponente eines static generators
                        $continue = true;
                        foreach (array_keys($g->_settings['component']) as $componentKey) {
                            if (in_array($componentKey, $editComponents)) {
                                $continue = false;
                                break;
                            }
                        }
                        if ($continue) continue;
                    }
                    if (isset($g->_settings['unique']) && $g->_settings['unique']) {
                        //vererbte, unique nur bei eigener komponente zurückgeben
                        if ($g->_class != $componentClass) continue;
                    }
                }
            }
            if (isset($selectParts[Kwf_Component_Select::WHERE_COMPONENT_CLASSES])) {
                $componentClasses = $selectParts[Kwf_Component_Select::WHERE_COMPONENT_CLASSES];
                $generatorComponentClasses = $g->getChildComponentClasses();
                $continue = true;
                foreach ($generatorComponentClasses as $cc) {
                    if (in_array($cc, $componentClasses)) {
                        $continue = false;
                        break;
                    }
                }
                if ($continue) { continue; }
            }
            if (isset($selectParts[Kwf_Component_Select::WHERE_HOME]) && $selectParts[Kwf_Component_Select::WHERE_HOME]) {
                if (!$g->getGeneratorFlag('hasHome')) continue;
            }
            if (!$g->getChildComponentClasses($select)) continue;

            $ret[] = $g;
        }

        return $ret;
    }

    public function getChildComponentClasses($select = array())
    {
        $cacheId = 'childComponentClasses-'.$this->_class.'-'.$this->_settings['generator'];
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if (!$success) {
            $ret = $this->_settings['component'];
            foreach ($ret as $c) {
                if (Kwc_Abstract::getFlag($c, 'hasAlternativeComponent')) {
                    $cls = strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c;
                    foreach (call_user_func(array($cls, 'getAlternativeComponents'), $c) as $ac) {
                        $ret[] = $ac;
                    }
                }
            }
            Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        }

        if ($select === array() ||
            ($select instanceof Kwf_Model_Select &&
                !($select->hasPart(Kwf_Component_Select::WHERE_FLAGS) ||
                    $select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_KEY)
                 )
            )
        ) {
             //performance
            return $ret;
        }

        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }

        if ($select->hasPart(Kwf_Component_Select::WHERE_FLAGS)) {
            $flags = $select->getPart(Kwf_Component_Select::WHERE_FLAGS);
            foreach ($ret as $k=>$c) {
                foreach ($flags as $f=>$v) {
                    if (Kwc_Abstract::getFlag($c, $f) != $v) {
                        unset($ret[$k]);
                    }
                }
            }
        }

        if ($select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_KEY)) {
            $componentKey = $select->getPart(Kwf_Component_Select::WHERE_COMPONENT_KEY);
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
        $select = new Kwf_Component_Select($select);
        $select->whereGenerator($this->_settings['generator']);
        return $select;
    }

    /**
     * Used by Kwc_Directories_List_ViewAjax_ViewController for format the select properly
     *
     * @internal
     */
    final public function formatSelect($parentData, $select)
    {
        return $this->_formatSelect($parentData, $select);
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

    public function getAddUrlPart()
    {
        return $this->_addUrlPart;
    }

    abstract public function getChildData($parentData, $select = array());
    abstract public function getChildIds($parentData, $select = array());

    public function countChildData($parentData, $select = array())
    {
        //Wenn nicht effizient genug, fkt überschreiben!
        return count($this->getChildData($parentData, $select));
    }

     //got removed, overwrite _getChildComponentClass or use alternative components instead
    protected final function _getChildComponentClasses() {
        throw new Kwf_Exception("use getChildComponentClasses instead");
    }

    protected function _getChildComponentClass($key, $parentData)
    {
        $c = $this->getChildComponentClasses();
        if ($key) {
            if (!isset($c[$key])) {
                throw new Kwf_Exception("ChildComponent with type '$key' for Component '{$this->_class}' not found; set are ".implode(', ', array_keys($c)));
            }
            $componentClass = $c[$key];
        } else {
            if (count($c) > 1) {
                throw new Kwf_Exception("For multiple components you have to submit a key in generator " . get_class($this) . " ($this->_class).");
            }
            reset($c);
            $componentClass = current($c);
        }

        if (Kwc_Abstract::getFlag($componentClass, "hasAlternativeComponent")) {
            $useAC = call_user_func(array($componentClass, 'useAlternativeComponent'), $componentClass, $parentData, $this);
            if ($useAC) {
                $alternativeComponents = call_user_func(array($componentClass, 'getAlternativeComponents'), $componentClass);
                if (!isset($alternativeComponents[$useAC])) {
                    throw new Kwf_Exception("Alternative component $useAC not set, returned by $componentClass::getAlternativeComponents");
                }
                $componentClass = $alternativeComponents[$useAC];
            }
        }

        return $componentClass;
    }

    protected function _formatSelectFilename(Kwf_Component_Select $select)
    {
        if ($select->hasPart(Kwf_Component_Select::WHERE_FILENAME)) {
            return null;
        }
        if ($select->hasPart(Kwf_Component_Select::WHERE_SHOW_IN_MENU)) {
            return null;
        }
        return $select;
    }

    protected function _formatSelectHome(Kwf_Component_Select $select)
    {
        if ($select->hasPart(Kwf_Component_Select::WHERE_HOME)) {
            return null;
        }
        return $select;
    }

    protected function _formatSelect($parentData, $select)
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        } else {
            $select = clone $select;
        }

        if (is_null($select)) return null;
        $select = $this->_formatSelectFilename($select);
        if (is_null($select)) return null;
        $select = $this->_formatSelectHome($select);
        if (is_null($select)) return null;
        if ($select->hasPart(Kwf_Component_Select::WHERE_FLAGS) || $select->hasPart(Kwf_Component_Select::WHERE_COMPONENT_KEY)) {
            $classes = $this->getChildComponentClasses($select);
            $select->whereComponentClasses($classes);
            if ($select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES) === array()) {
                return null;
            }

        }
        return $select;
    }

    protected function _createData($parentData, $row, $select)
    {
        $id = $this->_getIdFromRow($row);
        if (is_array($select)) $select = new Kwf_Component_Select($select);
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
        $ret = Kwf_Component_Data_Root::getInstance()->getFromDataCache($componentId);
        if (!$ret) {
            $config = $this->_formatConfig($parentData, $row);
            if (!$config['componentClass'] || !is_string($config['componentClass'])) {
                throw new Kwf_Exception("no componentClass set (id $parentData->componentId $id)");
            }
            $config['id'] = $id;
            if (isset($config['inherits'])) {
                throw new Kwf_Exception("You must set Generator::_inherits instead of magically modifying the config");
            }
            if ($this->_inherits) {
                $config['inherits'] = true;
            }
            $config['generator'] = $this; //wird benötigt für duplizieren
            $pageDataClass = $this->_getDataClass($config, $row);
            $ret = new $pageDataClass($config);
            Kwf_Component_Data_Root::getInstance()->addToDataCache($ret, $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE));
        }
        return $ret;
    }

    protected function _getDataClass($config, $row)
    {
        if (Kwc_Abstract::hasSetting($config['componentClass'], 'dataClass')) {
            return Kwc_Abstract::getSetting($config['componentClass'], 'dataClass');
        } else {
            return 'Kwf_Component_Data';
        }
    }

    protected function _getComponentIdFromRow($parentData, $row)
    {
        throw new Kwf_Exception('_getComponentIdFromRow has to be implemented for '.get_class($this));
    }

    protected function _formatConfig($parentData, $row) {
        throw new Kwf_Exception('_formatConfig has to be implemented for '.get_class($this));
    }
    protected function _getIdFromRow($row) {
        throw new Kwf_Exception('_getIdFromRow has to be implemented for '.get_class($this));
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

    public function duplicateChild($source, $parentTarget, Zend_ProgressBar $progressBar = null)
    {
        throw new Kwf_Exception_NotYetImplemented("duplicating is not yet implemented in '".get_class($this)."'");
    }

    public function getDuplicateProgressSteps($source)
    {
        return 0;
    }

    public function makeChildrenVisible($source)
    {
        throw new Kwf_Exception_NotYetImplemented();
    }

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $data = array();

        $data['icon'] = 'bullet_yellow';
        $data['iconEffects'] = array();
        if ($component->getDeviceVisible() == Kwf_Component_Data::DEVICE_VISIBLE_HIDE_ON_MOBILE) {
            $data['iconEffects'][] = 'smartphoneHide';
        } else if ($component->getDeviceVisible() == Kwf_Component_Data::DEVICE_VISIBLE_ONLY_SHOW_ON_MOBILE) {
            $data['iconEffects'][] = 'smartphone';
        }
        $data['allowDrag'] = false;
        $data['allowDrop'] = false;

        if (!$generatorClass) $generatorClass = $this->getClass();
        $data['editControllerComponentId'] = $component->componentId;

        $data['loadChildren'] = false;
        $data['actions'] = array();

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

    public function getPagePropertiesForm()
    {
        return null;
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

    /**
     * If this generator creates a fixed (static) datas, return the ids here.
     *
     * This will be used for performance optimisations.
     */
    public function getStaticChildComponentIds()
    {
        return null;
    }

    public function getSetting($setting)
    {
        return $this->_settings[$setting];
    }

    public function hasSetting($setting)
    {
        return isset($this->_settings[$setting]);
    }

    private function _getPossibleIndirectDbIdShortcutsImpl($class)
    {
        if (isset($this->_getPossibleIndirectDbIdShortcutsCache[$class])) {
            return $this->_getPossibleIndirectDbIdShortcutsCache[$class];
        }
        $ret = array();
        $gens = Kwf_Component_Generator_Abstract::getInstances($class);
        foreach ($gens as $g) {
            // Do not return page generators
            // For Page generators the dbIdShortcut is only used for components below the current
            // page in models
            if (!$g->getGeneratorFlag('page') && $g->hasSetting('dbIdShortcut') &&
                Kwc_Abstract::getIndirectChildComponentClasses($class, array('componentClass' => $this->_class))
            ) {
                $ret[] = $g->getSetting('dbIdShortcut');
            }
        }
        $this->_getPossibleIndirectDbIdShortcutsCache[$class] = $ret;
        foreach (Kwc_Abstract::getChildComponentClasses($class, array('page'=>false)) as $c) {
            $ret = array_merge($ret, $this->_getPossibleIndirectDbIdShortcutsImpl($c));
        }
        $this->_getPossibleIndirectDbIdShortcutsCache[$class] = $ret;
        return $ret;
    }

    /**
     * Helper function that returns the dbIdShortcuts that can be used below a componentClass (only across page)
     *
     * Static, fast and cached.
     */
    protected function _getPossibleIndirectDbIdShortcuts($class)
    {
        $cacheId = '-poss-dbid-sc-'.$this->_class.'-'.$this->getGeneratorKey().'-'.$class;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if (!$success) {
            $ret = $this->_getPossibleIndirectDbIdShortcutsImpl($class);
            $ret = array_unique($ret);
            Kwf_Cache_SimpleStatic::add($cacheId, $ret);
        }
        return $ret;
    }

    /**
     * Returns for every Kwf_Component_Data the device visibility
     *
     * Can be overriten for every Generator if you want a specific device visibility
     */
    public function getDeviceVisible(Kwf_Component_Data $data)
    {
        return Kwf_Component_Data::DEVICE_VISIBLE_ALL;
    }
}
