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
                'componentId' => null
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

    public function getComponentById($componentId, $page = null)
    {
        if (!$page) $page = $this;
        foreach ($this->_getIdParts($componentId) as $idPart) {
            $page = $page->getChildComponent($idPart);
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
    
    public function getByDbId($dbId)
    {
        $cmp = $this->getComponentsByDbId($dbId, true);
        return isset($cmp[0]) ? $cmp[0] : null;
    }

    public function getComponentsByDbId($dbId, $returnFirst=false)
    {
        $benchmark = Vps_Benchmark::start();

        if (is_numeric(substr($dbId, 0, 1))) {
            $data = $this->getComponentById($dbId);
            return array($data);
        }

        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            $tc = Vpc_TreeCache_Abstract::getInstance($class);
            if (!$tc) continue;
            if ($dbIdShortcut = $tc->getDbIdShortcut($dbId)) {
                $idParts = $this->_getIdParts(substr($dbId, strlen($dbIdShortcut) - 1));
                $data = $tc->getChildData(null, array('id' => $idParts[0]));
                unset($idParts[0]);
                $data = isset($data[0]) ? $data[0] : null;
                foreach ($idParts as $idPart) {
                    if (!$data) break;
                    $data = $data->getChildComponent($idPart);
                }
                if ($data) {
                    $ret[] = $data;
                }
                if ($ret && $returnFirst) {
                    return $ret;
                }
            }
        }
        return $ret;
    }
    public function getComponentsByClass($class)
    {
        if (!isset($this->_componentsByClassCache[$class])) {
            $benchmark = Vps_Benchmark::start();

            $lookingForChildClasses = array();
            foreach (Vpc_Abstract::getComponentClasses() as $c) {
                if (is_subclass_of($c, $class) || $c == $class) {
                    $lookingForChildClasses[] = $c;
                }
            }

            $ret = array();
            foreach ($this->_getCreatorsForClasses($lookingForChildClasses) as $c) {
                $tc = Vpc_TreeCache_Abstract::getInstance($c);
                if (!$tc) {
                    throw new Vps_Exception("No TreeCache found for '$c' although it has childComponentClasses");
                }
                $constraints = array('componentClass' => $lookingForChildClasses);
                $ret = array_merge($ret, $tc->getChildData(null, $constraints));
            }
            $this->_componentsByClassCache[$class] = $ret;
        }
        return $this->_componentsByClassCache[$class];
    }

    //gibt ein array von komponenten-klassen zurück die die übergebenen klassen
    //erstellen können
    private function _getCreatorsForClasses($lookingForClasses)
    {
        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            $cc = Vpc_Abstract::getChildComponentClasses($c);
            foreach ($cc as $childClass) {
                if (in_array($childClass, $lookingForClasses)) {
                    $ret[] = $c;
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