<?php
abstract class Vps_Component_Generator_Abstract
{
    protected $_class;
    protected $_settings;
    protected $_pluginBaseComponentClass = false;

    protected $_loadTableFromComponent = false;

    private $_dataCache = array();
    protected $_idSeparator;
    protected $_inherits = false;
    private $_model;

    private static $instances = array();
    private static $_cachedInstances = array();

    public function __sleep()
    {
        $ret = array();
        foreach (array_keys(get_object_vars($this)) as $i) {
            if ($i != '_model') {
                $ret[] = $i;
            }
        }
        return $ret;
    }

    public function __wakeup()
    {
        Vps_Benchmark::count('generators wokeup', $this->_class.'-'.$this->_settings['generator']);
    }

    protected function __construct($class, $settings)
    {
        $this->_class = $class;
        $this->_settings = $settings;
        $this->_init();
        Vps_Benchmark::count('generators', $this->_class.'-'.$settings['generator']);
    }

    protected function _init()
    {
        if (!is_array($this->_settings['component'])) {
            $this->_settings['component'] = array($this->_settings['generator']
                                            => $this->_settings['component']);
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
                    throw new Vps_Exception("Can't create model");
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
        self::$_cachedInstances = array();
    }

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
        if ($component instanceof Vps_Component_Data) {
            $componentClass = $component->componentClass;
        } else {
            $componentClass = $component;
            $component = null;
        }
        $cacheId = 'genInst'.$componentClass;
        if ($component) {
            $ic = $component->inheritClasses;
            foreach ($ic as $inheritComponent) {
                $cacheId .= '__' . $inheritComponent;
            }
        }
        static $cache = null;
        if (!$cache) {
            $cache = Vps_Cache::factory('Core', 'Memcached', array(
                'lifetime'=>null,
                'automatic_cleaning_factor' => false,
                'automatic_serialization'=>true));
        }
        if (isset(self::$_cachedInstances[$cacheId])) {
            Vps_Benchmark::countBt('Generator::getInst hit');
            $generators = self::$_cachedInstances[$cacheId];
        } else if (($cachedGeneratorData = $cache->load($cacheId)) !== false) {
            Vps_Benchmark::count('Generator::getInst semi-hit');
            $generators = array();
            foreach ($cachedGeneratorData as $g) {
                $generators[] = self::getInstance($g['componentClass'], $g['key'], array(), $g['pluginBaseComponentClass']);
            }
        } else {
            Vps_Benchmark::count('Generator::getInst miss', $cacheId);

            $generators = self::_getGeneratorsForComponent($componentClass, false);
            foreach (Vpc_Abstract::getSetting($componentClass, 'plugins') as $pluginClass) {
                $generators = array_merge($generators, self::_getGeneratorsForComponent($pluginClass, $componentClass));
            }
            if (is_object($component) && $component->inheritClasses) {
                $ic = $component->inheritClasses;
                foreach ($ic as $inheritClass) {
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
                        $inheritedGenerator = self::getInstance($inheritClass, $key, $inheritedGenerator);
                        if (!$inheritedGenerator instanceof Vps_Component_Generator_Box_Interface) {
                            $generators[] = $inheritedGenerator;
                            continue;
                        }
                        $inheritedBoxes = $inheritedGenerator->getBoxes();
//                             if (count($inheritedBoxes) != 1) {
//                                 throw new Vps_Exception("There should be only one box in an inherited generator");
//                             }
                        $inheritedBox = $inheritedBoxes[0];
                        foreach ($generators as $k=>$g) {
                            if (!$g instanceof Vps_Component_Generator_Box_Interface) continue;
                            foreach ($inheritedBoxes as $inheritedBox) {
                                if (!in_array($inheritedBox, $g->getBoxes())) continue;
                                if ($g->getPriority() >= $inheritedGenerator->getPriority()) {
                                    continue 3;
                                } else {
                                    if (count($g->getBoxes()) > 1) {
                                        throw new Vps_Exception("There should be only one box in an inherited generator");
                                    }
                                    unset($generators[$k]);
                                    break;
                                }
                            }
                        }
                        $generators[] = $inheritedGenerator;
                    }
                }
/*
                    foreach ($generators as $k=>$g) {
                        if ($g instanceof Vps_Component_Generator_Box_Interface && !$g->getBoxes()) {
                            unset($generators[$k]);
                        }
                    }
*/
            }
            $cachedGeneratorData = array();
            foreach ($generators as $g) {
                $cachedGeneratorData[] = array('componentClass' => $g->_class,
                                               'key' => $g->_settings['generator'],
                                               'pluginBaseComponentClass' => $g->_pluginBaseComponentClass);
            }
            $cache->save($cachedGeneratorData, $cacheId);
        }
        self::$_cachedInstances[$cacheId] = $generators;

        $selectParts = $select->getParts();

        $ret = array();
        foreach ($generators as $g) {
            if ($component && $g instanceof Vps_Component_Generator_Page &&
                !(
                    is_numeric($component->componentId) ||
                    $component instanceof Vps_Component_Data_Root ||
                    is_instance_of($component->componentClass, 'Vpc_Root_Category_Component')
                )
            ) {
                continue;
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


            $interfaces = array(
                Vps_Component_Select::WHERE_PAGE_GENERATOR => 'Vps_Component_Generator_Page',
                Vps_Component_Select::WHERE_PAGE => 'Vps_Component_Generator_Page_Interface',
                Vps_Component_Select::WHERE_PSEUDO_PAGE => 'Vps_Component_Generator_PseudoPage_Interface',
                Vps_Component_Select::WHERE_BOX => 'Vps_Component_Generator_Box_Interface',
                Vps_Component_Select::WHERE_MULTI_BOX => 'Vps_Component_Generator_MultiBox_Interface'
            );

            foreach ($interfaces as $part=>$interface) {
                if (isset($selectParts[$part])) {
                    $value = $selectParts[$part];
                    if ($g instanceof $interface) {
                        if (!$value) continue 2;
                    } else {
                        if ($value) continue 2;
                    }
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
                if (is_array($g->_settings['component'])) {
                    $continue = true;
                    foreach (array_keys($g->_settings['component']) as $componentKey) {
                        if (in_array($componentKey, $editComponents)) {
                            $continue = false;
                            break;
                        }
                    }
                    if ($continue) continue;
                } else if (!in_array($key, $editComponents)) {
                    continue;
                }
                if (isset($g->_settings['unique']) && $g->_settings['unique']) {
                    //vererbte, unique nur bei eigener komponente zurückgeben
                    if ($g->_class != $componentClass) continue;
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
                if (!$g instanceof Vps_Component_Generator_Page) continue;
            }
            if (!$g->getChildComponentClasses($select)) continue;

            $ret[] = $g;
        }

        return $ret;
    }

    public function getChildComponentClasses($select = array())
    {
        return self::getStaticChildComponentClasses($this->_settings, $select);
    }

    public static function getStaticChildComponentClasses($data, $select = array())
    {
        if ($select === array()) {
            return $data['component']; //performance
        }
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }

        $ret = $data['component'];

        if (!is_array($ret)) $ret = array($ret);
        foreach ($ret as $key => $r) {
            if (!$r) {
                unset($ret[$key]);
            }
        }

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


    protected function _getChildComponentClass($key)
    {
        $c = $this->_settings['component'];
        if (!isset($c[$key])) {
            throw new Vps_Exception("ChildComponent with type '$key' for Component '{$this->_class}' not found.");
        }
        return $c[$key];
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

        if (!isset($this->_dataCache[$parentData->componentId][$id])) {
            $config = $this->_formatConfig($parentData, $row);
            if (!$config['componentClass']) {
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
            $this->_dataCache[$parentData->componentId][$id] = new $pageDataClass($config);
        }
        return $this->_dataCache[$parentData->componentId][$id];
    }

    protected function _getDataClass($config, $row)
    {
        if (Vpc_Abstract::hasSetting($config['componentClass'], 'dataClass')) {
            return Vpc_Abstract::getSetting($config['componentClass'], 'dataClass');
        } else {
            return 'Vps_Component_Data';
        }
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

    public function getInherits()
    {
        return $this->_inherits;
    }

    public function duplicateChild($source, $parentTarget)
    {
        throw new Vps_Exception_NotYetImplemented();
    }
}
