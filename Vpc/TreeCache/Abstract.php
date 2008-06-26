<?php
abstract class Vpc_TreeCache_Abstract
{
    protected $_db;
    protected $_class;

    protected $_loadTableFromComponent = false;
    protected $_table;
    protected $_tableName;

    protected $_additionalTreeCaches = array();
    private $_isTop = true;

    private $_dataCache = array();

    protected function __construct($class)
    {
        $this->_class = $class;
        $this->_db = Zend_Registry::get('db');
        $this->_init();
        $GLOBALS['treeCacheCounter']++;
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

    public function getChildIds($parentData, $constraints)
    {
        $ret = array();
        foreach ($this->_getAdditionalTreeCaches($parentData) as $treeCache) {
            $ret = array_merge($ret, $treeCache->getChildIds($parentData, $constraints));
        }
        return $ret;
    }

    protected function _getAdditionalTreeCaches($parentData)
    {
        $ret = $this->_additionalTreeCaches;
        if ($this->_isTop && $parentData && $parentData->isPage) {
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
                $tc = Vpc_TreeCache_Abstract::getInstance(Vps_Registry::get('config')->vpc->rootComponent);
                //TODO wird da eh nicht ein bestehender tc geändert der woanders ohne isTop gebraucht wird?
                $tc->_isTop = false;
                $ret[] = $tc;
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
