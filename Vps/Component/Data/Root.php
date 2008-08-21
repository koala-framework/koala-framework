<?php
class Vps_Component_Data_Root extends Vps_Component_Data
{
    private static $_instance;
    private $_hasChildComponentCache;
    private $_componentsByClassCache;
    private $_currentPage;

    public function __construct($config = array())
    {
        $config = array_merge(array(
                'componentClass' => Vps_Registry::get('config')->vpc->rootComponent,
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
        if (!self::$_instance) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }
   /* 
    public function getChildComponents($constraints = array())
    {
        $pageTypes = Zend_Registry::get('config')->vpc->pageTypes->toArray();
        if ($this->componentId == 'root'
            && !isset($constraints['hasEditComponents'])
            && !isset($constraints['home'])
            && (
                (isset($constraints['id']) && isset($pageTypes[$constraints['id']])) || 
                (!isset($constraints['type']) && !isset($constraints['id']))
            )
        ) {
            $ret = array();
            foreach ($pageTypes as $id => $name) {
                if (!isset($constraints['id']) || $id == $constraints['id']) {
                    $ret[$id] = new Vps_Component_Data_Category($id, $name);
                }
            }
            return $ret;
        } else {
            return parent::getChildComponents($constraints);
        }
    }
*/
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

    public function getComponentById($componentId, array $constraints = array())
    {
        $ret = $this;
        foreach ($this->_getIdParts($componentId) as $idPart) {
            if ($idPart == 'root') {
                $ret = $this;
            } else {
                $constraints['id'] = $idPart;
                $ret = $ret->getChildComponent($constraints);
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
    
    public function getComponentByDbId($dbId, array $constraints = array())
    {
        $constraints['limit'] = 1;
        $cmp = $this->getComponentsByDbId($dbId, $constraints);
        return isset($cmp[0]) ? $cmp[0] : null;
    }

    public function getComponentsByDbId($dbId, array $constraints = array())
    {
        $benchmark = Vps_Benchmark::start();

        if (is_numeric(substr($dbId, 0, 1))) {
            $data = $this->getComponentById($dbId, $constraints);
            if ($data) {
                return array($data);
            } else {
                return array();
            }
        }

        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                if (isset($generator['dbIdShortcut'])
                        && substr($dbId, 0, strlen($generator['dbIdShortcut'])) == $generator['dbIdShortcut']) {
                    $idParts = $this->_getIdParts(substr($dbId, strlen($generator['dbIdShortcut']) - 1));
                    $generator = Vps_Component_Generator_Abstract::getInstance($class, $key);
                    $constraints['id'] = $idParts[0];
                    $data = $generator->getChildData(null, $constraints);
                    unset($idParts[0]);
                    $data = isset($data[0]) ? $data[0] : null;
                    foreach ($idParts as $idPart) {
                        if (!$data) break;
                        $constraints['id'] = $idPart;
                        $data = $data->getChildComponent($constraints);
                    }
                    if ($data) {
                        $ret[] = $data;
                    }
                    if ($ret && isset($constraints['limit']) && count($ret) >= $constraints['limit']) {
                        return $ret;
                    }
                }
            }
        }
        return $ret;
    }
    public function getComponentsByClass($class)
    {
        if (!isset($this->_componentsByClassCache[$class])) {
            $benchmark = Vps_Benchmark::start();

            // Man sucht die Komponenten der übergebenen und aller Unterklassen
            $lookingForChildClasses = array();
            foreach (Vpc_Abstract::getComponentClasses() as $c) {
                foreach (Vpc_Abstract::getParentClasses($c) as $p) {
                    if ($p == $class) {
                        $lookingForChildClasses[] = $c;
                        break;
                    }
                }
            }

            foreach ($lookingForChildClasses as $c) {
                if (is_instance_of($c, 'Vpc_Root_Component')) {
                    return array($this);
                }
            }
            $constraints = array('componentClass' => $lookingForChildClasses);

            $ret = array();
            foreach ($this->_getGeneratorsForClasses($lookingForChildClasses) as $generator) {
                $ret = array_merge($ret, $generator->getChildData(null, $constraints));
            }

            $this->_componentsByClassCache[$class] = $ret;
        }
        return $this->_componentsByClassCache[$class];
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

    public function getComponentByClass($class, $constraints = array())
    {
        $components = $this->getComponentsByClass($class, $constraints);
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