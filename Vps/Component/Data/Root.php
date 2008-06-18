<?php
class Vps_Component_Data_Root extends Vps_Component_Data_Page {
    
    private static $_instance;
    
    public static function getInstance()
    {
        if (!self::$_instance) {
            self::$_instance = new self(array('componentClass' => 'Vpc_Root_Component'));
        }
        return self::$_instance;
    }
    
    public function getChildPages($constraints = array())
    {
        $childPages = array();
        $ret = array();
        foreach (Vpc_Abstract::getComponentClasses() as $class) {
            $tc = Vpc_TreeCache_Abstract::getInstance($class);
            if ($tc) { // TODO interface root oä.
                $ret = array_merge($ret, $tc->getChildData($this, $constraints));
            }
        }
        return $ret;
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
            // TODO: parent von Seitenbaum finden
            $page = $page->getChildComponent(array('id' => $idPart));
        }
        return $page;
    }
}
?>