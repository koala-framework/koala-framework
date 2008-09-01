<?php
abstract class Vps_Component_Generator_Abstract
{
    protected $_class;
    protected $_settings;

    protected $_loadTableFromComponent = false;

    private $_dataCache = array();
    protected $_idSeparator;
    private $_model;
    
    protected function __construct($class, $settings)
    {
        $this->_class = $class;
        $this->_settings = $settings;
        $this->_init();
        Vps_Benchmark::count('generators');
    }
    
    protected function _init()
    {
    }
    
    protected function _getModel()
    {
        if (!$this->_model) {
            if (isset($this->_settings['model'])) {
                if (is_string($this->_settings['model'])) {
                    $this->_model = new $this->_settings['model']();
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
                } else if ($this->_loadTableFromComponent) {
                    $tableName = Vpc_Abstract::getSetting($this->_class, 'tablename');
                    if (!$tableName) {
                        throw new Vps_Exception("Entweder tablename-setting der Komponente oder _tableName bzw. _table des Generators muss gesetzt sein ($this->_class)");
                    }
                    $table = new $tableName(array('componentClass'=>$this->_class));
                }
                if (isset($table)) {
                    $this->_model = new Vps_Model_Db(array('table' => $table));
                }
            }
        }
        return $this->_model;
    }
    
    public static function getInstance($componentClass, $key, $settings = array())
    {
        static $instances = array();
        $instanceKey = $componentClass . '_' . $key;
        if (!isset($instances[$instanceKey])) {
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
            if (!is_array($settings['component'])) {
                $settings['component'] = array($key => $settings['component']);
            }
            $settings['generator'] = $key;
            $instances[$instanceKey] = new $settings['class']($componentClass, $settings);
        }
        return $instances[$instanceKey];
    }
    
    private static function _getGeneratorsForComponent($componentClass, $select)
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        //p($componentClass);
        //p($select);
        //bt();
        $generators = Vpc_Abstract::getSetting($componentClass, 'generators');
        $ret = array();
        foreach ($generators as $key => $generator) {
            if ($value = $select->getPart(Vps_Component_Select::WHERE_GENERATOR)) {
                if ($value != $key) {
                    continue;
                }
            }
            if (!isset($generator['class'])) {
                throw new Vps_Exception("Generator class for '$key' ($componentClass) is not set.");
            }
            if ($select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
                if (!$select->hasPart(Vps_Component_Select::WHERE_PSEUDO_PAGE)) {
                    $select->wherePseudoPage();
                }
            }
            if ($select->hasPart(Vps_Component_Select::IGNORE_VISIBLE)) {
                if (!$generator instanceof Vps_Component_Generator_Table) {
                    continue;
                }
            }
            $interfaces = array(
                Vps_Component_Select::WHERE_PAGE => 'Vps_Component_Generator_Page_Interface',
                Vps_Component_Select::WHERE_PSEUDO_PAGE => 'Vps_Component_Generator_PseudoPage_Interface',
                Vps_Component_Select::WHERE_BOX => 'Vps_Component_Generator_Box_Interface',
                Vps_Component_Select::WHERE_MULTI_BOX => 'Vps_Component_Generator_MultiBox_Interface'
            );
            foreach ($interfaces as $part=>$interface) {
                if ($select->hasPart($part)) {
                    $value = $select->getPart($part);
                    if (in_array($interface, class_implements($generator['class']))) {
                        if (!$value) continue 2;
                    } else {
                        if ($value) continue 2;
                    }
                }
            }
            if ($select->hasPart(Vps_Component_Select::WHERE_UNIQUE)) {
                $value = $select->getPart(Vps_Component_Select::WHERE_UNIQUE);
                if (isset($generator['unique']) && $generator['unique']) {
                    if (!$value) continue;
                } else {
                    if ($value) continue;
                }
            }
            if ($select->hasPart(Vps_Component_Select::WHERE_INHERIT)) {
                $value = $select->getPart(Vps_Component_Select::WHERE_INHERIT);
                if (isset($generator['inherit']) && $generator['inherit']) {
                    if (!$value) continue;
                } else {
                    if ($value) continue;
                }
            }
            if ($select->hasPart(Vps_Component_Select::WHERE_SHOW_IN_MENU)) {
                $value = $select->getPart(Vps_Component_Select::WHERE_SHOW_IN_MENU);
                if (isset($generator['showInMenu']) && $generator['showInMenu']) {
                    if (!$value) continue;
                } else {
                    if ($value) continue;
                }
            }
            if ($select->getPart(Vps_Component_Select::WHERE_HAS_EDIT_COMPONENTS)) {
                if (!Vpc_Abstract::hasSetting($componentClass, 'editComponents')) {
                    continue;
                }
                $editComponents = Vpc_Abstract::getSetting($componentClass, 'editComponents');
                if (!in_array($key, $editComponents)) {
                    continue;
                }
            }
            if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
                $componentClasses = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
                $generatorComponentClasses = $generator['component'];
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
            if ($select->hasPart(Vps_Component_Select::WHERE_HOME)) {
                if ($generator['class'] != 'Vps_Component_Generator_Page' &&
                    !is_subclass_of($generator['class'], 'Vps_Component_Generator_Page')
                ) continue;
            }
            $ret[] = self::getInstance($componentClass, $key, $generator);
        }
        return $ret;
    }

    public static function getInstances($component, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }

        if (is_object($component)) {
            $cacheId = $component->componentId;
            Vps_Benchmark::count('Gen::getInstances dynamisch', $cacheId.$select->toDebug());
        } else {
            $cacheId = $component."<br />";
            $type = 'unknown';
            foreach (debug_backtrace() as $bt) {
                if ($bt['function']=='getIndirectChildComponentClasses') {
                    $type = 'getIndirectChildComponentClasses';
                }
            }
            if ($type == 'unknown') {
                foreach (debug_backtrace() as $bt) {
                    if (isset($bt['class'])) $cacheId .= $bt['class'];
                    $cacheId .= '::'.$bt['function']."<br />";
                }
            } else {
                $cacheId .= "<b>$type</b><br />";
            }
            Vps_Benchmark::count('Gen::getInstances statisch', $cacheId.$select->toDebug());
        }

        if ($component instanceof Vps_Component_Data) {
            $componentClass = $component->componentClass;
        } else {
            $componentClass = $component;
        }
        $ret = self::getStaticInstances($componentClass, $select);
        if (is_object($component)) {
            $ret = array_merge($ret, self::getDynamicInstances($component, $select));
        }
        return $ret;
    }

    public static function getStaticInstances($componentClass, $select = array())
    {
        $ret = self::_getGeneratorsForComponent($componentClass, $select);
        foreach (Vpc_Abstract::getSetting($componentClass, 'plugins') as $pluginClass) {
            $ret = array_merge($ret, self::_getGeneratorsForComponent($pluginClass, $select));
        }
        return $ret;
    }

    public static function getDynamicInstances($component, $select = array())
    {
        $ret = array();
        if (!$component->isPage) return array();
        
        static $instances = array();
        $cacheId = $component->componentId . serialize($select->getParts());
        if (isset($instances[$cacheId])) {
$b = Vps_Benchmark::start('getDynamicInstancesCached', $component->componentId.'<br/>'.$select->toDebug());
            return $instances[$cacheId];
if($b)$b->stop();
        }

$b = Vps_Benchmark::start('getDynamicInstances', $component->componentId.'<br/>'.$select->toDebug());
        Vps_Benchmark::count('Gen::getInstances dynamisch+page');

        if (!$select->getPart(Vps_Component_Select::WHERE_GENERATOR) &&
            !$select->getPart(Vps_Component_Select::WHERE_PAGE))
        {
            $inheritSelect = clone $select;
            $inheritSelect->unsetPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
            if ($component->parent instanceof Vps_Component_Data_Root) {
                $parent = $component->parent;
            } else {
                $parent = $component->getParentPage();
            }
            if ($parent) {
                $inheritGenerators = $parent->getRecursiveGenerators(array('inherit' => true));
                foreach ($inheritGenerators as $ig) {
                    if ($ig->getChildComponentClasses($inheritSelect)) {
                        $ret[] = $ig;
                    }
                }
            }
        }

        if (!$select->getPart(Vps_Component_Select::SKIP_ROOT)
            && ($component instanceof Vps_Component_Data_Root || is_numeric($component->componentId))
        ) {
            $rootSelect = clone $select;
            $rootSelect->whereGenerator('page');
            $ret = array_merge($ret, self::_getGeneratorsForComponent(
                Vps_Component_Data_Root::getComponentClass(), $rootSelect
            ));
        }
        $instances[$cacheId] = $ret;
if($b)$b->stop();

        return $ret;
    }

    public function getChildComponentClasses($select = array())
    {
        return self::getStaticChildComponentClasses($this->_settings, $select);
    }
    
    public static function getStaticChildComponentClasses($data, $select = array())
    {
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

        $ret = array_unique(array_values($ret));
        
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
    
    public function getChildData($parentData, $select = array())
    {
        return array();
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
        }
        $select = $this->_formatSelectFilename($select);
        if (is_null($select)) return null;
        $select = $this->_formatSelectHome($select);
        if (is_null($select)) return null;

        if ($select->hasPart(Vps_Component_Select::WHERE_FLAGS) || $select->hasPart(Vps_Component_Select::WHERE_COMPONENT_KEY)) {
            $classes = Vpc_Abstract::getChildComponentClasses($parentData->componentClass, $select);
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
        if (!isset($this->_dataCache[$parentData->componentId][$id])) {
            $config = $this->_formatConfig($parentData, $row);
            $config['id'] = $id;
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
        return print_r($this->_settings, true);
    }
}
