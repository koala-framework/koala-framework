<?php
class Vps_Component_Data_Root extends Vps_Component_Data_Page {
    
    private static $_instance;
    
    public static function getInstance()
    {
        if (!self::$_instance) {
            $componentClass = Vps_Registry::get('config')->vpc->rootComponent;
            self::$_instance = new self(array(
                'componentClass' => $componentClass,
                'name' => '',
                'parent' => null
            ));
        }
        return self::$_instance;
    }
    
    public function getPageByPath($path)
    {
        $page = $this;
        foreach (explode('/', substr($path, 1)) as $pathPart) {
            $page = $page->getChildPage(array('filename' => $pathPart));
        }
        return $page;
    }

    public function getComponentById($componentId)
    {
        $page = $this;
        foreach (split('[_\-]', $componentId) as $idPart) {
            $page = $page->getChildComponent($idPart);
        }
        return $page;
    }
    
    public function getByDbId($dbId)
    {
        if (is_numeric(substr($dbId, 0, 1))) {
            return $this->getComponentById($dbId);
        }
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            $tc = $this->_getTreeCache($class);
            if ($tc && ($dbIdShortcut = $tc->getDbIdShortcut($dbId))) {
                foreach ($this->getComponentsByClass($class) as $data) {
                    $id = substr($dbId, strlen($dbIdShortcut));
                    foreach (split('[_\-]', $id) as $idPart) {
                        $data = $data->getChildComponent($idPart);
                    }
                    if ($data) return $data;
                }
            }
        }
        return null;
    }
    
    public function getComponentsByClass($classes, $data = null)
    {
        if (!is_array($classes)) {
            $parentClass = $classes;
            $classes = array();
            foreach (Vpc_Abstract::getComponentClasses() as $class) {
                if (is_subclass_of($class, $parentClass) || $class == $parentClass) {
                    $classes[] = $class;
                }
            }
        }
        $ret = array();
        if (!$data) $data = $this;
        foreach ($data->getChildComponents() as $childData) {
            if (in_array($childData->componentClass, $classes)) {
                $ret[] = $childData;
            }
            if ($this->_hasChildComponentClass(array($childData->componentClass), $classes)) {
                $ret = array_merge($ret, $this->getComponentsByClass($classes, $childData));
            }
        }
        return $ret;
    }
    
    private function _hasChildComponentClass($classes, $childClass)
    {
        foreach ($classes as $class) {
            if ($class) {
                $childClasses = Vpc_Abstract::getSetting($class, 'childComponentClasses');
                if (in_array($childClass, $childClasses)) return true;
                foreach ($childClasses as $c) {
                    if ($this->_hasChildComponentClass($classes, $c)) return true;
                }
            }
        }
        return false;
    }
    
    public function getComponentByClass($class)
    {
        $components = $this->getComponentsByClass($class);
        if (isset($components[0])) {
            return $components[0];
        }
        return null;
    }
}
?>