<?php
class Vps_Component_Data_Root extends Vps_Component_Data
{
    private static $_instance;
    private static $_rootComponentClass;
    private $_hasChildComponentCache;
    private $_componentsByClassCache;
    private $_currentPage;

    public function __construct($config = array())
    {
        $config = array_merge(array(
                'name' => 'Root',
                'parent' => null,
                'isPage' => false,
                'isPseudoPage' => false,
                'componentId' => 'root'
            ), $config
        );
        return parent::__construct($config);
    }
        
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new self(array('componentClass' => self::getComponentClass()));
        }
        return self::$_instance;
    }
    
    public static function getComponentClass()
    {
        if (is_null(self::$_rootComponentClass)) {
            self::$_rootComponentClass = Vps_Registry::get('config')->vpc->rootComponent;
        }
        return self::$_rootComponentClass;
    }
    
    public static function setComponentClass($componentClass)
    {
        self::$_rootComponentClass = $componentClass;
        self::$_instance = null;
    }
    
    public function getPageByPath($path)
    {
        if (substr($path, -1) == '/') {
            $path = substr($path, 0, -1);
        }
        if ($path == '') {
            return $this->getChildPage(array('home' => true));
        } else {
            $path = substr($path, 1);
            foreach (Vpc_Abstract::getComponentClasses() as $c) {
                if (Vpc_Abstract::getFlag($c, 'shortcutUrl')) {
                    $ret = call_user_func(array($c, 'getDataByShortcutUrl'), $c, $path);
                    if ($ret) return $ret;
                }
            }
            return $this->getChildPageByPath($path);
        }
    }

    public function getComponentById($componentId, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $ret = $this;
        foreach ($this->_getIdParts($componentId) as $idPart) {
            if ($idPart == 'root') {
                $ret = $this;
            } else {
                $select->whereId($idPart);
                $ret = $ret->getChildComponent($select);
                if (!$ret) break;
            }
        }
        return $ret;
    }

    private function _getIdParts($componentId)
    {
        $ret = array();
        $ids = preg_split('/([_\-])/', $componentId, -1, PREG_SPLIT_DELIM_CAPTURE);
        for ($i = 0; $i < count($ids); $i++) {
            if ($ids[$i] == '') {
                $i++;
            }
            $idPart = $ids[$i];
            if ($i > 0) {
                $i++;
                $idPart .= $ids[$i];
            }
            $ret[] = $idPart;
        }
        return $ret;
    }
    
    public function getComponentByDbId($dbId, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $select->limit(1);
        $cmp = $this->getComponentsByDbId($dbId, $select);
        return isset($cmp[0]) ? $cmp[0] : null;
    }

    public function getComponentsByDbId($dbId, $select = array())
    {
        $benchmark = Vps_Benchmark::start();

        if (is_numeric(substr($dbId, 0, 1))) {
            $data = $this->getComponentById($dbId, $select);
            if ($data) {
                return array($data);
            } else {
                return array();
            }
        }

        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }


        if ($select->hasPart(Vps_Component_Select::LIMIT_COUNT)) {
            $limitCount = $select->getPart(Vps_Component_Select::LIMIT_COUNT);
        }
        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                if (isset($generator['dbIdShortcut'])
                        && substr($dbId, 0, strlen($generator['dbIdShortcut'])) == $generator['dbIdShortcut']) {
                    $idParts = $this->_getIdParts(substr($dbId, strlen($generator['dbIdShortcut']) - 1));
                    $generator = Vps_Component_Generator_Abstract::getInstance($class, $key);
                    $generatorSelect = clone $select;
                    if (isset($limitCount)) {
                        $generatorSelect->limit($limitCount - count($ret));
                    }
                    $generatorSelect->whereId($idParts[0]);
                    $data = $generator->getChildData(null, $generatorSelect);
                    unset($idParts[0]);
                    $data = isset($data[0]) ? $data[0] : null;
                    foreach ($idParts as $idPart) {
                        if (!$data) break;
                        $select->whereId($idPart);
                        $data = $data->getChildComponent($select);
                    }
                    if ($data) {
                        $ret[] = $data;
                    }
                    if (isset($limitCount)) {
                        if ($limitCount - count($ret) <= 0) {
                        return $ret;
                        }
                    }
                }
            }
        }
        return $ret;
    }
    public function getComponentsByClass($class, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $cacheId = $class.serialize($select->getParts());
        if (!isset($this->_componentsByClassCache[$cacheId])) {
            $benchmark = Vps_Benchmark::start();

            $lookingForChildClasses = Vpc_Abstract::getComponentClassesByParentClass($class);
            foreach ($lookingForChildClasses as $c) {
                if (is_instance_of($c, 'Vpc_Root_Component')) {
                    return array($this);
                }
            }
            $select->whereComponentClasses($lookingForChildClasses);

            $ret = array();
            foreach ($this->_getGeneratorsForClasses($lookingForChildClasses) as $generator) {
                $ret = array_merge($ret, $generator->getChildData(null, $select));
            }

            $this->_componentsByClassCache[$cacheId] = $ret;
        }
        return $this->_componentsByClassCache[$cacheId];
    }

    private function _getGeneratorsForClasses($lookingForClasses)
    {
        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            foreach (Vpc_Abstract::getSetting($c, 'generators') as $key => $generator) {
                if (is_array($generator['component'])) {
                    $childClasses = $generator['component'];
                } else {
                    $childClasses = array($generator['component']);
                }
                foreach ($childClasses as $childClass) {
                    if (in_array($childClass, $lookingForClasses)) {
                        $ret[] = Vps_Component_Generator_Abstract::getInstance($c, $key);
                    }
                }
            }
        }
        return $ret;
    }

    public function getComponentByClass($class, $select = array())
    {
        $components = $this->getComponentsByClass($class, $select);
        if (isset($components[0])) {
            return $components[0];
        }
        return null;
    }

    public function setCurrentPage(Vps_Component_Data $page)
    {
        $this->_currentPage = $page;
    }

    public function getCurrentPage()
    {
        return $this->_currentPage;
    }
}
?>