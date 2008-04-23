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
    
    protected function __construct($class, Zend_Db_Adapter_Pdo_Mysql $db)
    {
        $this->_class = $class;
        $this->_db = $db;
        $this->_cache = self::getTreeCacheTable();
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
                $instances[$componentClass] = new $c($componentClass, Zend_Registry::get('db'));
            } else {
                $instances[$componentClass] = null;
            }
        }
        return $instances[$componentClass];
    }

    public function createRoot()
    {
        foreach ($this->_additionalTreeCaches as $treeCache) {
            $treeCache->createRoot();
        }
    }

    public function createChilds($componentId)
    {
        foreach ($this->_additionalTreeCaches as $treeCache) {
            $treeCache->createChilds($componentId);
        }
    }

    public function deleteChilds($componentId)
    {
        foreach ($this->_additionalTreeCaches as $treeCache) {
            $treeCache->deleteChilds($componentId);
        }
    }

    public function onInsertRow($row)
    {
        foreach ($this->_additionalTreeCaches as $treeCache) {
            $treeCache->onInsertRow($row);
        }
    }

    public function onUpdateRow($row)
    {
        foreach ($this->_additionalTreeCaches as $treeCache) {
            $treeCache->onUpdateRow($row);
        }
    }

    public function onDeleteRow($row)
    {
        foreach ($this->_additionalTreeCaches as $treeCache) {
            $treeCache->onDeleteRow($row);
        }
    }

    public function createMissingChilds()
    {
        foreach ($this->_additionalTreeCaches as $treeCache) {
            $treeCache->createMissingChilds();
        }
    }

    protected function _loggedQuery($sql, $bind = array())
    {
        $logger = false;
        if (Zend_Registry::isRegistered('debugLogger')) {
            $logger = Zend_Registry::get('debugLogger');
        }
        if ($logger) {
            $start = microtime(true);
            $logger->info(get_class($this));
            $logger->debug("$sql");
            if ($bind) $logger->debug(print_r($bind, true));
        }

        $ret = $this->_cache->getAdapter()->query($sql, $bind);

        if ($logger) {
            $time = round(microtime(true)-$start, 2);
            $logger->debug("Dauer: $time sec");
        }
        return $ret;
    }
}
