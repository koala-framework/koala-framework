<?php
class Kwf_Component_Data
{
    /**
     * @var Kwc_Abstract
     */
    private $_component;

    private $_url;
    private $_rel;
    protected $_filename;
    protected $_inheritClasses;
    protected $_uniqueParentDatas;

    private $_childComponentsCache = array();
    private $_recursiveGeneratorsCache = array();
    private $_languageCache;

    //public static $objectsCount;
    //public static $objectsById = array();

    public function __construct($config)
    {
        foreach ($config as $k=>$i) {
            if ($k == 'url') {
                $this->_url = $i;
            } else if ($k == 'rel') {
                $this->_rel = $i;
            } else if ($k == 'filename') {
                $this->_filename = $i;
            } else if ($k == 'unserialized') {
            } else {
                $this->$k = $i;
            }
        }
        if (!isset($this->dbId) && isset($this->componentId)) {
            $this->dbId = $this->componentId;
        }

        //self::$objectsCount++;
        //if (!isset(self::$objectsById[$this->componentId])) self::$objectsById[$this->componentId] = 0;
        //self::$objectsById[$this->componentId]++;

        if (isset($config['unserialized']) && $config['unserialized']) {
            Kwf_Benchmark::count('unserialized componentDatas', $this->componentId);
        } else {
            Kwf_Benchmark::count('componentDatas', $this->componentId);
        }
    }

    public function __destruct()
    {
        //self::$objectsCount--;
        //self::$objectsById[$this->componentId]--;
        //if (!self::$objectsById[$this->componentId]) unset(self::$objectsById[$this->componentId]);
    }

    /**
     * Like ->url but also works for pseudoPages
     *
     * overridden in Data_Home
     */
    protected function _getPseudoPageUrl()
    {
        $data = $this;
        $filename = '';
        $hadStaticPage = false;
        do {
            if ($data->isPseudoPage || $data->componentId == 'root') {
                if ($filename && Kwc_Abstract::getFlag($data->componentClass, 'shortcutUrl')) {
                    $filename = call_user_func(array($data->componentClass, 'getShortcutUrl'), $data->componentClass, $data).($filename ? '/' : '').$filename;
                    break;
                } else {
                    if ($data->filename) $filename = $data->filename.($filename ? '/' : '').$filename;
                }
                if ($data->componentId != 'root' && $data->generator->getGeneratorFlag('static')) {
                    $hadStaticPage = true;
                } else {
                    $hadStaticPage = false;
                }
            } else {
                if ($hadStaticPage && $data->generator->getGeneratorFlag('table')) {
                    $filename = $data->id.($filename ? ':' : '').$filename;
                }
            }
        } while ($data = $data->parent);

        $urlPrefix = Kwf_Config::getValue('kwc.urlPrefix'); //TODO urlPrefix vs. root filename: both do the same
        return ($urlPrefix ? $urlPrefix : '').'/'.$filename;
    }

    /**
     * Returns absolute url including domain
     *
     * @return string
     */
    public function getAbsoluteUrl()
    {
        $ret = $this->url;
        $data = $this;
        do {
            if (Kwc_Abstract::getFlag($data->componentClass, 'hasDomain')) {
                return 'http://'.$data->getComponent()->getDomain().$ret;
            }
        } while($data = $data->parent);
        return 'http://'.Kwf_Config::getValue('server.domain').$ret;
    }

    /**
     * Returns absolute url including preview domain
     *
     * @return string
     */
    public function getAbsolutePreviewUrl()
    {
        $ret = $this->url;
        $data = $this;
        do {
            if (Kwc_Abstract::getFlag($data->componentClass, 'hasDomain')) {
                return 'http://'.$data->getComponent()->getPreviewDomain().$ret;
            }
        } while($data = $data->parent);

        if (Kwf_Config::getValue('server.previewDomain')) {
            return 'http://' . Kwf_Config::getValue('server.previewDomain') . $ret;
        } else {
            return 'http://' . Kwf_Config::getValue('server.domain') . $ret;
        }
    }

    public function __get($var)
    {
        if ($var == 'url') {
            if (!$this->isPage) {
                $page = $this->getPage();
                if (!$page) return '';
                return $page->url;
            }
            return $this->_getPseudoPageUrl();
        } else if ($var == 'rel') {
            /*
            $childs = $this->getPage()->getRecursiveChildComponents(array(
                'flags' => array('noIndex' => true),
                'page' => false
            ));*/
            $page = $this->getPage();
            if (!$page) return '';
            $rel = $page->_rel;
            if (/*$childs || */Kwf_Component_Abstract::getFlag($this->getPage()->componentClass, 'noIndex')) {
                $rel .= ' nofollow';
            }
            $contentSender = Kwc_Abstract::getSetting($page->componentClass, 'contentSender');
            if ($contentSender != 'Kwf_Component_Abstract_ContentSender_Default') { //skip for performance
                $contentSender = new $contentSender($page);
                $rel .= ' '.$contentSender->getLinkRel();
            }
            return trim($rel);
        } else if ($var == 'filename') {
            return rawurlencode($this->getPseudoPageOrRoot()->_filename);
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
                    while (($page = $page->parent)) {
                        foreach (Kwc_Abstract::getSetting($page->componentClass, 'generators') as $gKey=> $g) {
                            if (isset($g['inherit']) && $g['inherit']) {
                                if (!in_array($page->componentClass, $this->_inheritClasses)) {
                                    $this->_inheritClasses[] = $page->componentClass;
                                }
                                if (isset($g['unique']) && $g['unique']) {
                                    $this->_uniqueParentDatas[$page->componentClass.$gKey] = $page;
                                }
                            }
                        }
                        if ($page->inherits) {
                            //wenn page selbst erbt einfach von da übernehmen (rekursiver aufruf)
                            $this->_inheritClasses = array_merge($this->_inheritClasses, $page->inheritClasses);
                            $this->_uniqueParentDatas = array_merge($this->_uniqueParentDatas, $page->_uniqueParentDatas);
                            break; //aufhören, rest kommt durch rekursion daher
                        }
                    }
                }
            }
            return $this->_inheritClasses;
        } else if ($var == '_uniqueParentDatas') {
            $this->inheritClasses; //populates _uniqueParentDatas as side effect
            return $this->_uniqueParentDatas;
        } else if ($var == 'parent' && isset($this->_lazyParent)) {
            $ret = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_lazyParent, array('ignoreVisible'=>true));
            $this->parent = $ret;
            unset($this->_lazyParent);
            return $ret;
        } else if ($var == 'generator' && isset($this->_lazyGenerator)) {
            $ret = Kwf_Component_Generator_Abstract::getInstance($this->_lazyGenerator[0], $this->_lazyGenerator[1]);
            $this->generator = $ret;
            unset($this->_lazyGenerator);
            return $ret;
        } else if ($var == 'row' && isset($this->_lazyRow)) {
            $ret = $this->generator->getModel()->getRow($this->_lazyRow);
            $this->row = $ret;
            unset($this->_lazyRow);
            return $ret;
        } else if ($var == 'chained' && isset($this->_lazyChained)) {
            $ret = Kwf_Component_Data_Root::getInstance()->getComponentById($this->_lazyChained, array('ignoreVisible'=>true));
            $this->chained = $ret;
            unset($this->_lazyChained);
            return $ret;
        } else {
            throw new Kwf_Exception("Variable '$var' is not set for ".get_class($this) . " with componentId '{$this->componentId}'");
        }
    }

    public function __isset($var)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            return true;
        }
        if (substr($var, 0, 5) != '_lazy') {
            $lazyVar = '_lazy' . ucfirst($var);
            if (isset($this->$lazyVar)) return true;
        }
        return false;
    }

    public function __unset($var)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            throw new Kwf_Exception("Variable '$var' can't be modified for ".get_class($this));
        } else {
            throw new Kwf_Exception("Variable '$var' is not set for ".get_class($this));
        }
    }

    public function __set($var, $value)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            throw new Kwf_Exception("Variable '$var' can't be modified for ".get_class($this));
        } else {
            $this->$var = $value;
        }
    }

    public function __call($method, $arguments)
    {
        if (substr($method, 0, 3) == 'set') {
            if (!isset($arguments[0]) && !is_null($arguments[0])) {
                throw new Kwf_Exception("Missing argument 1 (value)");
            }
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            $this->$name = $arguments[0];
            return $this;
        } else if (substr($method, 0, 3) == 'get') {
            $name = strtolower(substr($method, 3, 1)) . substr($method, 4);
            return $this->$name;
        } else {
            throw new Kwf_Exception("Invalid method called: '$method'");
        }
    }

    public function getRecursiveChildComponents($select = array(), $childSelect = array('page'=>false))
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        } else {
            $select = clone $select;
        }
        Kwf_Benchmark::count('getRecursiveChildComponents');
        if (is_array($childSelect)) {
            $childSelect = new Kwf_Component_Select($childSelect);
        }
        $ret = $this->getChildComponents($select);
        if ($select->hasPart('limitCount') && $select->getPart('limitCount') <= count($ret)) {
            return $ret;
        }

        $genSelect = new Kwf_Component_Select();
        $genSelect->copyParts(array(
            Kwf_Component_Select::WHERE_HOME,
            Kwf_Component_Select::WHERE_PAGE,
            Kwf_Component_Select::WHERE_PSEUDO_PAGE,
            Kwf_Component_Select::WHERE_FLAGS,
            Kwf_Component_Select::WHERE_BOX,
            Kwf_Component_Select::WHERE_MULTI_BOX,
            Kwf_Component_Select::WHERE_SHOW_IN_MENU,
            Kwf_Component_Select::WHERE_COMPONENT_CLASSES,
            Kwf_Component_Select::WHERE_PAGE_GENERATOR,
            Kwf_Component_Select::WHERE_GENERATOR,
            Kwf_Component_Select::WHERE_HAS_EDIT_COMPONENTS,
            Kwf_Component_Select::WHERE_INHERIT,
            Kwf_Component_Select::WHERE_UNIQUE,
            Kwf_Component_Select::WHERE_GENERATOR_CLASS,
            Kwf_Component_Select::WHERE_COMPONENT_KEY,
        ), $select);

        $selectHash = md5($genSelect->getHash().$childSelect->getHash());
        $cacheId = 'recCCGen-'.$selectHash.$this->componentClass.implode('__', $this->inheritClasses);
        $generators = Kwf_Cache_Simple::fetch($cacheId, $success);
        if (!$success) {
            //get (statically=fast and cached) all generators that could create the component we are looking for
            $generators = $this->_getRecursiveGenerators(
                        Kwc_Abstract::getChildComponentClasses($this, $childSelect),
                        $genSelect, $childSelect, $selectHash);
            Kwf_Cache_Simple::add($cacheId, $generators);
        }

        $noSubPages =
            $childSelect->hasPart('wherePage') && !$childSelect->getPart('wherePage') ||
            $childSelect->hasPart('wherePseudoPage') && !$childSelect->getPart('wherePseudoPage');
        if ($noSubPages) {
            $select->whereChildOfSamePage($this);
        } else {
            $select->whereSubroot($this);
        }

        foreach ($generators as $g) {
            if ($g['type'] == 'notStatic') {
                $gen = Kwf_Component_Generator_Abstract::getInstance($g['class'], $g['key']);
                foreach ($gen->getChildData(null, clone $select) as $d) {
                    $add = true;
                    if (!$noSubPages) { // sucht über unterseiten hinweg, wird hier erst im Nachhinein gehandelt, langsam
                        $add = false;
                        $c = $d;
                        while (!$add && $c) {
                            if ($c->componentId == $this->componentId) $add = true;
                            $c = $c->parent;
                        }
                    }
                    if ($add && !in_array($d, $ret, true)) {
                        $ret[] = $d;
                        if ($select->hasPart('limitCount') && $select->getPart('limitCount') <= count($ret)) {
                            return $ret;
                        }
                    }
                }
            }
        }

        foreach ($generators as $k=>$g) {
            if ($g['type'] == 'cards') {
                $lookingForDefault = true;
                if ($select->hasPart('whereComponentClasses')) {
                    $gen = Kwf_Component_Generator_Abstract
                            ::getInstance($g['class'], $g['key'], array(), $g['pluginBaseComponentClass']);
                    $classes = array_values($gen->getChildComponentClasses());
                    $defaultCardClass = $classes[0];
                    if (!in_array($defaultCardClass, $select->getPart('whereComponentClasses'))) {
                        $lookingForDefault = false;
                    }
                }
                if ($lookingForDefault) {
                    //we have to look for it like for a static component because it's the default value that might not be in the table
                    //this is not so efficient
                    $generators[$k]['type'] = 'static'; //(kind of hackish to change the type here but works for now)
                } else {
                    $gen = Kwf_Component_Generator_Abstract
                        ::getInstance($g['class'], $g['key'], array(), $g['pluginBaseComponentClass']);
                    foreach ($gen->getChildData(null, clone $select) as $d) {
                        $ret[] = $d;
                        if ($select->hasPart('limitCount') && $select->getPart('limitCount') <= count($ret)) {
                            return $ret;
                        }
                    }

                }
            }
        }

        $staticGeneratorComponentClasses = array();
        foreach ($generators as $k=>$g) {
            if ($g['type'] == 'static') {
                if ($g['pluginBaseComponentClass']) {
                    $staticGeneratorComponentClasses[] = $g['pluginBaseComponentClass'];
                } else {
                    $staticGeneratorComponentClasses[] = $g['class'];
                }
            }
        }

        if ($staticGeneratorComponentClasses) {
            $pdSelect = array(
                'componentClasses' => $staticGeneratorComponentClasses
            );
            if ($select->hasPart('ignoreVisible')) {
                $pdSelect['ignoreVisible'] = $select->getPart('ignoreVisible');
            }
            $pd = $this->getRecursiveChildComponents($pdSelect, $childSelect);
            foreach ($generators as $k=>$g) {
                if ($g['type'] == 'static') {
                    $parentDatas = array();
                    foreach ($pd as $d) {
                        if ($d->componentClass == $g['class'] || $d->componentClass == $g['pluginBaseComponentClass']) {
                            $parentDatas[] = $d;
                        }
                    }
                    if ($parentDatas) {
                        $gen = Kwf_Component_Generator_Abstract
                                ::getInstance($g['class'], $g['key'], array(), $g['pluginBaseComponentClass']);
                        foreach ($gen->getChildData($parentDatas, $select) as $d) {
                            if (!in_array($d, $ret, true)) {
                                $ret[] = $d;
                                if ($select->hasPart('limitCount') && $select->getPart('limitCount') <= count($ret)) {
                                    return $ret;
                                }
                            }
                        }
                    }
                }
            }
        }
        return $ret;
    }

    private function _getRecursiveGenerators($componentClasses, $select, $childSelect, $selectHash)
    {
        $cacheId = Implode('-', $componentClasses).$selectHash;
        if (isset($this->_recursiveGeneratorsCache[$cacheId])) {
            return $this->_recursiveGeneratorsCache[$cacheId];
        }

        $ret = array();
        $this->_recursiveGeneratorsCache[$cacheId] = array();
        foreach ($componentClasses as $componentClass) {
            if (!$componentClass) continue;
            foreach (Kwf_Component_Generator_Abstract::getInstances($componentClass, $select) as $generator) {
                if ($generator->getChildComponentClasses($select)) {
                    if ($generator->getGeneratorFlag('static')) {
                        if ($generator instanceof Kwc_Abstract_Cards_Generator) {
                            $type = 'cards';
                        } else {
                            $type = 'static';
                        }
                    } else {
                        $type = 'notStatic';
                    }
                    $ret[] = array(
                        'type' => $type,
                        'class' => $generator->getClass(),
                        'pluginBaseComponentClass' => $generator->getPluginBaseComponentClass(),
                        'key' => $generator->getGeneratorKey()
                    );
                }
            }
        }
        foreach ($componentClasses as $componentClass) {
            if (!$componentClass) continue;
            foreach (Kwf_Component_Generator_Abstract::getInstances($componentClass, $childSelect) as $generator) {
                $g = $this->_getRecursiveGenerators(
                                    $generator->getChildComponentClasses(),
                                    $select, $childSelect, $selectHash);
                foreach ($g as $i) {
                    foreach ($ret as $j) {
                        if ($j['class'] == $i['class']
                                && $j['key'] == $i['key']
                                && $j['pluginBaseComponentClass'] == $i['pluginBaseComponentClass']) {
                            continue 2;
                        }
                    }
                    $ret[] = $i;
                }
            }
        }
        $this->_recursiveGeneratorsCache[$cacheId] = $ret;
        return $ret;
    }

    private function _formatChildConstraints($select, $childSelect)
    {
        $childSelect = clone $childSelect;

        $select = clone $select;
        if ($select->hasPart(Kwf_Model_Select::LIMIT_COUNT)) {
            $select->unsetPart(Kwf_Model_Select::LIMIT_COUNT);
        }
        if ($select->hasPart(Kwf_Component_Select::WHERE_FILENAME)) {
            $select->unsetPart(Kwf_Component_Select::WHERE_FILENAME);
        }
        if ($select->hasPart(Kwf_Component_Select::WHERE_HOME)) {
            $select->unsetPart(Kwf_Component_Select::WHERE_HOME);
        }
        $classes = Kwc_Abstract::getIndirectChildComponentClasses($this->componentClass, $select);
        $page = $this;
        $ic = $this->inheritClasses;
        foreach ($ic as $c) {
            $classes = array_merge($classes,
                Kwc_Abstract::getIndirectChildComponentClasses($c, $select)
            );
        }
        // Nur bei hasEditComponents, Root soll keine Domain-Komponenten anzeigen
        // Hack-Alarm :D
        if ($select->hasPart(Kwf_Component_Select::WHERE_HAS_EDIT_COMPONENTS) &&
            $this instanceof Kwf_Component_Data_Root
        ) {
            $cc = array();
            foreach ($classes as $class) {
                if (!is_instance_of($class, 'Kwc_Root_DomainRoot_Domain_Component')) {
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
            $select = new Kwf_Component_Select($select);
        }
        return $select;
    }
    public function countChildComponents($select = array())
    {
        Kwf_Benchmark::count('countChildComponents');

        $select = $this->_formatSelect($select);

        if (!$select->hasPart(Kwf_Component_Select::WHERE_GENERATOR)) {
            throw new Kwf_Exception("You can count only for one generator at a time");
        }
        $generators = Kwf_Component_Generator_Abstract::getInstances($this->componentClass, $select);
        return current($generators)->countChildData($this, $select);
    }

    public function getChildIds($select = array())
    {
        $select = $this->_formatSelect($select);
        if (!$select->hasPart(Kwf_Component_Select::WHERE_GENERATOR)) {
            throw new Kwf_Exception('Only one generator supported, please restrict select to a generator');
        }
        $generator = current(Kwf_Component_Generator_Abstract::getInstances($this, $select));
        return $generator->getChildIds($this, $select);
    }

    public function getChildComponents($select = array())
    {
        $select = $this->_formatSelect($select);
        $sc = $select->getHash();
        if (isset($this->_childComponentsCache[$sc])) {
            Kwf_Benchmark::count('getChildComponents cached');
        } else {
            Kwf_Benchmark::count('getChildComponents uncached');
        }

        if (!isset($this->_childComponentsCache[$sc])) {

            $this->_childComponentsCache[$sc] = array();

            if ($select->getPart(Kwf_Component_Select::WHERE_COMPONENT_CLASSES) === array()) {
                return $this->_childComponentsCache[$sc]; //vorzeitig abbrechen, da kommt sicher kein ergebnis
            }

            if ($select->hasPart(Kwf_Component_Select::LIMIT_COUNT)) {
                $limitCount = $select->getPart(Kwf_Component_Select::LIMIT_COUNT);
            } else {
                $limitCount = null;
            }

            $generators = Kwf_Component_Generator_Abstract::getOwnInstances($this, $select);
            $ret = $this->_getChildComponentsFromGenerators($generators, $select, $limitCount);

            if (is_null($limitCount) || count($ret) < $limitCount) { //wenn limit nicht erreicht, inherited generator auch noch fragen
                if (!is_null($limitCount)) $limitCount -= count($ret);
                $generators = Kwf_Component_Generator_Abstract::getInheritedInstances($this, $select);
                $ret += $this->_getChildComponentsFromGenerators($generators, $select, $limitCount); //kein array_merge, da wuerden die keys verloren gehen - und die sind eh eindeutig
            }

            $this->_childComponentsCache[$sc] = $ret;
        }
        return $this->_childComponentsCache[$sc];
    }

    private function _getChildComponentsFromGenerators($generators, $select, $limitCount)
    {
        $ret = array();
        foreach ($generators as $generator) {
            $generatorSelect = clone $select;
            if ($limitCount) {
                $generatorSelect->limit($limitCount - count($ret));
            }
            $genId = $generator->getClass().$generator->getGeneratorKey();
            $parentData = $this;
            if (isset($this->_uniqueParentDatas[$genId])) {
                $parentData = $this->_uniqueParentDatas[$genId];
            }
            foreach ($generator->getChildData($parentData, $generatorSelect) as $data) {
                if (isset($ret[$data->componentId])) {
                    throw new Kwf_Exception("Id not unique: {$data->componentId}");
                }
                $ret[$data->componentId] = $data;

                if ($limitCount) {
                    if ($limitCount - count($ret) <= 0) {
                        break 2;
                    }
                }
            }
        }
        return $ret;
    }

    public function getChildPages($select = array(), $childSelect = array('page'=>false))
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $select->wherePage(true);
        return $this->getRecursiveChildComponents($select, $childSelect);
    }

    public function getChildPseudoPages($select = array(), $childSelect = array('page'=>false))
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $select->wherePseudoPage(true);
        return $this->getRecursiveChildComponents($select, $childSelect);
    }

    public function getChildBoxes($select = array())
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $select->whereBox(true);
        return $this->getRecursiveChildComponents($select);
    }

    public function getChildMultiBoxes($select = array())
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
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
            $select = new Kwf_Component_Select($select);
        }
        $select->limit(1);
        return current($this->getChildPages($select, $childSelect));
    }

    public function getChildPseudoPage($select = array())
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }
        $select->limit(1);
        $ret = $this->getChildPseudoPages($select);
        if (!$ret) return null;
        return current($ret);
    }

    public function getGenerator($key)
    {
        return Kwf_Component_Generator_Abstract::getInstance($this->componentClass, $key);
    }

    public function getChildComponentIds($constraints = array())
    {
        $ret = array();
        foreach ($this->getChildComponents($constraints) as $data) {
            $ret[] = $data->componentId;
        }
        return $ret;
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getChildComponent($select = array())
    {
        $select = $this->_formatSelect($select);
        $select->limit(1);
        $cc = $this->getChildComponents($select);
        if (!$cc) return null;
        return current($cc);
    }


    /**
     * @return Kwf_Component_Data
     */
    public function getRecursiveChildComponent($select = array(), $childSelect = array('page'=>false))
    {
        $select = $this->_formatSelect($select);
        $select->limit(1);
        $cc = $this->getRecursiveChildComponents($select);
        if (!$cc) return null;
        return current($cc);
    }

    /**
     * @return Kwc_Abstract
     */
    public function getComponent()
    {
        if (!isset($this->_component)) {
            $class = $this->componentClass;
            $class = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
            $component = new $class($this);
            $this->_component = $component;
        }
        return $this->_component;
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getPage()
    {
        $page = $this;
        if ($this->isPage) return $this;
        if (isset($this->_lazyParent)) {
            //optimierung: hier koennen eventuell ein paar nicht-pages uebersprungen werden
            $id = $this->_lazyParent;
            if (is_numeric($id) || strpos($id, '-') === false) {
                //ist eine page
            } else {
                if (strpos($id, '_') === false) {
                    $id = substr($id, 0, strpos($id, '-'));
                    if (!is_numeric($id)) {
                        return null;
                    }
                } else {
                    $underScorePos = strrpos($id, '_');
                    $hyphenPos = strpos($id, '-', $underScorePos);
                    if ($hyphenPos > $underScorePos) {
                        $id = substr($id, 0, $hyphenPos);
                    }
                }
            }
            return Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
        }
        while ($page && !$page->isPage) {
            $page = $page->parent;
        }
        return $page;
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getPageOrRoot()
    {
        $page = $this;
        while ($page && !$page->isPage) {
            if ($page instanceof Kwf_Component_Data_Root) return $page;
            $page = $page->parent;
        }
        return $page;
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getPseudoPageOrRoot()
    {
        $page = $this;
        while ($page && !$page->isPseudoPage) {
            if ($page instanceof Kwf_Component_Data_Root) return $page;
            $page = $page->parent;
        }
        return $page;
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getPseudoPage()
    {
        $page = $this;
        while ($page && !$page->isPseudoPage) {
            $page = $page->parent;
        }
        return $page;
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getParentPage()
    {
        $page = $this->getPage();
        if ($page && $page->parent) {
            return $page->parent->getPage();
        }
        return null;
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getParentPageOrRoot()
    {
        $page = $this->getPageOrRoot();
        if ($page->parent) {
            return $page->parent->getPageOrRoot();
        }
        return $page;
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getParentPseudoPage()
    {
        $page = $this->getPseudoPage();
        if ($page && $page->parent) {
            return $page->parent->getPseudoPage();
        }
        return null;
    }


    /**
     * @return Kwf_Component_Data
     */
    public function getParentPseudoPageOrRoot()
    {
        $page = $this->getPseudoPage();
        if ($page && $page->parent) {
            return $page->parent->getPseudoPageOrRoot();
        }
        return null;
    }

    /**
     * @param string|array
     * @return Kwf_Component_Data
     */
    public function getParentByClass($cls)
    {
        if (!is_array($cls)) $cls = array($cls);
        $d = $this;
        while ($d) {
            foreach ($cls as $i) {
                if (is_instance_of($d->componentClass, $i)) {
                    return $d;
                }
            }
            $d = $d->parent;
        }
        return $d;
    }

    public function getParentComponent($numParent = 1)
    {
        if (isset($this->_lazyParent)) {
            $id = $this->_lazyParent;
            for ($i=0;$i<$numParent;$i++) {
                $pos = max(strrpos($id, '_'), strrpos($id, '-'));
                if ($pos) {
                    $id = substr($id, 0, $pos);
                } else {
                    $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
                    for ($j=0;$j<$numParent-$i-1;$j++) {
                        $c = $c->parent;
                    }
                    return $c;
                }
            }
            return Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
        }
        $c = $this;
        for ($i=0;$i<$numParent;$i++) {
            $c = $c->parent;
        }
        return $c;
    }

    public function getParentComponentId($numParent = 1)
    {
        if (isset($this->_lazyParent)) {
            $id = $this->_lazyParent;
            for ($i=0;$i<$numParent;$i++) {
                $pos = max(strrpos($id, '_'), strrpos($id, '-'));
                if ($pos) {
                    $id = substr($id, 0, $pos);
                } else {
                    $c = Kwf_Component_Data_Root::getInstance()->getComponentById($id, array('ignoreVisible'=>true));
                    for ($j=0;$j<$numParent-$i-1;$j++) {
                        $c = $c->parent;
                    }
                    return $c->componentId;
                }
            }
            return $id;
        }
        $c = $this;
        for ($i=0;$i<$numParent;$i++) {
            $c = $c->parent;
        }
        return $c->componentId;
    }

    public function getTitle()
    {
        $title = array();
        $row = $this->getPage();
        if (!$row) {
            return null;
        }
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
        $plugins = Kwc_Abstract::getSetting($this->componentClass, 'plugins');
        foreach ($plugins as $p) {
            if (!$interface || is_instance_of($p, $interface)) {
                $ret[] = $p;
            }
        }
        return $ret;
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getChildPageByPath($path)
    {
        $page = $this;
        $pathParts = preg_split('#([/:])#', $path, -1, PREG_SPLIT_DELIM_CAPTURE);
        for($i=0; $i<count($pathParts); $i++) {
            $pathPart = $pathParts[$i];
            $i++;
            $nextSeparator = isset($pathParts[$i]) ? $pathParts[$i] : '/';
            if ($nextSeparator == '/') {
                $pages = $page->getRecursiveChildComponents(array(
                                'filename' => $pathPart,
                                'pseudoPage'=>true,
                                'limit'=>1),
                            array('pseudoPage'=>false));
            } else {
                // if path is separated by ':', id comes without '-', search for child-component
                $pages = $page->getRecursiveChildComponents(array(
                                'id' => '-' . $pathPart,
                                'pseudoPage'=>false,
                                'limit'=>1),
                            array('pseudoPage'=>false));
            }
            $page = current($pages);
            if (!$page) break;
        }
        return $page;
    }

    /**
     * @return Kwf_Component_Data
     */
    public function getLanguageData()
    {
        // search parents for flag hasLanguage
        $c = $this;
        do {
            if (Kwc_Abstract::getFlag($c->componentClass, 'hasLanguage')) {
                break;
            }
        } while (($c = $c->parent));

        if (!$c) return null;
        return $c;
    }

    public function getLanguage()
    {
        if (!isset($this->_languageCache)) { //cache ist vorallem für bei kwfUnserialize nützlich
            $langData = $this->getLanguageData();
            if (!$langData) {
                $this->_languageCache = Kwf_Trl::getInstance()->getWebCodeLanguage();
            } else {
                $this->_languageCache = $langData->getComponent()->getLanguage();
            }
        }
        return $this->_languageCache;
    }

    /**
     * Returns if this component is visible
     *
     * A component is visible if all parents are visible.
     */
    public function isVisible()
    {
        $c = $this;
        while($c) {
            if (isset($c->invisible) && $c->invisible) {
                return false;
            }
            $c = $c->parent;
        }
        return true;
    }

    /**
     * Returns if this page should be shown in menus
     *
     * Category_Generators can change that dynamically using the hide column,
     * other generators have a 'showInMenu' setting (defaults to false)
     */
    public function isShownInMenu()
    {
        if (!$this->isPage) return false;
        if ($this->generator instanceof Kwc_Root_Category_Generator) {
            //it's not worth for this single special case to add a generator method, but should be done if other special cases are needed
            return !$this->row->hide;
        } else {
            if (!$this->generator->hasSetting('showInMenu')) return false;
            return (bool)$this->generator->getSetting('showInMenu');
        }
    }

    public function trlStaticExecute($trlStaticData)
    {
        return Kwf_Trl::getInstance()->trlStaticExecute($trlStaticData, $this->getLanguage());
    }

    public function trl($string, $text = array())
    {
        return Kwf_Trl::getInstance()->trl($string, $text, Kwf_Trl::SOURCE_WEB, $this->getLanguage());
    }

    public function trlc($context, $string, $text = array())
    {
        return Kwf_Trl::getInstance()->trlc($context, $string, $text, Kwf_Trl::SOURCE_WEB, $this->getLanguage());
    }

    public function trlp($single, $plural, $text =  array())
    {
        return Kwf_Trl::getInstance()->trlp($single, $plural, $text, Kwf_Trl::SOURCE_WEB, $this->getLanguage());
    }

    public function trlcp($context, $single, $plural, $text = array())
    {
        return Kwf_Trl::getInstance()->trlcp($context, $single, $plural, $text, Kwf_Trl::SOURCE_WEB, $this->getLanguage());
    }

    public function trlKwf($string, $text = array())
    {
        return Kwf_Trl::getInstance()->trl($string, $text, Kwf_Trl::SOURCE_KWF, $this->getLanguage());
    }

    public function trlcKwf($context, $string, $text = array())
    {
        return Kwf_Trl::getInstance()->trlc($context, $string, $text, Kwf_Trl::SOURCE_KWF, $this->getLanguage());
    }

    public function trlpKwf($single, $plural, $text =  array())
    {
        return Kwf_Trl::getInstance()->trlp($single, $plural, $text, Kwf_Trl::SOURCE_KWF, $this->getLanguage());
    }

    public function trlcpKwf($context, $single, $plural, $text = array())
    {
        return Kwf_Trl::getInstance()->trlcp($context, $single, $plural, $text, Kwf_Trl::SOURCE_KWF, $this->getLanguage());
    }

    public function toDebug()
    {
        return $this->componentId . ' (' . $this->componentClass . ')';
    }

    public function render($enableCache = null, $renderMaster = false)
    {
        $output = new Kwf_Component_Renderer();
        $output->setEnableCache($enableCache);
        if ($renderMaster) {
            return $output->renderMaster($this);
        } else {
            return $output->renderComponent($this);
        }
    }

    public function kwfSerialize()
    {
        $this->getLanguage(); //um _languageCache zu befüllen

        $ret = array();
        $ret['class'] = get_class($this);
        foreach (get_object_vars($this) as $k=>$v) {
            if ($k == '_component') continue;
            if ($k == '_inheritClasses') continue;
            if ($k == '_uniqueParentDatas') continue;
            if ($k == '_childComponentsCache') continue;
            if ($k == '_recursiveGeneratorsCache') continue;
            if ($k == 'generator') {
                $v = array($v->getClass(), $v->getGeneratorKey());
                $k = '_lazyGenerator';
            } else if ($k == 'row') {
                if ($v instanceof Kwf_Model_Row_Interface && $this->generator->getModel() !== $v->getModel()) {
                    throw new Kwf_Exception('data row has invalid model');
                }
                $v = $v->{$this->generator->getModel()->getPrimaryKey()};
                $k = '_lazyRow';
            } else if ($k == 'parent') {
                $v = $v->componentId;
                $k = '_lazyParent';
            } else if ($k == 'chained') {
                $v = $v->componentId;
                $k = '_lazyChained';
            }
            $ret[$k] = $v;
        }
        return $ret;
    }

    public static function kwfUnserialize($vars)
    {
        if ($ret = Kwf_Component_Data_Root::getInstance()->getFromDataCache($vars['componentId'])) {
            return $ret;
        }
        $cls = $vars['class'];
        unset($vars['class']);
        $vars['unserialized'] = true;
        $ret = new $cls($vars);
        Kwf_Component_Data_Root::getInstance()->addToDataCache($ret, new Kwf_Component_Select());
        //TODO: generator data-cache?
        return $ret;
    }

    /**
     * @internal
     */
    protected function _freeMemory()
    {
        if (isset($this->parent)) {
            $this->_lazyParent = $this->parent->componentId;
            unset($this->parent);
        }
        if (isset($this->_component)) {
            $this->_component->freeMemory();
            unset($this->_component);
        }
        //unset($this->generator);
        if (isset($this->row)) {
            if ($this->row instanceof Kwf_Model_Row_Interface && $this->generator->getModel() !== $this->row->getModel()) {
                throw new Kwf_Exception('data row has invalid model');
            }
            $this->_lazyRow = $this->row->{$this->generator->getModel()->getPrimaryKey()};
            unset($this->row);
        }
        if (isset($this->_uniqueParentDatas)) unset($this->_uniqueParentDatas);
        if (isset($this->_inheritClasses)) unset($this->_inheritClasses);
        $this->_childComponentsCache = array();
        $this->_recursiveGeneratorsCache = array();
        if (isset($this->_languageCache)) unset($this->_languageCache);
    }
}
