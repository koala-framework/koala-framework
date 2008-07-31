<?php
class Vps_Component_Data_Root extends Vps_Component_Data
{
    private static $_instance;
    private $_hasChildComponentCache;
    private $_componentsByClassCache;
    private $_currentPage;

    public static function getInstance()
    {
        if (!self::$_instance) {
            $componentClass = Vps_Registry::get('config')->vpc->rootComponent;
            self::$_instance = new self(array(
                'componentClass' => $componentClass,
                'name' => '',
                'parent' => null,
                'isPage' => false,
                'componentId' => 'root'
            ));
        }
        return self::$_instance;
    }
    
    public function getPageByPath($path)
    {
        if ($path == '/') {
            return $this->getChildPage(array('home' => true));
        } else {
            $page = $this;
            foreach (explode('/', substr($path, 1)) as $pathPart) {
                $page = $page->getChildPage(array('filename' => $pathPart));
                if (!$page) break;
            }
            return $page;
        }
    }

    public function getComponentById($componentId, array $constraints = array())
    {
        $page = $this;
        foreach ($this->_getIdParts($componentId) as $idPart) {
            $constraints['id'] = $idPart;
            $page = $page->getChildComponent($constraints);
            if (!$page) break;
        }
        return $page;
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
        $cmp = $this->getComponentsByDbId($dbId, $constraints, true);
        return isset($cmp[0]) ? $cmp[0] : null;
    }

    public function getComponentsByDbId($dbId, array $constraints = array(), $returnFirst=false)
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
                    if ($ret && $returnFirst) {
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
                // is_subclass_of funktioniert nicht, wahrscheinlich wegen autoload
                while ($c) {
                    if ($c == $class) {
                        $lookingForChildClasses[] = $c;
                        break;
                    }
                    $c = get_parent_class($c);
                }
            }

            $ret = array();
            foreach ($this->_getGeneratorsForClasses($lookingForChildClasses) as $generator) {
                $constraints = array('componentClass' => $lookingForChildClasses);
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

    public function getComponentByClass($class)
    {
        $components = $this->getComponentsByClass($class);
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