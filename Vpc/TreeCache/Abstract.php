<?php
abstract class Vpc_TreeCache_Abstract
{
    protected $_db;
    protected $_class;
    protected $_cache;

    protected $_loadTableFromComponent = false;
    protected $_table;
    protected $_tableName;
    
    protected $_pageDataClass = 'Vps_Component_Data';
    
    protected $_additionalTreeCaches = array();
    private $_pagesTreeCache;
    private $_isTop = true;
    
    protected function __construct($class)
    {
        $this->_class = $class;
        $this->_db = Zend_Registry::get('db');
        $this->_init();
    }

    protected function _init()
    {
        if (!isset($this->_table)) {
            if (isset($this->_tableName)) {
                $this->_table = new $this->_tableName;
            } else if ($this->_loadTableFromComponent) {
                $table = Vpc_Abstract::getSetting($this->_class, 'tablename');
                if (!$table) {
                    throw new Vps_Exception("Entweder tablename-setting der Komponente oder _tableName bzw. _table des TreeCaches muss geseftzt sein ($this->_class)");
                }
                $this->_table = new $table(array('componentClass'=>$this->_class));
            }
        }
        foreach ($this->_additionalTreeCaches as $key => $treeCache) {
            $this->_additionalTreeCaches[$key] = new $treeCache($this->_class);
        }
    }
    
    public function addTreeCache($treeCache)
    {
        $this->_additionalTreeCaches[] = $treeCache;
    }

    public static function getInstance($componentClass, $isTop = true)
    {
        static $instances = array();
        if (!isset($instances[$componentClass])) {
            $c = Vpc_Admin::getComponentFile($componentClass, 'TreeCache', 'php', true);
            if ($c) {
                $instances[$componentClass] = new $c($componentClass);
            } else {
                $instances[$componentClass] = null;
            }
        }
        return $instances[$componentClass];
    }
    
    public function getTreeCache($class)
    {
        if ($this instanceof $class) {
            return $this;
        } else {
            foreach ($this->_additionalTreeCaches as $treeCache) {
                $tc = $treeCache->getTreeCache($class);
                if ($tc) return $tc;
            }
        }
        return null;
    }

    public function getChildData($parentData, $constraints)
    {
        $ret = array();
        
        foreach ($this->_getAdditionalTreeCaches($parentData) as $treeCache) {
            $ret = array_merge($ret, $treeCache->getChildData($parentData, $constraints));
        }
        return $ret;
    }

    protected function _getAdditionalTreeCaches($parentData)
    {
        $ret = $this->_additionalTreeCaches;
        if ($this->_isTop && $parentData instanceof Vps_Component_Data_Page) {
            if (!$parentData instanceof Vps_Component_Data_Root) {
                foreach (Vps_Registry::get('config')->vpc->masterComponents->toArray() as $mc) {
                    $tc = Vpc_TreeCache_Abstract::getInstance($mc);
                    if ($tc) {
                        $tc->_isTop = false;
                        $ret[] = $tc;
                    }
                }
            }
            if ($parentData instanceof Vps_Component_Data_Root || is_numeric($parentData->componentId)) {
                if (!isset($this->_pagesTreeCache)) {
                    $this->_pagesTreeCache = Vpc_TreeCache_Abstract::getInstance(Vps_Registry::get('config')->vpc->rootComponent);
                    $this->_pagesTreeCache->_isTop = false;
                }
                $ret[] = $this->_pagesTreeCache;
            }
        }
        return $ret;
    }

    protected function _getSetting($name)
    {
        return Vpc_Abstract::getSetting($this->_class, $name);
    }

    protected function _getChildComponentClass($key)
    {
        $c = $this->_getSetting('childComponentClasses');
        if (!isset($c[$key])) {
            throw new Vps_Exception("ChildComponent with type '$key' for Component '{$this->_class}' not found.");
        }
        return $c[$key];
    }
    
    protected function _formatConstraints($parentData, $constraints)
    {
        if (isset($constraints['treecache']) && 
            !$this instanceof $constraints['treecache']
        ){
            return null;
        }
        return $constraints;
    }
    
    public function hasDbIdShortcut($dbId)
    {
        return false;
    }
}
