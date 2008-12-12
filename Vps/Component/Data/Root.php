<?php
class Vps_Component_Data_Root extends Vps_Component_Data
{
    private static $_instance;
    private static $_rootComponentClass;
    private $_hasChildComponentCache;
    private $_componentsByClassCache;
    private $_currentPage;
    private $_pageGenerators;

    public function __construct($config = array())
    {
        $config = array_merge(array(
                'name' => 'Root',
                'parent' => null,
                'isPage' => false,
                'isPseudoPage' => false,
                'inherits' => true,
                'componentId' => 'root'
            ), $config
        );
        parent::__construct($config);
        $this->_inheritClasses = array();
        $this->_uniqueParentDatas = array();
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
        Vps_Component_Abstract::resetSettingsCache();
    }

    public function getPageByUrl($url)
    {
        $parsedUrl = parse_url($url);
        $path = $this->getComponent()->formatPath($parsedUrl);
        if (substr($path, -1) == '/') {
            $path = substr($path, 0, -1);
        }
        if ($path == '') {
            $ret = $this->getChildPage(array('home' => true));
        } else {
            $path = substr($path, 1);
            foreach (Vpc_Abstract::getComponentClasses() as $c) {
                if (Vpc_Abstract::getFlag($c, 'shortcutUrl')) {
                    $ret = call_user_func(array($c, 'getDataByShortcutUrl'), $c, $path);
                    if ($ret) return $ret;
                }
            }
            $ret = $this->getChildPageByPath($path);
            if ($parsedUrl['path'] == '' || $parsedUrl['path'] == '/') {
                $ret = $ret->getChildPage(array('home' => true));
            }
        }
        return $ret;
    }

    public function getComponentById($componentId, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $ret = $this;
        $idParts = $this->_getIdParts($componentId);
        foreach ($idParts as $i=>$idPart) {
            if ($idPart == 'root') {
                $ret = $this;
            } else {
                if ($i+1 == count($idParts)) {
                    //nur bei letzem part select berücksichtigen
                    $select->whereId($idPart);
                    $s = $select;
                } else {
                    $s = array('id'=>$idPart);
                    if ($select->hasPart(Vps_Component_Select::IGNORE_VISIBLE)) {
                        //ignoreVisible doch mitnehmen damit wir unterkomponeten von unsichtbaren
                        //komponenten finden
                        $s['ignoreVisible'] = $select->getPart(Vps_Component_Select::IGNORE_VISIBLE);
                    }
                }

                if ($i == 0) { // Muss eine Page sein
                    $generators = $this->getPageGenerators();
                    foreach ($generators as $generator) {
                        $ret = array_pop($generator->getChildData(null, $s));
                        if ($ret) break;
                    }
                } else {
                    $ret = $ret->getChildComponent($s);
                }
                if (!$ret) break;
            }
        }
        return $ret;
    }

    public function getPageGenerators()
    {
        if (!$this->_pageGenerators) {
            $this->_pageGenerators = array();
            foreach (Vpc_Abstract::getComponentClasses() as $class) {
                foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                    if (is_instance_of($generator['class'], 'Vps_Component_Generator_Page')) {
                        $this->_pageGenerators[] = Vps_Component_Generator_Abstract::getInstance($class, $key, $generator);
                    }
                }
            }
        }
        return $this->_pageGenerators;
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
        if (is_numeric(substr($dbId, 0, 1)) || substr($dbId, 0, 4)=='root') {
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
                    if (count($idParts) <= 1) {
                        $generatorSelect = clone $select;
                    } else {
                        $generatorSelect = new Vps_Component_Select(); // Select erst bei letzten Part
                        if ($select->hasPart(Vps_Component_Select::IGNORE_VISIBLE)) {
                            $generatorSelect->ignoreVisible($select->getPart(Vps_Component_Select::IGNORE_VISIBLE));
                        }
                    }
                    if (isset($limitCount)) {
                        $generatorSelect->limit($limitCount - count($ret));
                    }
                    $generatorSelect->whereId($idParts[0]);
                    $data = $generator->getChildData(null, $generatorSelect);
                    if (isset($data[0])) {
                        unset($idParts[0]);
                        $componentId = $data[0]->componentId . implode('', $idParts);
                        $data = $this->getComponentById($componentId, $select);
                        if ($data) {
                            $ret[] = $data;
                        }
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
        if (is_instance_of($class, 'Vpc_Root_Abstract')) {
            $this->_componentsByClassCache[$cacheId] = array($this);
        }
        if (!isset($this->_componentsByClassCache[$cacheId])) {
            $benchmark = Vps_Benchmark::start();

            $lookingForChildClasses = Vpc_Abstract::getComponentClassesByParentClass($class);
            foreach ($lookingForChildClasses as $c) {
                if (is_instance_of($c, 'Vpc_Root_Component')) {
                    return array($this);
                }
            }
            $ret = $this->getComponentsBySameClass($lookingForChildClasses, $select);
            $this->_componentsByClassCache[$cacheId] = $ret;

            if ($benchmark) $benchmark->stop();
        }
        return $this->_componentsByClassCache[$cacheId];
    }

    public function getComponentsBySameClass($lookingForChildClasses, $select = array())
    {
        if (!is_array($lookingForChildClasses)) {
            $lookingForChildClasses = array($lookingForChildClasses);
        }

        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $select->whereComponentClasses($lookingForChildClasses);

        $ret = array();
        foreach ($this->_getGeneratorsForClasses($lookingForChildClasses) as $generator) {
            $data = $generator->getChildData(null, $select);
            $ret = array_merge($ret, $data);
        }
        return $ret;
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
