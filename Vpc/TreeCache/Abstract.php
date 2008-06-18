<?php
abstract class Vpc_TreeCache_Abstract
{
    protected $_db;
    protected $_class;
    protected $_cache;

    protected $_loadTableFromComponent = false;
    protected $_table;
    protected $_tableName;
    
    protected $_additionalTreeCaches = array();
    
    protected function __construct($class, Zend_Db_Adapter_Pdo_Mysql $db, $additionalTreeCaches = array())
    {
        $this->_class = $class;
        $this->_db = $db;
        $this->_cache = self::getTreeCacheTable();
        $this->_additionalTreeCaches += $additionalTreeCaches;
        $this->_init();
    }

    public static function getTreeCacheTable()
    {
        static $c = null;
        if (!$c) $c = new Vps_Dao_TreeCache(); //wg. performance
        return $c;
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
            $this->_additionalTreeCaches[$key] = new $treeCache($this->_class, $this->_db);
        }
    }

    public static function getInstance($componentClass)
    {
        static $instances = array();
        if (!isset($instances[$componentClass])) {
            $c = Vpc_Admin::getComponentFile($componentClass, 'TreeCache', 'php', true);
            if ($c) {
                $instances[$componentClass] = new $c($componentClass, Zend_Registry::get('db'), array('Vpc_TreeCache_Page'));
            } else {
                $instances[$componentClass] = null;
            }
        }
        return $instances[$componentClass];
    }

    public function getChildData($parentData, $constraints)
    {
        $ret = array();
        foreach ($this->_additionalTreeCaches as $treeCache) {
            return array_merge($ret, $treeCache->getChildData($parentData, $constraints));
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
}
