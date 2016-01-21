<?php
/**
 * @package Components
 *
 * @property string $componentId unique componentId of this data
 * @property string $dbId dbId of this data
 * @property string $componentClass component class of this data
 * @property int $id id of this data, excluding parants
 * @property Kwf_Comonent_Generator_Abstract $generator generator that created this data
 * @property Kwf_Component_Data $parent parent data
 * @property string $url url pointing to the page of this data, generated from filename property of parents
 * @property string $rel rel attribute when url is used in an a-tag
 * @property string $filename (only if page): the filename of this data, used to generate urls
 * @property Kwf_Model_Row_Interface $row only if created by table generator: row assigned to this data
 * @property bool $isPage true if this data represents a page
 * @property bool $isPseudopage true if this data represents a page
 */
class Kwf_Component_Data
{
    const DEVICE_VISIBLE_ALL = 'all';
    const DEVICE_VISIBLE_HIDE_ON_MOBILE = 'hideOnMobile';
    const DEVICE_VISIBLE_ONLY_SHOW_ON_MOBILE = 'onlyShowOnMobile';
    /**
     * @var Kwc_Abstract
     */
    private $_component;

    private $_url;
    private $_rel;

    /**
     * @internal
     */
    protected $_filename;

    /**
     * @internal
     */
    protected $_inheritClasses;

    /**
     * @internal
     */
    protected $_uniqueParentDatas;

    private $_childComponentsCache = array();
    private $_recursiveGeneratorsCache = array();
    private $_languageCache;
    private $_expandedComponentIdCache;
    private $_serializedBaseProperties = array(
        'preLogin' => null
    );

    //public static $objectsCount;
    //public static $objectsById = array();

    /**
     * @internal
     */
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

            if ($hadStaticPage && isset($data->generator) &&
                !$data->isPseudoPage && $data->generator->getAddUrlPart()
            ) {
                $filename = $data->id.($filename ? ':' : '').$filename;
            }
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
            }
        } while ($data = $data->parent);

        $baseUrl = Kwf_Setup::getBaseUrl(); //TODO baseUrl vs. root filename: both do the same
        return ($baseUrl ? $baseUrl : '').'/'.$filename;
    }

    /**
     * Returns component_id with seperate entries from every page in tree
     *
     * @example
     * root
     *   |-1
     *     |-2
     *       |-3
     * componentId: 3, expandedComponentId: root-1_2_3
     *
     * @return string
     */
    public function getExpandedComponentId()
    {
        if ($this->_expandedComponentIdCache) {
            return $this->_expandedComponentIdCache;
        }
        $generator = $this->generator;
        if ($generator instanceof Kwc_Root_Category_Generator) {
            $separator = '_';
        } else {
            $separator = $generator->getIdSeparator();
        }
        $this->_expandedComponentIdCache = $this->parent->getExpandedComponentId() .
            $separator . $this->id;

        return $this->_expandedComponentIdCache;
    }

    /**
     * Returns domain component for current component
     *
     * @return string
     */
    public function getDomainComponent()
    {
        $component = $this;
        while ($component) {
            if (Kwc_Abstract::getFlag($component->componentClass, 'hasBaseProperties') &&
                $component->getComponent()->getBaseProperty('domain'))
            {
                return $component;
            }
            $component = $component->parent;
        }
    }

    /**
     * Returns domain for current component
     *
     * @return string
     */
    public function getDomain()
    {
        return $this->getBaseProperty('domain');
    }

    /**
     * Returns absolute url including domain and protocol (http://)
     *
     * @return string
     */
    public function getAbsoluteUrl()
    {
        $https = Kwf_Util_Https::domainSupportsHttps($this->getDomain());
        $protocol = $https ? 'https' : 'http';
        return $protocol . '://'.$this->getDomain().$this->url;
    }

    /**
     * Returns preview url
     *
     * @return string
     */
    public function getPreviewUrl()
    {
        return Kwf_Setup::getBaseUrl().'/admin/component/preview/?url='.urlencode($this->getAbsoluteUrl().'?kwcPreview');
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
            $ret = $this->generator->getRowByLazyRow($this->_lazyRow, $this);
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

    /**
     * @internal
     */
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

    /**
     * @internal
     */
    public function __unset($var)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            throw new Kwf_Exception("Variable '$var' can't be modified for ".get_class($this));
        } else {
            throw new Kwf_Exception("Variable '$var' is not set for ".get_class($this));
        }
    }

    /**
     * @internal
     */
    public function __set($var, $value)
    {
        if ($var == 'url' || $var == 'rel' || $var == 'filename') {
            throw new Kwf_Exception("Variable '$var' can't be modified for ".get_class($this));
        } else {
            $this->$var = $value;
        }
    }

    /**
     * deprecated, could be removed
     * @internal
     */
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

    /**
     * Returns child components recursively
     *
     * This method usually is very efficient and tries to create as less data objects as possible.
     * It is still a complex operation thus should not get called too often.
     *
     * @param Kwf_Component_Select|array what to search for
     * @param Kwf_Component_Select|array how deep to search
     * @return array(Kwf_Component_Data)
     */
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
        $generators = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);

        if (!$success) {
            //get (statically=fast and cached) all generators that could create the component we are looking for
            $generators = $this->_getRecursiveGenerators(
                        Kwc_Abstract::getChildComponentClasses($this, $childSelect), //all classes $this could create that match $childSelect
                        $genSelect, $childSelect, $selectHash);
            Kwf_Cache_SimpleStatic::add($cacheId, $generators);
        }

        $noSubPages =
            $childSelect->hasPart('wherePage') && !$childSelect->getPart('wherePage') ||
            $childSelect->hasPart('wherePseudoPage') && !$childSelect->getPart('wherePseudoPage');
        if ($noSubPages) {
            $select->whereChildOf($this);
        } else {
            $select->whereSubroot($this);
        }

        foreach ($generators as $g) {
            if ($g['type'] == 'notStatic') {
                $gen = Kwf_Component_Generator_Abstract::getInstance($g['class'], $g['key']);
                $s = clone $select;
                if (!$noSubPages) {
                    //unset limit as we may have filter away results
                    $s->unsetPart('limitCount');
                }
                foreach ($gen->getChildData(null, $s) as $d) {
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
            $pdSelect = clone $childSelect;
            $pdSelect->whereComponentClasses($staticGeneratorComponentClasses);
            $pdSelect->copyParts(array('ignoreVisible'), $select);
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

    /**
     * returns the number of child components for a single generator
     *
     * Only works for a single generator - you have to specify the required generator.
     *
     * This is much more efficient than count(->getChildComponents()) as it will result in an
     * SELECT COUNT() sql query
     *
     * @param array|Kwf_Component_Select
     * @return int
     */
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

    /**
     * Returns child ids for a single generator
     *
     * Only works for a single generator - you have to specify the required generator.
     *
     * This is much more efficient than getChildComponents as no rows or data objects will be created.
     *
     * @param array|Kwf_Component_Select
     * @return int[]
     */
    public function getChildIds($select = array())
    {
        $select = $this->_formatSelect($select);
        if (!$select->hasPart(Kwf_Component_Select::WHERE_GENERATOR)) {
            throw new Kwf_Exception('Only one generator supported, please restrict select to a generator');
        }
        $generator = current(Kwf_Component_Generator_Abstract::getInstances($this, $select));
        return $generator->getChildIds($this, $select);
    }

    /**
     * Returns child components matching the given select
     *
     * @param array|Kwf_Component_Select
     * @return Kwf_Component_Data[]
     */
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

    /**
     * Returns child pages matching the given select
     *
     * Not only direct children will be returned, also pages created by child components.
     *
     * @param array|Kwf_Component_Select additional constraints
     * @param array|Kwf_Component_Select constraints on how deep indirect child pages will be returned
     * @return Kwf_Component_Data[]
     */
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

    /**
     * Returns child pseudo pages matching the given select
     *
     * Not only direct children will be returned, also pseudo pages created by child components.
     *
     * @param array|Kwf_Component_Select additional constraints
     * @param array|Kwf_Component_Select constraints on how deep indirect child pseudo pages will be returned
     * @return Kwf_Component_Data[]
     */
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

    /**
     * Returns child boxes matching the given select
     *
     * Not only direct children will be returned, also boxes created by child components.
     *
     * @param array|Kwf_Component_Select
     * @return Kwf_Component_Data[]
     */
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

    /**
     * Returns if the component has content
     *
     * Shortcut for $this->getComponent()->hasContent()
     *
     * @return bool
     */
    public function hasContent()
    {
        return $this->getComponent()->hasContent();
    }

    /**
     * Returns a single child page
     *
     * @see getChildPages
     * @return Kwf_Component_Data
     */
    public function getChildPage($select = array(), $childSelect = array('page'=>false))
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }
        $select->limit(1);
        return current($this->getChildPages($select, $childSelect));
    }

    /**
     * Returns a single child pseudo page
     *
     * @see getChildPseudoPages
     * @return Kwf_Component_Data
     */
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

    /**
     * Returns a generator of this data
     *
     * shortcut for Kwf_Component_Generator_Abstract::getInstance($data->componentClass, $key);
     *
     * @param string generator key
     * @return Kwf_Component_Generator_Abstract
     */
    public function getGenerator($key)
    {
        return Kwf_Component_Generator_Abstract::getInstance($this->componentClass, $key);
    }

    /**
     * Returns a single direct child component
     *
     * @see getChildComponents
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
     * Returns a single child component
     *
     * @see getRecursiveChildComponents
     * @return Kwf_Component_Data
     */
    public function getRecursiveChildComponent($select = array(), $childSelect = array('page'=>false))
    {
        $select = $this->_formatSelect($select);
        $select->limit(1);
        $cc = $this->getRecursiveChildComponents($select, $childSelect);
        if (!$cc) return null;
        return current($cc);
    }

    /**
     * Returns the Component object of to this data
     *
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
     * Returns the page this data belongs to (might be a page itself)
     *
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
     * Returns the page this data belongs to (might be a page itself) OR (if there is no page) the root component
     *
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
     * Returns the psuedo page or pagethis data belongs to (might be a page itself) OR (if there is no page) the root component
     *
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
     * Returns the pseudo page this data belongs to (might be a pseudo page itself)
     *
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
     * Returns the parent page of this data
     *
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
     * Returns the parent page OR (if there is no none) the root component
     *
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
     * Returns the parent pseudo page of this data
     *
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
     * Returns the parent pseudo page OR (if there is no none) the root component
     *
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
     * Returns the parent matching a given component class
     *
     * @param string|array component class or array of component classes
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

    /**
     * Returns a parent component from a given depth
     *
     * more efficient than getting ->parent multiple times (only if data was unserialized)
     *
     * @param int levels to go up
     * @return Kwf_Component_Data
     */
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

    /**
     * Returns a parent component id from a given depth
     *
     * more efficient than getting ->parent multiple times (only if data was unserialized)
     *
     * @param int levels to go up
     * @return Kwf_Component_Data
     */
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

    /**
     * Returns the page this data belongs to (might be a page itself) OR (if there is no page) the root component
     *
     * @return Kwf_Component_Data
     */
    public function getInheritsParent()
    {
        $page = $this;
        while ($page && !$page->inherits) {
            if ($page instanceof Kwf_Component_Data_Root) return $page;
            $page = $page->parent;
        }
        return $page;
    }

    /**
     * Returns the title of the page
     *
     * Can be overridden to customize.
     *
     * By default names of parent pages will be used
     *
     * @return string
     */
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

    /**
     * @internal
     */
    public function getPlugins($interface = null)
    {
        $ret = array();
        $plugins = Kwc_Abstract::getSetting($this->componentClass, 'plugins');
        foreach ($plugins as $p) {
            if (is_array($interface)) {
                foreach ($interface as $i) {
                    if (is_instance_of($p, $i)) {
                        $ret[] = $p;
                        break;
                    }
                }
            } else if (!$interface) {
                $ret[] = $p;
            } else if (is_instance_of($p, $interface)) {
                $ret[] = $p;
            }
        }
        return $ret;
    }

    /**
     * Searches for a child page by a given path
     *
     * Should only be used to resolve incoming paths
     *
     * @param string
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
     * Returns the language used by this data
     *
     * @return string
     */
    public function getLanguage()
    {
        if (!isset($this->_languageCache)) { //cache ist vorallem für bei kwfUnserialize nützlich
            $this->_languageCache = $this->getBaseProperty('language');
        }
        return $this->_languageCache;
    }

    /**
     * Retrieves a base Property for a component
     *
     * Any component can add a flag called 'hasBaseProperties' and implement
     * getBaseProperties($propertyName) to return a property specific for this component and all
     * child components (e.g. language, domain, id for analytics...)
     * It's also possible to specify the returned property by adding an array "baseProperties"
     * to the settings. This may help some to exclude components to be asked for base Properties
     * which they actually don't return.
     *
     * @param string $propertyName
     * @return string Property
     */
    public function getBaseProperty($propertyName)
    {
        $ret = null;
        if (isset($this->_serializedBaseProperties[$propertyName])) {
            $ret = $this->_serializedBaseProperties[$propertyName];
        }
        $c = $this;
        while (is_null($ret) && $c) {
            if (Kwc_Abstract::getFlag($c->componentClass, 'hasBaseProperties')) {
                $ret = $c->getComponent()->getBaseProperty($propertyName);
            }
            $c = $c->parent;
        }
        return $ret;
    }


    public function getSubroot()
    {
        $c = $this;
        while (true) {
            if (Kwc_Abstract::getFlag($c->componentClass, 'subroot')) {
                break;
            }
            if ($c->componentId == 'root') {
                break;
            }
            $c = $c->parent;
        }
        return $c;
    }

    /**
     * Returns if this component is visible
     *
     * A component is visible if all parents are visible.
     *
     * @return bool
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
     *
     * @return bool
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
        return Kwf_Trl::getInstance()->trl($string, $text, $this->getLanguage());
    }

    public function trlc($context, $string, $text = array())
    {
        return Kwf_Trl::getInstance()->trlc($context, $string, $text, $this->getLanguage());
    }

    public function trlp($single, $plural, $text =  array())
    {
        return Kwf_Trl::getInstance()->trlp($single, $plural, $text, $this->getLanguage());
    }

    public function trlcp($context, $single, $plural, $text = array())
    {
        return Kwf_Trl::getInstance()->trlcp($context, $single, $plural, $text, $this->getLanguage());
    }

    public function trlKwf($string, $text = array())
    {
        return Kwf_Trl::getInstance()->trlKwf($string, $text, $this->getLanguage());
    }

    public function trlcKwf($context, $string, $text = array())
    {
        return Kwf_Trl::getInstance()->trlcKwf($context, $string, $text, $this->getLanguage());
    }

    public function trlpKwf($single, $plural, $text =  array())
    {
        return Kwf_Trl::getInstance()->trlpKwf($single, $plural, $text, $this->getLanguage());
    }

    public function trlcpKwf($context, $single, $plural, $text = array())
    {
        return Kwf_Trl::getInstance()->trlcpKwf($context, $single, $plural, $text, $this->getLanguage());
    }

    /**
     * @internal
     */
    public function toDebug()
    {
        return $this->componentId . ' (' . $this->componentClass . ')';
    }

    /**
     * Render the component
     *
     * Usually only used internally or for debugging
     *
     * @param bool if view cache should be used, if null config setting will be used
     * @param bool if master should be rendered
     * @return string
     */
    public function render($enableCache = null, $renderMaster = false, &$hasDynamicParts = false)
    {
        $output = new Kwf_Component_Renderer();
        if ($enableCache !== null) $output->setEnableCache($enableCache);
        if ($renderMaster) {
            $hasDynamicParts = true;
            return $output->renderMaster($this);
        } else {
            return $output->renderComponent($this, $hasDynamicParts);
        }
    }

    /**
     * @internal
     */
    public function kwfSerialize()
    {
        $this->getLanguage(); //fill _languageCache
        $this->getExpandedComponentId(); //fill _expandedComponentIdCache

        foreach ($this->_serializedBaseProperties as $baseProperty => $value) {
            $this->_serializedBaseProperties[$baseProperty] = $this->getBaseProperty($baseProperty);
        }

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
                $v = $this->generator->getLazyRowByRow($v, $this);
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

    /**
     * @internal
     */
    public static function kwfUnserialize($vars)
    {
        if ($ret = Kwf_Component_Data_Root::getInstance()->getFromDataCache($vars['componentId'])) {
            return $ret;
        }
        $cls = $vars['class'];
        unset($vars['class']);
        $vars['unserialized'] = true;
        $ret = new $cls($vars);
        Kwf_Component_Data_Root::getInstance()->addToDataCache($ret, false);
        //TODO: generator data-cache?
        return $ret;
    }

    /**
     * @internal
     */
    protected function _freeMemory()
    {
        if (isset($this->parent) && $this->parent) {
            $this->_lazyParent = $this->parent->componentId;
            unset($this->parent);
        }
        if (isset($this->chained)) {
            $this->_lazyChained = $this->chained->componentId;
            unset($this->chained);
        }
        if (isset($this->_component)) {
            $this->_component->freeMemory();
            unset($this->_component);
        }
        //unset($this->generator);
        if (isset($this->row)) {
            $this->_lazyRow = $this->generator->getLazyRowByRow($this->row, $this);
            unset($this->row);
        }
        if (isset($this->_uniqueParentDatas)) unset($this->_uniqueParentDatas);
        if (isset($this->_inheritClasses)) unset($this->_inheritClasses);
        $this->_childComponentsCache = array();
        $this->_recursiveGeneratorsCache = array();
        if (isset($this->_languageCache)) unset($this->_languageCache);
    }

    /**
     * Returns on which devices this page should be visible
     *
     * DEVICE_VISIBLE_* constants are returned.
     * Implement getDeviceVisible in generator to change behaviour.
     */
    final public function getDeviceVisible()
    {
        return $this->generator->getDeviceVisible($this);
    }

    public function getLinkDataAttributes()
    {
        $ret = array();
        if ($this->isPage) {
            $contentSender = Kwc_Abstract::getSetting($this->componentClass, 'contentSender');
            if ($contentSender != 'Kwf_Component_Abstract_ContentSender_Default') { //skip for performance
                $contentSender = new $contentSender($this);
                $ret = $contentSender->getLinkDataAttributes();
            }
        }
        return $ret;

    }

    public function getLinkTitle()
    {
        return null;
    }
}
