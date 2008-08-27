<?php
abstract class Vps_Component_Generator_Abstract
{
    protected $_class;
    protected $_settings;

    protected $_loadTableFromComponent = false;
    protected $_table;

    private $_dataCache = array();
    protected $_idSeparator;
    
    protected function __construct($class, $settings)
    {
        $this->_class = $class;
        $this->_settings = $settings;
        $this->_init();
        Vps_Benchmark::count('generators');
    }
    
    protected function _init()
    {
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
                $this->_model = new Vps_Db_Model(array('table' => $table));
            }
        }
    }
    
    public static function getInstance($componentClass, $key)
    {
        static $instances = array();
        $instanceKey = $componentClass . '_' . $key;
        if (!isset($instances[$instanceKey])) {
            $settings = Vpc_Abstract::getSetting($componentClass, 'generators');
            if (!isset($settings[$key])) {
                throw new Vps_Exception("Generator with key '$key' for '$componentClass' not found.");
            }
            if (!isset($settings[$key]['class'])) {
                throw new Vps_Exception("No Generator-Class set: key '$key' for '$componentClass'");
            }
            if (!class_exists($settings[$key]['class'])) {
                throw new Vps_Exception("Generator-Class '{$settings[$key]['class']}' does not exist (used in '$componentClass')");
            }
            if (!is_subclass_of($settings[$key]['class'], 'Vps_Component_Generator_Abstract')) {
                throw new Vps_Exception("Generator-Class '{$settings[$key]['class']}' is not an Vps_Component_Generator_Abstract");
            }
            if (!is_array($settings[$key]['component'])) {
                $settings[$key]['component'] = array($key => $settings[$key]['component']);
            }
            $settings[$key]['generator'] = $key;
            $instances[$instanceKey] = new $settings[$key]['class']($componentClass, $settings[$key]);
        }
        return $instances[$instanceKey];
    }
    
    private static function _getGeneratorsForComponent($componentClass, $select)
    {
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
            $interfaces = array(
                Vps_Component_Select::WHERE_PAGE => 'Vps_Component_Generator_Page_Interface',
                Vps_Component_Select::WHERE_PSEUDO_PAGE => 'Vps_Component_Generator_PseudoPage_Interface',
                Vps_Component_Select::WHERE_BOX => 'Vps_Component_Generator_Box_Interface',
                Vps_Component_Select::WHERE_MULTI_BOX => 'Vps_Component_Generator_MultiBox_Interface'
            );
            foreach ($interfaces as $part=>$interface) {
                if ($select->hasPart($part)) {
                    $value = $select->getPart($part);
                    if (is_instance_of($generator['class'], $interface)) {
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
            $ret[] = self::getInstance($componentClass, $key);
        }
        $select->processed(Vps_Component_Select::WHERE_GENERATOR);
        $select->processed(Vps_Component_Select::WHERE_PAGE);
        $select->processed(Vps_Component_Select::WHERE_PSEUDO_PAGE);
        $select->processed(Vps_Component_Select::WHERE_BOX);
        $select->processed(Vps_Component_Select::WHERE_MULTI_BOX);
        $select->processed(Vps_Component_Select::WHERE_UNIQUE);
        $select->processed(Vps_Component_Select::WHERE_INHERIT);
        $select->processed(Vps_Component_Select::WHERE_SHOW_IN_MENU);
        $select->processed(Vps_Component_Select::WHERE_HAS_EDIT_COMPONENTS);

        return $ret;
    }

    /**
     * Gibt alle Generators für eine Komponente zurück.
     * ist abhängig davon ob es eine Page ist (daher $parentData)
     * und man kann auch constraints übergeben (zB um nur page generators zu bekommen)
     **/
    public static function getInstances($component, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }

        if ($select->getCheckProcessed()) {
            $select->resetProcessed();
        }

        if ($component instanceof Vps_Component_Data) {
            $componentClass = $component->componentClass;
        } else {
            $componentClass = $component;
            $component = null;
        }
        $ret = self::_getGeneratorsForComponent($componentClass, $select);
        
        if ($component && $component->isPage) {
            
            if (!$select->getPart(Vps_Component_Select::WHERE_GENERATOR) &&
                !$select->getPart(Vps_Component_Select::WHERE_PAGE) &&
                !$select->getPart(Vps_Component_Select::SKIP_INHERIT))
            {
                $inheritSelect = clone $select;
                $inheritSelect->skipInherit();
                $inheritSelect->whereInherit();
                $page = $component;
                while ($page) { // Aktuelle inkl. aller Überseiten durchlaufen
                    if ($page->componentId == $component->componentId) {
                        $generators = $component->getGenerators($inheritSelect);
                    } else {
                        $generators = $page->getRecursiveGenerators($inheritSelect);
                    }
                    $ret = array_merge($ret, $generators);
                    $parent = $page->getParentPage();
                    if (!$parent) { $parent = $page->parent; }
                    $page = $parent;
                }
            }
            $select->processed(Vps_Component_Select::SKIP_INHERIT);

            if (!$select->skipRoot()
                && ($component instanceof Vps_Component_Data_Root || is_numeric($component->componentId))
            ) {
                $rootSelect = clone $select;
                $rootSelect->wherePage();
                $ret = array_merge($ret, self::_getGeneratorsForComponent(
                    Vps_Registry::get('config')->vpc->rootComponent, $rootSelect
                ));
            }
            $select->processed(Vps_Component_Select::SKIP_ROOT);
        }
        
        foreach (Vpc_Abstract::getSetting($componentClass, 'plugins') as $pluginClass) {
            $generators = self::_getGeneratorsForComponent($pluginClass, $select);
            $ret = array_merge($ret, $generators);
        }
        $select->checkAndResetProcessed();
        return $ret;
    }
    
    public function getChildComponentClasses($select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }

        $select->processed(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
        $select->processed(Vps_Component_Select::WHERE_FLAGS);
        $select->processed(Vps_Component_Select::WHERE_COMPONENT_KEY);

        $ret = $this->_settings['component'];
        if (!is_array($ret)) $ret = array($ret);

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

        if ($select->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $componentKey = $select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
            foreach ($ret as $k => $r) {
                if (in_array($r, $constraints['componentClasses'])) {
                    unset($ret[$k]);
                }
            }
        }

        $select->checkAndResetProcessed();
        return array_unique(array_values($ret));
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
    
    public function getChildData($parentData, $select)
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
            $select->processed(Vps_Component_Select::WHERE_FILENAME);
            return null;
        }
        if ($select->hasPart(Vps_Component_Select::WHERE_SHOW_IN_MENU)) {
            $select->processed(Vps_Component_Select::WHERE_SHOW_IN_MENU);
            return null;
        }
        return $select;
    }

    protected function _formatSelect($parentData, $select)
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $select->processed(Vps_Component_Select::WHERE_FLAGS);
        $select->processed(Vps_Component_Select::WHERE_COMPONENT_KEY);

        $select = $this->_formatSelectFilename($select);
        if (is_null($select)) return null;

        if ($select->hasPart(Vps_Component_Select::WHERE_FLAGS) || $select->hasPart(Vps_Component_Select::WHERE_COMPONENT_KEY)) {
            $classesSelect = clone $select;
            $classesSelect->setCheckProcessed(false);
            $classes = Vpc_Abstract::getChildComponentClasses($parentData->componentClass, $classesSelect);
            $select->whereComponentClasses($classes);
            if ($select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES) === array()) {
                $select->processed(Vps_Component_Select::WHERE_COMPONENT_CLASSES);
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
}
