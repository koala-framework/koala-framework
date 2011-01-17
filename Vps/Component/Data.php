<?php
class Vps_Component_Data
{
    private $_component;

    private $_url;
    private $_rel;
    protected $_filename;
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
    }

    /**
     * So wie ->url aber funktioniert auch fuer pseudoPages
     */
    protected function _getPseudoPageUrl()
    {
        $page = $this;
        if (!$page->isPseudoPage) {
            $page = $page->getParentPseudoPageOrRoot();
        }
        if (!$page) return '';
        $filenames = array();
        do {
            if (!empty($filenames) && Vpc_Abstract::getFlag($page->componentClass, 'shortcutUrl')) {
                $filenames[] = call_user_func(array($page->componentClass, 'getShortcutUrl'), $page->componentClass, $page);
                break;
            } else {
                if ($page->filename) $filenames[] = $page->filename;
            }
        } while ($page = $page->getParentPseudoPageOrRoot());
        $urlPrefix = Vps_Registry::get('config')->vpc->urlPrefix;
        return ($urlPrefix ? $urlPrefix : '').'/'.implode('/', array_reverse($filenames));
    }

    public function __get($var)
    {
        if ($var == 'url') {
            $filenames = array();
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
            if (/*$childs || */Vps_Component_Abstract::getFlag($this->getPage()->componentClass, 'noIndex')) {
                $rel .= ' nofollow';
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
                        foreach (Vpc_Abstract::getSetting($page->componentClass, 'generators') as $gKey=> $g) {
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
        Vps_Benchmark::count('getRecursiveChildComponents');
        if (is_array($childSelect)) {
            $childSelect = new Vps_Component_Select($childSelect);
        }
        $ret = $this->getChildComponents($select);
        if ($select->hasPart('limitCount') && $select->getPart('limitCount') <= count($ret)) {
            return $ret;
        }

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
            Vps_Component_Select::WHERE_GENERATOR,
            Vps_Component_Select::WHERE_HAS_EDIT_COMPONENTS,
            Vps_Component_Select::WHERE_INHERIT,
            Vps_Component_Select::WHERE_UNIQUE,
            Vps_Component_Select::WHERE_GENERATOR_CLASS,
            Vps_Component_Select::WHERE_COMPONENT_KEY,
        ), $select);

        $selectHash = md5($genSelect->getHash().$childSelect->getHash());
        $cacheId = 'recCCGen'.$selectHash.$this->componentClass.implode('__', $this->inheritClasses);
        $cacheId = str_replace('.', '_', $cacheId);
        if (isset($this->_recursiveGeneratorsCache[$cacheId])) {
            $generators = $this->_recursiveGeneratorsCache[$cacheId];
        } else if (($generators = $cache->load($cacheId)) !== false) {
            $this->_recursiveGeneratorsCache[$cacheId] = $generators;
        } else {
            $generators = $this->_getRecursiveGenerators(
                        Vpc_Abstract::getChildComponentClasses($this, $childSelect),
                        $genSelect, $childSelect, $selectHash);
            $this->_recursiveGeneratorsCache[$cacheId] = $generators;
            $cache->save($generators, $cacheId);
        }
        $noSubPages =
            $childSelect->hasPart('wherePage') && !$childSelect->getPart('wherePage') ||
            $childSelect->hasPart('wherePseudoPage') && !$childSelect->getPart('wherePseudoPage');
        if ($noSubPages) {
            $select->whereChildOfSamePage($this);
        }

        foreach ($generators as $g) {
            if (!$g['static']) {
                $gen = Vps_Component_Generator_Abstract::getInstance($g['class'], $g['key']);
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

        $staticGeneratorComponentClasses = array();
        foreach ($generators as $k=>$g) {
            if ($g['static']) {
                if ($g['pluginBaseComponentClass']) {
                    $staticGeneratorComponentClasses[] = $g['pluginBaseComponentClass'];
                } else {
                    $staticGeneratorComponentClasses[] = $g['class'];
                }
            }
        }

        if ($staticGeneratorComponentClasses) {
            $pd = $this->getRecursiveChildComponents(array(
                'componentClasses' => $staticGeneratorComponentClasses
            ), $childSelect);
            foreach ($generators as $k=>$g) {
                if ($g['static']) {
                    $parentDatas = array();
                    foreach ($pd as $d) {
                        if ($d->componentClass == $g['class'] || $d->componentClass == $g['pluginBaseComponentClass']) {
                            $parentDatas[] = $d;
                        }
                    }
                    if ($parentDatas) {
                        $gen = Vps_Component_Generator_Abstract
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
            foreach (Vps_Component_Generator_Abstract::getInstances($componentClass, $select) as $generator) {
                if ($generator->getChildComponentClasses($select)) {
                    $ret[] = array(
                        'static' => !!$generator->getGeneratorFlag('static'),
                        'class' => $generator->getClass(),
                        'pluginBaseComponentClass' => $generator->getPluginBaseComponentClass(),
                        'key' => $generator->getGeneratorKey()
                    );
                }
            }
        }
        foreach ($componentClasses as $componentClass) {
            if (!$componentClass) continue;
            foreach (Vps_Component_Generator_Abstract::getInstances($componentClass, $childSelect) as $generator) {
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
        // Hack-Alarm :D
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

    public function getChildIds($select = array())
    {
        $select = $this->_formatSelect($select);
        if (!$select->hasPart(Vps_Component_Select::WHERE_GENERATOR)) {
            throw new Vps_Exception('Only one generator supported, please restrict select to a generator');
        }
        $generator = current(Vps_Component_Generator_Abstract::getInstances($this, $select));
        return $generator->getChildIds($this, $select);
    }

    public function getChildComponents($select = array())
    {
        $select = $this->_formatSelect($select);
        $sc = $select->getHash();
        if (isset($this->_constraintsCache[$sc])) {
            Vps_Benchmark::count('getChildComponents cached');
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

    public function getChildPseudoPages($select = array(), $childSelect = array('page'=>false))
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $select->wherePseudoPage(true);
        return $this->getRecursiveChildComponents($select, $childSelect);
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

    /**
     * @return Vps_Component_Data
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
     * @return Vps_Component_Data
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
     * @return Vpc_Abstract
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
     * @return Vps_Component_Data
     */
    public function getPage()
    {
        $page = $this;
        while ($page && !$page->isPage) {
            $page = $page->parent;
        }
        return $page;
    }

    /**
     * @return Vps_Component_Data
     */
    public function getPageOrRoot()
    {
        $page = $this;
        while ($page && !$page->isPage) {
            if ($page instanceof Vps_Component_Data_Root) return $page;
            $page = $page->parent;
        }
        return $page;
    }

    /**
     * @return Vps_Component_Data
     */
    public function getPseudoPageOrRoot()
    {
        $page = $this;
        while ($page && !$page->isPseudoPage) {
            if ($page instanceof Vps_Component_Data_Root) return $page;
            $page = $page->parent;
        }
        return $page;
    }

    /**
     * @return Vps_Component_Data
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
     * @return Vps_Component_Data
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
     * @return Vps_Component_Data
     */
    public function getParentPageOrRoot()
    {
        $page = $this->getPageOrRoot();
        if ($page && $page->parent) {
            return $page->parent->getPageOrRoot();
        }
        return null;
    }

    /**
     * @return Vps_Component_Data
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
     * @return Vps_Component_Data
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
     * @return Vps_Component_Data
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
        $plugins = Vpc_Abstract::getSetting($this->componentClass, 'plugins');
        foreach ($plugins as $p) {
            if (!$interface || is_instance_of($p, $interface)) {
                $ret[] = $p;
            }
        }
        return $ret;
    }

    /**
     * @return Vps_Component_Data
     */
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

    /**
     * @return Vps_Component_Data
     */
    public function getLanguageData()
    {
        // search parents for flag hasLanguage
        $c = $this;
        do {
            if (Vpc_Abstract::getFlag($c->componentClass, 'hasLanguage')) {
                break;
            }
        } while (($c = $c->parent));

        if (!$c) return null;
        return $c;
    }

    public function getLanguage()
    {
        $langData = $this->getLanguageData();
        if (!$langData) {
            return Vps_Trl::getInstance()->getWebCodeLanguage();
        } else {
            return $langData->getComponent()->getLanguage();
        }
    }

    public function trlStaticExecute($trlStaticData)
    {
        return Vps_Trl::getInstance()->trlStaticExecute($trlStaticData, $this->getLanguage());
    }

    public function trl($string, $text = array())
    {
        return Vps_Trl::getInstance()->trl($string, $text, Vps_Trl::SOURCE_WEB, $this->getLanguage());
    }

    public function trlc($context, $string, $text = array())
    {
        return Vps_Trl::getInstance()->trlc($context, $string, $text, Vps_Trl::SOURCE_WEB, $this->getLanguage());
    }

    public function trlp($single, $plural, $text =  array())
    {
        return Vps_Trl::getInstance()->trlp($single, $plural, $text, Vps_Trl::SOURCE_WEB, $this->getLanguage());
    }

    public function trlcp($context, $single, $plural, $text = array())
    {
        return Vps_Trl::getInstance()->trlcp($context, $single, $plural, $text, Vps_Trl::SOURCE_WEB, $this->getLanguage());
    }

    public function trlVps($string, $text = array())
    {
        return Vps_Trl::getInstance()->trl($string, $text, Vps_Trl::SOURCE_VPS, $this->getLanguage());
    }

    public function trlcVps($context, $string, $text = array())
    {
        return Vps_Trl::getInstance()->trlc($context, $string, $text, Vps_Trl::SOURCE_VPS, $this->getLanguage());
    }

    public function trlpVps($single, $plural, $text =  array())
    {
        return Vps_Trl::getInstance()->trlp($single, $plural, $text, Vps_Trl::SOURCE_VPS, $this->getLanguage());
    }

    public function trlcpVps($context, $single, $plural, $text = array())
    {
        return Vps_Trl::getInstance()->trlcp($context, $single, $plural, $text, Vps_Trl::SOURCE_VPS, $this->getLanguage());
    }

    public function toDebug()
    {
        return $this->componentId . ' (' . $this->componentClass . ')';
    }

    // Nur zum Testen!
    public function render()
    {
        $output = new Vps_Component_Output_Cache();
        return $output->render($this);
    }
}
?>
