<?php
class Vps_Component_Data
{
    private $_component;

    private $_url;
    private $_rel;
    private $_filename;
    protected $_inheritClasses;
    protected $_uniqueParentDatas;

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
        if ($this->isPage) {
            Vps_Benchmark::count('componentData Pages', $this->componentId);
        }
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
                if (!empty($filenames) && Vpc_Abstract::getFlag($page->componentClass, 'shortcutUrl')) {
                    $filenames[] = call_user_func(array($page->componentClass, 'getShortcutUrl'), $page->componentClass, $page);
                    break;
                } else {
                    if ($page->filename) $filenames[] = $page->filename;
                }
            } while ($page = $page->getParentPseudoPage());
            return '/'.implode('/', array_reverse($filenames));
        } else if ($var == 'rel') {
            /*
            $childs = $this->getPage()->getRecursiveChildComponents(array(
                'flags' => array('noIndex' => true),
                'page' => false
            ));*/
            $rel = $this->getPage()->_rel;
            if (/*$childs || */Vps_Component_Abstract::getFlag($this->getPage()->componentClass, 'noIndex')) {
                $rel .= ' nofollow';
            }
            return trim($rel);
        } else if ($var == 'filename') {
            return $this->getPseudoPage()->_filename;
        } else if ($var == 'inherits') {
            return false;
        } else if ($var == 'visible') {
            if (isset($this->row->visible)) {
                return $this->row->visible;
            } else {
                return true;
            }
        } else if ($var == 'inheritClasses') {
            if (!isset($this->_inheritClasses)) {
                $this->_uniqueParentDatas = array();
                $this->_inheritClasses = array();
                if ($this->inherits) {
                    $page = $this;
                    $foundInheritGeneratorPage = false;
                    while (($page = $page->parent) && !$foundInheritGeneratorPage) {
                        if (!$page->inherits) continue;
                        foreach (Vpc_Abstract::getSetting($page->componentClass, 'generators') as $gKey=> $g) {
                            if (isset($g['inherit']) && $g['inherit']) {
                                if (!$foundInheritGeneratorPage) {
                                    $this->_inheritClasses[] = $page->componentClass;
                                    $this->_inheritClasses = array_merge($this->_inheritClasses, $page->inheritClasses);
                                    $this->_uniqueParentDatas = $page->_uniqueParentDatas;
                                }
                                if (isset($g['unique']) && $g['unique']) {
                                    $this->_uniqueParentDatas[$page->componentClass.$gKey] = $page;
                                }
                                $foundInheritGeneratorPage = true;
                            }
                        }
                    }
                }
            }
            return $this->_inheritClasses;
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

    public function getRecursiveChildComponents($select = array(), $childSelect = array('page'=>false))
    {
        static $cache = null;
        if (!$cache) {
            $cache = Vps_Cache::factory('Core', 'Memcached', array(
                'lifetime'=>null,
                'automatic_cleaning_factor' => false,
                'automatic_serialization'=>true));
        }

        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        } else {
            $select = clone $select;
        }
        Vps_Benchmark::count('getRecursiveChildComponents', $this->componentId.' '.$select->toDebug());
        if (is_array($childSelect)) {
            $childSelect = new Vps_Component_Select($childSelect);
        }
        $ret = $this->getChildComponents($select);

        $genSelect = new Vps_Component_Select();
        $genSelect->copyParts(array(
            Vps_Component_Select::WHERE_HOME,
            Vps_Component_Select::WHERE_PAGE,
            Vps_Component_Select::WHERE_PSEUDO_PAGE,
            Vps_Component_Select::WHERE_FLAGS,
            Vps_Component_Select::WHERE_BOX,
            Vps_Component_Select::WHERE_MULTI_BOX,
            Vps_Component_Select::WHERE_SHOW_IN_MENU,
            Vps_Component_Select::WHERE_COMPONENT_CLASSES,
            Vps_Component_Select::WHERE_PAGE_GENERATOR,
            Vps_Component_Select::WHERE_GENERATOR
        ), $select);

        $selectHash = md5($genSelect->getHash().$childSelect->getHash());
        $cacheId = 'recCCGen'.$selectHash.$this->componentClass.implode('__', $this->inheritClasses);
        if (isset($this->_recursiveGeneratorsCache[$cacheId])) {
            Vps_Benchmark::count('getRecCC Gen hit', $this->componentClass.' '.$genSelect->toDebug());
            $generators = $this->_recursiveGeneratorsCache[$cacheId];
        } else if (($generators = $cache->load($cacheId)) !== false) {
            Vps_Benchmark::count('getRecCC Gen semi-hit', $this->componentClass.' '.$genSelect->toDebug());
            $this->_recursiveGeneratorsCache[$cacheId] = $generators;
        } else {
            Vps_Benchmark::count('getRecCC Gen miss', $this->componentClass.' '.$genSelect->toDebug());
            $generators = array();
            foreach (Vpc_Abstract::getChildComponentClasses($this, $childSelect) as $class) {
                $g = $this->_getRecursiveGenerators($class, $genSelect, $childSelect, $selectHash);
                $generators = array_merge($generators, $g);
            }
            $this->_recursiveGeneratorsCache[$cacheId] = $generators;
            $cache->save($generators, $cacheId);
        }

        $select->whereOnSamePage($this);
        foreach ($generators as $g) {
            if (!$g['static']) {
                $gen = Vps_Component_Generator_Abstract::getInstance($g['class'], $g['key']);
                foreach ($gen->getChildData(null, $select) as $d) {
                    if (!in_array($d, $ret, true)) {
                        $ret[] = $d;
                    }
                }
            }
        }

        $staticGeneratorComponentClasses = array();
        foreach ($generators as $k=>$g) {
            if ($g['static']) {
                $staticGeneratorComponentClasses[] = $g['class'];
            }
        }
        if ($staticGeneratorComponentClasses) {
            $pd = $this->getRecursiveChildComponents(array(
                'componentClasses' => $staticGeneratorComponentClasses
            ));
            foreach ($generators as $k=>$g) {
                if ($g['static']) {
                    $parentDatas = array();
                    foreach ($pd as $d) {
                        if ($d->componentClass == $g['class']) {
                            $parentDatas[] = $d;
                        }
                    }
                    if ($parentDatas) {
                        $gen = Vps_Component_Generator_Abstract::getInstance($g['class'], $g['key']);
                        foreach ($gen->getChildData($parentDatas, $select) as $d) {
                            if (!in_array($d, $ret, true)) {
                                $ret[] = $d;
                            }
                        }
                    }
                }
            }
        }
        return $ret;
    }

    private function _getRecursiveGenerators($componentClass, $select, $childSelect, $selectHash)
    {
        $cacheId = $componentClass.$selectHash;
        Vps_Benchmark::count('_getRecursiveGenerators');
        if (isset($this->_recursiveGeneratorsCache[$cacheId])) {
            return $this->_recursiveGeneratorsCache[$cacheId];
        }

        $ret = array();
        $this->_recursiveGeneratorsCache[$cacheId] = array();
        foreach (Vps_Component_Generator_Abstract::getInstances($componentClass, $select) as $generator) {
            if ($generator->getChildComponentClasses($select)) {
                $ret[] = array(
                    'static' => $generator instanceof Vps_Component_Generator_Static,
                    'class' => $generator->getClass(),
                    'key' => $generator->getGeneratorKey()
                );
            }
        }
        foreach (Vps_Component_Generator_Abstract::getInstances($componentClass, $childSelect) as $generator) {
            foreach ($generator->getChildComponentClasses() as $c) {
                if ($c)
                    $ret = array_merge($ret, $this->_getRecursiveGenerators($c, $select, $childSelect, $selectHash));
            }
        }
        $this->_recursiveGeneratorsCache[$cacheId] = $ret;
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
        $classes = Vpc_Abstract::getIndirectChildComponentClasses($this->componentClass, $select);
        $page = $this;
        $ic = $this->inheritClasses;
        foreach ($ic as $c) {
            $classes = array_merge($classes,
                Vpc_Abstract::getIndirectChildComponentClasses($c, $select)
            );
        }
        // Nur bei hasEditComponents, Root soll keine Domain-Komponenten anzeigen
        if ($select->hasPart(Vps_Component_Select::WHERE_HAS_EDIT_COMPONENTS) &&
            $this instanceof Vps_Component_Data_Root
        ) {
            $cc = array();
            foreach ($classes as $class) {
                if (!is_instance_of($class, 'Vpc_Root_DomainRoot_Domain_Component')) {
                    $cc[] = $class;
                }
            }
            $classes = $cc;
        }
        $childSelect->whereComponentClasses(array_unique($classes));
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
    public function countChildComponents($select = array())
    {
        Vps_Benchmark::count('countChildComponents');

        $select = $this->_formatSelect($select);

        if (!$select->hasPart(Vps_Component_Select::WHERE_GENERATOR)) {
            throw new Vps_Exception("You can count only for one generator at a time");
        }
        $generators = Vps_Component_Generator_Abstract::getInstances($this->componentClass, $select);
        return current($generators)->countChildData($this, $select);
    }

    public function getChildComponents($select = array())
    {
        $select = $this->_formatSelect($select);
        $sc = $select->getHash();
        if (isset($this->_constraintsCache[$sc])) {
            Vps_Benchmark::count('getChildComponents cached', $select->toDebug());
        } else {
            Vps_Benchmark::count('getChildComponents uncached');
        }

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

            $generators = Vps_Component_Generator_Abstract::getInstances($this, $select);
            $ret = array();

            foreach ($generators as $generator) {
                $generatorSelect = clone $select;
                if ($limitCount) {
                    $generatorSelect->limit($limitCount - count($ret));
                }
                $genId = $generator->getClass().$generator->getGeneratorKey();
                if (isset($this->_uniqueParentDatas[$genId])) {
                    $parentData = $this->_uniqueParentDatas[$genId];
                } else {
                    $parentData = $this;
                }
                foreach ($generator->getChildData($parentData, $generatorSelect) as $data) {
                    if (isset($ret[$data->componentId])) {
                        throw new Vps_Exception("Id not unique: {$data->componentId}");
                    }
                    $ret[$data->componentId] = $data;

                    if ($limitCount) {
                        if ($limitCount - count($ret) <= 0) {
                            break 2;
                        }
                    }
                }
            }
            $this->_constraintsCache[$sc] = $ret;
        }
        return $this->_constraintsCache[$sc];
    }

    public function getChildPages($select = array(), $childSelect = array('page'=>false))
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $select->wherePage(true);
        return $this->getRecursiveChildComponents($select, $childSelect);
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
        return $this->getChildComponents($select);
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

    public function getChildPage($select = array(), $childSelect = array('page'=>false))
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $select->limit(1);
        return current($this->getChildPages($select, $childSelect));
    }

    public function getChildPseudoPage($select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $select->limit(1);
        $ret = $this->getChildPseudoPages($select);
        if (!$ret) return null;
        return current($ret);
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

    public function getPageOrRoot()
    {
        $page = $this;
        while ($page && !$page->isPage) {
            if ($page instanceof Vps_Component_Data_Root) return $page;
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

    public function getParentPageOrRoot()
    {
        $page = $this->getPageOrRoot();
        if ($page && $page->parent) {
            return $page->parent->getPageOrRoot();
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
            if ($row->name != '' && $row->name != 'Home') {
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
            $pages = $page->getRecursiveChildComponents(array(
                                'filename' => $pathPart,
                                'pseudoPage'=>true,
                                'limit'=>1),
                            array('pseudoPage'=>false));
            $page = current($pages);
            if (!$page) break;
        }
        return $page;
    }

    public function toDebug()
    {
        return $this->componentId . ' (' . $this->componentClass . ')';
    }
}
?>
