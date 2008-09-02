<?php
class Vps_Component_Data
{
    private $_component;

    private $_url;
    private $_rel;
    private $_filename;
    private $_constraintsCache = array();
    private $_recursiveGeneratorsCache = array();


    public function __construct($config)
    {
        foreach ($config as $k=>$i) {
            if ($k == 'url') {
                $this->_url = $i;
            } else if ($k == 'rel') {
                $this->_rel = $i;
            } else if ($k == 'filename') {
                $this->_filename = $i;
            } else {
                $this->$k = $i;
            }
        }
        if (!isset($this->dbId) && isset($this->componentId)) {
            $this->dbId = $this->componentId;
        }
        Vps_Benchmark::count('componentDatas', $this->componentId);
    }

    public function __get($var)
    {
        if ($var == 'url') {
            $filenames = array();
            if (!$this->isPage) {
                $page = $this->getPage();
                return $page->url;
            }
            $page = $this;
            do {
                if (Vpc_Abstract::getFlag($page->componentClass, 'shortcutUrl')) {
                    $filenames[] = call_user_func(array($page->componentClass, 'getShortcutUrl'), $page->componentClass, $page);
                    break;
                } else {
                    $filenames[] = $page->filename;
                }
            } while ($page = $page->getParentPseudoPage());
            return '/'.implode('/', array_reverse($filenames));
        } else if ($var == 'rel') {
            
            $childs = $this->getPage()->getRecursiveChildComponents(array(
                'flags' => array('noIndex' => true),
                'page' => false
            ));
            
            $rel = $this->getPage()->_rel;
            /*
            TODO
            if ($childs || $this->_hasFlags($this->componentClass, array('noIndex' => true))) {
                $rel .= ' nofollow';
            }*/
            return trim($rel);
        } else if ($var == 'filename') {
            return $this->getPseudoPage()->_filename;
        } else if ($var == 'visible') {
            if (isset($this->row->visible)) {
                return $this->row->visible;
            } else {
                return true;
            }
        } else {
            throw new Vps_Exception("Variable '$var' is not set for ".get_class($this) . " with componentId '{$this->componentId}'");
        }
    }

    public function __isset($var)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            return true;
        }
        return false;
    }

    public function __unset($var)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            throw new Vps_Exception("Variable '$var' can't be modified for ".get_class($this));
        } else {
            throw new Vps_Exception("Variable '$var' is not set for ".get_class($this));
        }
    }

    public function __set($var, $value)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            throw new Vps_Exception("Variable '$var' can't be modified for ".get_class($this));
        } else {
            $this->$var = $value;
        }
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            if (!isset($arguments[0]) && !is_null($arguments[0])) {
                throw new Vps_Exception("Missing argument 1 (value)");
            }
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            $this->$name = $arguments[0];
            return $this;
        } else if (substr($method, 0, 3) == 'get') {
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->$name;
        } else {
            throw new Vps_Exception("Invalid method called: '$method'");
        }
    }

    public function getGenerators($select = array())
    {
        return Vps_Component_Generator_Abstract::getInstances($this, $select);
    }

    public function getRecursiveGenerators($select,
                                $childSelect = array('page'=>false))
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        if (is_array($childSelect)) {
            $childSelect = new Vps_Component_Select($childSelect);
        }

        $cacheId = serialize($select->getParts());
        if (isset($this->_recursiveGeneratorsCache[$cacheId])) {
            Vps_Benchmark::count('getRecursiveGenerators hit');
            return $this->_recursiveGeneratorsCache[$cacheId];
        }
        Vps_Benchmark::count('getRecursiveGenerators miss', $this->componentClass.' '.$select->toDebug()."<br />");

        $ret = $this->getGenerators($select);
        $childSelect = $this->_formatChildConstraints($select, $childSelect);
        foreach ($this->getChildComponents($childSelect) as $component) {
            $ret = array_merge($ret, $component->getRecursiveGenerators($select, $childSelect));
        }
        $this->_recursiveGeneratorsCache[$cacheId] = $ret;
        return $ret;
    }

    public function getRecursiveChildComponents($select,
                                $childSelect = array('page'=>false))
    {
        Vps_Benchmark::count('getRecursiveChildComponents');

        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        if (is_array($childSelect)) {
            $childSelect = new Vps_Component_Select($childSelect);
        }
        $ret = $this->getChildComponents($select);
        if ($ret && $select->getPart(Vps_Component_Select::LIMIT_COUNT) == 1) {
            return $ret;
        }
        $childSelect = $this->_formatChildConstraints($select, $childSelect);
        foreach ($this->getChildComponents($childSelect) as $component) {
            $ret = array_merge($ret, $component->getRecursiveChildComponents($select, $childSelect));
        }
        return $ret;
    }

    private function _formatChildConstraints($select, $childSelect)
    {
        $childSelect = clone $childSelect;

        $select = clone $select;
        if ($select->hasPart(Vps_Model_Select::LIMIT_COUNT)) {
            $select->unsetPart(Vps_Model_Select::LIMIT_COUNT);
        }
        if ($select->hasPart(Vps_Component_Select::WHERE_FILENAME)) {
            $select->unsetPart(Vps_Component_Select::WHERE_FILENAME);
        }
        if ($select->hasPart(Vps_Component_Select::WHERE_HOME)) {
            $select->unsetPart(Vps_Component_Select::WHERE_HOME);
        }
        //if (!$childSelect->hasPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES)) {
            $classes = Vpc_Abstract::getIndirectChildComponentClasses($this->componentClass, $select);
        
/*
//########### versuch 1
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            $inheritClasses = Vpc_Abstract::getChildComponentClasses($c, array('inherit'=>true));
            foreach ($inheritClasses as $ic) {
                $childClasses = Vpc_Abstract::getIndirectChildComponentClasses($ic, $select);
                if ($childClasses) {
                    $classes = array_merge($classes, $childClasses, array($ic));
                }
            }
        }
//########### versuch 1
*/
/*
//########### versuch 2
        foreach (Vpc_Abstract::getComponentClasses() as $c) {
            $generators = Vpc_Abstract::getSetting($c, 'generators');
            foreach ($generators as $key=>$gen) {
                if (isset($gen['inherit']) && $gen['inherit']) {
                    $inheritClasses = $gen['component'];
                    if (!is_array($inheritClasses)) $inheritClasses = array($inheritClasses);
                    foreach ($inheritClasses as $ic) {
                        if (!$ic) continue;
                        $childClasses = Vpc_Abstract::getIndirectChildComponentClasses($ic, $select);
                        $classes = array_merge($classes, $childClasses, array($ic));
                    }
                }
            }
        }
//########### versuch 2
*/

//########### versuch 3

        $page = $this;
        while (1) {
            if ($this->parent instanceof Vps_Component_Data_Root) {
                $page = $page->parent;
            } else {
                $page = $page->getParentPage();
            }
            if (!$page) break;
            $classes = array_merge($classes,
                Vpc_Abstract::getIndirectChildComponentClasses($page->componentClass, $select)
            );
        }
//########### versuch 3

/*
//########### versuch 4
        static $inheritClasses = null;
        if (is_null($inheritClasses)) {
            foreach (Vpc_Abstract::getComponentClasses() as $c) {
                $generators = Vpc_Abstract::getSetting($c, 'generators');
                foreach ($generators as $key=>$gen) {
                    if (isset($gen['inherit']) && $gen['inherit']) {
                        if (!is_array($gen['component'])) $gen['component'] = array($gen['component']);
                        foreach ($gen['component'] as $ic) {
                            if (!$ic) continue;
                            $inheritClasses[] = $ic;
                        }
                    }
                }
            }
        }
        foreach ($inheritClasses as $ic) {
            $childClasses = Vpc_Abstract::getIndirectChildComponentClasses($ic, $select);
            $classes = array_merge($classes, $childClasses, array($ic));
        }
//########### versuch 4
*/
        //p($classes);
        
            $childSelect->whereComponentClasses(array_unique($classes));
        //}
        return $childSelect;
    }

    private function _formatSelect($select)
    {
        if (is_string($select)) {
            $select = array('id' => $select);
        }
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        return $select;
    }
    
    public function getChildComponents($select = array())
    {
        Vps_Benchmark::count('getChildComponents');

        $select = $this->_formatSelect($select);
        $sc = serialize($select->getParts());
        if (!isset($this->_constraintsCache[$sc])) {
            $ret = array();

            $this->_constraintsCache[$sc] = array();

            if ($select->getPart(Vps_Component_Select::WHERE_COMPONENT_CLASSES) === array()) {
                return $this->_constraintsCache[$sc]; //vorzeitig abbrechen, da kommt sicher kein ergebnis
            }

            if ($select->hasPart(Vps_Component_Select::LIMIT_COUNT)) {
                $limitCount = $select->getPart(Vps_Component_Select::LIMIT_COUNT);
            } else {
                $limitCount = null;
            }

            $generators = Vps_Component_Generator_Abstract::getStaticInstances($this->componentClass, $select);
            $this->_getChildComponentsForGenerators($this->_constraintsCache[$sc],
                                $generators, $select, $limitCount);
            if (!$limitCount || count($this->_constraintsCache[$sc]) < $limitCount) {
                $generators = Vps_Component_Generator_Abstract::getDynamicInstances($this, $select);
                $this->_getChildComponentsForGenerators($this->_constraintsCache[$sc],
                                $generators, $select, $limitCount);
            }


        }
        return $this->_constraintsCache[$sc];
    }
    private function _getChildComponentsForGenerators(&$ret, $generators, $select, $limitCount)
    {
        foreach ($generators as $generator) {

            $generatorSelect = clone $select;
            if ($limitCount) {
                $generatorSelect->limit($limitCount - count($ret));
            }

            foreach ($generator->getChildData($this, $generatorSelect) as $data) {
                if (isset($ret[$data->componentId])) {
                    $odata = $ret[$data->componentId];
                    if (isset($data->box) && isset($odata->box) && $data->box == $odata->box) {
                        if ($data->priority > $odata->priority) {
                            unset($ret[$data->componentId]);
                        } else {
                            continue;
                        }
                    }
                }
                if (isset($ret[$data->componentId])) {
                    throw new Vps_Exception("Key for generator not unique: {$data->componentId}");
                }
                $ret[$data->componentId] = $data;
            }

            if ($limitCount) {
                if ($limitCount - count($ret) <= 0) {
                    break;
                }
            }
        }
    }

    public function getChildPages($select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $select->wherePage(true);
        return $this->getRecursiveChildComponents($select);
    }

    public function getChildPseudoPages($select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $select->wherePseudoPage(true);
        return $this->getRecursiveChildComponents($select);
    }

    public function getChildBoxes($select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $select->whereBox(true);
        return $this->getRecursiveChildComponents($select);
    }

    public function getChildMultiBoxes($select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $select->whereMultiBox(true);
        return $this->getRecursiveChildComponents($select);
    }

    /**
     * Abkürzung für $this->getComponent()->hasContent()
     *
     * @return boolean $hasContent Ob die zugehörige Komponente Inhalt hat oder nicht
     */
    public function hasContent()
    {
        return $this->getComponent()->hasContent();
    }

    public function getChildPage($select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $select->limit(1);
        return current($this->getChildPages($select));
    }

    public function getChildPseudoPage($select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $select->limit(1);
        return current($this->getChildPseudoPages($select));
    }

    public function getGenerator($key)
    {
        return Vps_Component_Generator_Abstract::getInstance($this->componentClass, $key);
    }

    public function getChildComponentIds($constraints = array())
    {
        $ret = array();
        foreach ($this->getChildComponents($constraints) as $data) {
            $ret[] = $data->componentId;
        }
        return $ret;
    }

    public function getChildComponent($select = array())
    {
        $select = $this->_formatSelect($select);
        $select->limit(1);
        $cc = $this->getChildComponents($select);
        if (!$cc) return null;
        return current($cc);
    }

    public function getComponent()
    {
        if (!isset($this->_component)) {
            $component = new $this->componentClass($this);
            $this->_component = $component;
        }
        return $this->_component;
    }

    public function getPage()
    {
        $page = $this;
        while ($page && !$page->isPage) {
            $page = $page->parent;
        }
        return $page;
    }

    public function getPseudoPage()
    {
        $page = $this;
        while ($page && !$page->isPseudoPage) {
            $page = $page->parent;
        }
        return $page;
    }

    public function getParentPage()
    {
        $page = $this->getPage();
        if ($page && $page->parent) {
            return $page->parent->getPage();
        }
        return null;
    }

    public function getParentPseudoPage()
    {
        $page = $this->getPseudoPage();
        if ($page && $page->parent) {
            return $page->parent->getPseudoPage();
        }
        return null;
    }

    public function getTitle()
    {
        $title = array();
        $row = $this->getPage();
        do {
            if ($row->name != '') {
                $title[] = $row->name;
            }
        } while ($row = $row->getParentPage());
        return implode(' - ', $title);
    }

    public function getPlugins($interface = null)
    {
        $ret = array();
        $plugins = Vpc_Abstract::getSetting($this->componentClass, 'plugins');
        foreach ($plugins as $p) {
            if (!$interface || is_instance_of($p, $interface)) {
                $ret[] = $p;
            }
        }
        return $ret;
    }

    public function getChildPageByPath($path)
    {
        $page = $this;
        foreach (explode('/', $path) as $pathPart) {
            $page = $page->getChildPseudoPage(array('filename' => $pathPart));
            if (!$page) break;
        }
        return $page;
    }

/*
    public function toDebug()
    {
        $ret = '';
        foreach ($this as $k=>$i) {
            if ($k == 'parent') continue;
            $ret .= "$k: $i<br >\n";
        }
        return $ret;
    }
*/
}
?>