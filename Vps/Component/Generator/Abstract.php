<?php
abstract class Vps_Component_Generator_Abstract
{
    protected $_db;
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
        $this->_db = Zend_Registry::get('db');
        $this->_init();
        Vps_Benchmark::count('generators');
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
    
    protected function _init()
    {
        if (!isset($this->_table)) {
            if (isset($this->_settings['table'])) {
                if (is_string($this->_settings['table'])) {
                    $this->_table = new $this->_settings['table'];
                } else {
                    $this->_table = $this->_settings['table'];
                }
            } else if ($this->_loadTableFromComponent) {
                $table = Vpc_Abstract::getSetting($this->_class, 'tablename');
                if (!$table) {
                    throw new Vps_Exception("Entweder tablename-setting der Komponente oder _tableName bzw. _table des Generators muss gesetzt sein ($this->_class)");
                }
                $this->_table = new $table(array('componentClass'=>$this->_class));
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
    
    private static function _getGeneratorsForComponent($componentClass, $constraints)
    {
        $generators = Vpc_Abstract::getSetting($componentClass, 'generators');
        $ret = array();
        foreach ($generators as $key => $generator) {
            if (isset($constraints['generator']) && $key != $constraints['generator']) {
                continue;
            }
            if (!isset($generator['class'])) {
                throw new Vps_Exception("Generator class for '$key' ($componentClass) is not set.");
            }
            if (isset($constraints['page'])) {
                if (is_instance_of($generator['class'], 'Vps_Component_Generator_Page_Interface')) {
                    if (!$constraints['page']) continue;
                } else {
                    if ($constraints['page']) continue;
                }
            }
            if (isset($constraints['pseudoPage'])) {
                if (is_instance_of($generator['class'], 'Vps_Component_Generator_PseudoPage_Interface')) {
                    if (!$constraints['pseudoPage']) continue;
                } else {
                    if ($constraints['pseudoPage']) continue;
                }
            }
            if (isset($constraints['box'])) {
                if (is_instance_of($generator['class'], 'Vps_Component_Generator_Box_Interface')) {
                    if (!$constraints['box']) continue;
                } else {
                    if ($constraints['box']) continue;
                }
            }
            $ret[] = self::getInstance($componentClass, $key);
        }
        return $ret;
    }

    public static function getInstances($componentClass, $parentData = null, $constraints = array())
    {
        $ret = self::_getGeneratorsForComponent($componentClass, $constraints);
        foreach (Vpc_Abstract::getSetting($componentClass, 'plugins') as $pluginClass) {
            $ret = array_merge($ret, self::_getGeneratorsForComponent($pluginClass, $constraints));
        }
        
        if ($parentData && $parentData->isPage) {
            
            if (!$parentData instanceof Vps_Component_Data_Root) {
                foreach (Vps_Registry::get('config')->vpc->masterComponents->toArray() as $mc) {
                    $ret = array_merge($ret, self::_getGeneratorsForComponent($mc, $constraints));
                }
            }
            
            if (!isset($constraints['generator']) &&
                (!isset($constraints['page']) || !$constraints['page']) &&
                (!isset($constraints['skipBox']) || !$constraints['skipBox']))
            {
                $boxConstraints = array(
                    'box' => true,
                    'skipBox' => true,
                    'inherit' => true
                );
                $page = $parentData;
                while ($page) { // Aktuelle inkl. aller Überseiten durchlaufen
                    if ($page->componentId == $parentData->componentId) {
                        $generators = $parentData->getGenerators($boxConstraints);
                    } else {
                        $generators = $page->getRecursiveGenerators($boxConstraints);
                    }
                    $ret = array_merge($ret, $generators);                    
                    $parent = $page->getParentPage();
                    if (!$parent) { $parent = $page->parent; }
                    $page = $parent;
                }
            }
            
            if ((!isset($constraints['skipRoot']) || !$constraints['skipRoot'])
                && ($parentData instanceof Vps_Component_Data_Root || is_numeric($parentData->componentId))
            ) {
                $ret = array_merge($ret, self::_getGeneratorsForComponent(
                    Vps_Registry::get('config')->vpc->rootComponent, array_merge(array('generator' => 'page'), $constraints)
                ));
            }
            
        }
        return $ret;
    }
    
    public function getChildData($parentData, $constraints)
    {
        return array();
    }

    public function getChildIds($parentData, $constraints)
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

    protected function _formatConstraints($parentData, $constraints)
    {
        if (isset($constraints['hasEditComponents'])) {
            unset($constraints['hasEditComponents']);
            if (isset($constraints['componentClass'])) {
                throw new Vps_Exception("Can't use constraint hasEditComponents together with componentClass (not implemented)");
            }
            $constraints['componentClass'] = array();
            if (!Vpc_Abstract::hasSetting($this->_class, 'editComponents')
                    || !Vpc_Abstract::getSetting($this->_class, 'editComponents'))
            {
                return null;
            }
            $editComponents = Vpc_Abstract::getSetting($this->_class, 'editComponents');
            if (!is_array($editComponents)) $editComponents = array($editComponents);

            $constraints['componentClass'][] = array();
            foreach ($editComponents as $c) {
                if (isset($this->_settings['component'][$c])) {
                    $constraints['componentClass'][] = $this->_settings['component'][$c];
                }
            }
            
            if (!$constraints['componentClass']) return null;
        }
        return $constraints;
    }

    protected function _createData($parentData, $row, $constraints)
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
