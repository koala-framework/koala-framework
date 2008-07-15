<?php
abstract class Vps_Component_Generator_Abstract
{
    protected $_db;
    protected $_class;
    protected $_settings;

    protected $_loadTableFromComponent = false;
    protected $_table;

    private $_dataCache = array();

    protected function __construct($class, $settings)
    {
        $this->_class = $class;
        $this->_settings = $settings;
        $this->_db = Zend_Registry::get('db');
        $this->_init();
        Vps_Benchmark::count('treeCaches');
    }

    protected function _init()
    {
        if (!isset($this->_table)) {
            if (isset($this->_settings['table'])) {
                $this->_table = new $this->_settings['table'];
            } else if ($this->_loadTableFromComponent) {
                $table = Vpc_Abstract::getSetting($this->_class, 'tablename');
                if (!$table) {
                    throw new Vps_Exception("Entweder tablename-setting der Komponente oder _tableName bzw. _table des TreeCaches muss geseftzt sein ($this->_class)");
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
            if (isset($settings[$key]) && 
                is_subclass_of($settings[$key]['class'], 'Vps_Component_Generator_Abstract'))
            {
                if (!is_array($settings[$key]['component'])) {
                    $settings[$key]['component'] = array($key => $settings[$key]['component']);
                }
                $instances[$instanceKey] = new $settings[$key]['class']($componentClass, $settings[$key]);
            } else {
                throw new Vps_Exception("Generator with key '$key' for '$componentClass' not found.");
            }
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
            if ($parentData instanceof Vps_Component_Data_Root || is_numeric($parentData->componentId)) {
                $ret = array_merge($ret, self::_getGeneratorsForComponent(
                    Vps_Registry::get('config')->vpc->rootComponent, $constraints
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
        if (isset($constraints['select']) && ($constraints['select'] instanceof Vps_Db_Table_Select_TreeCache)) {
            $constraints['treecache'] = $constraints['select']->getTreeCacheClass();
        }
        if (isset($constraints['treecache']) &&
            !$this instanceof $constraints['treecache']
        ){
            return null;
        }
        return $constraints;
    }
    
    public function getDbIdShortcut($dbId)
    {
        foreach ($this->_getAdditionalTreeCaches(null) as $treeCache) {
            $ret = $treeCache->getDbIdShortcut($dbId);
            if ($ret) return $ret;
        }
        return null;
    }

    protected function _createData($parentData, $row)
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

    public function createsPages()
    {
        foreach ($this->_getAdditionalTreeCaches(null) as $treeCache) {
            if ($treeCache->createsPages()) return true;
        }
        return false;
    }
    public function createsBoxes()
    {
        foreach ($this->_getAdditionalTreeCaches(null) as $treeCache) {
            if ($treeCache->createsBoxes()) return true;
        }
        return false;
    }
}
