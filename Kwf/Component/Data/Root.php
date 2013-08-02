<?php
/**
 *
 *
 * @package Components
 */
class Kwf_Component_Data_Root extends Kwf_Component_Data
{
    private static $_instance;
    private static $_rootComponentClass;
    private static $_showInvisible;
    private $_hasChildComponentCache;
    private $_componentsByClassCache;
    private $_componentsByDbIdCache;
    private $_generatorsForClassesCache = array();
    private $_currentPage;
    private $_pageGenerators;

    //caches fuer getComponentById
    private $_dataCache = array();
    private $_dataCacheIgnoreVisible = array();

    /**
     * @internal
     */
    public function __construct($config = array())
    {
        $config = array_merge(array(
                'name' => 'Root',
                'parent' => null,
                'isPage' => false,
                'isPseudoPage' => false,
                'inherits' => true,
                'componentId' => 'root',
                'filename' => false
            ), $config
        );
        parent::__construct($config);
        $this->_inheritClasses = array();
        $this->_uniqueParentDatas = array();
    }

    /**
     * Returns the root component data instance
     *
     * @return Kwf_Component_Data_Root
     */
    public static function getInstance()
    {
        if (is_null(self::$_instance)) {
            $c = self::getComponentClass();
            if ($c) {
                self::$_instance = new self(array('componentClass' => $c));
            } else {
                self::$_instance = false;
            }
        }
        return self::$_instance;
    }

    /**
     * @internal
     */
    public static function getComponentClass()
    {
        if (is_null(self::$_rootComponentClass)) {
            if (Kwf_Config::getValue('kwc.rootComponent')) {
                self::$_rootComponentClass = Kwf_Config::getValue('kwc.rootComponent');
            } else {
                self::$_rootComponentClass = false;
            }
        }
        return self::$_rootComponentClass;
    }

    /**
     * @internal
     */
    public static function setComponentClass($componentClass)
    {
        self::$_rootComponentClass = $componentClass;
        self::reset();
    }

    /**
     * @internal
     */
    public static function setShowInvisible($showInvisible)
    {
        self::$_showInvisible = $showInvisible;
    }

    /**
     * Returns true if invisible items should be shown, true if app is opened in with preview param
     *
     * @return bool
     */
    public static function getShowInvisible()
    {
        return self::$_showInvisible;
    }

    /**
     * @internal
     */
    public static function reset($resetCache = true)
    {
        self::$_instance = null;
        Kwf_Component_Generator_Abstract::clearInstances();
        Kwf_Component_Abstract::clearModelInstances();
        Kwf_Component_Events::clearCache();
        if ($resetCache) Kwf_Component_Abstract::resetSettingsCache();
    }

    /**
     * Tries to clear all cached data objects and row objects
     *
     * Usful when processing lot of components to avoid memory issues
     */
    public function freeMemory()
    {
        $this->_freeMemory();
        foreach ($this->_dataCache as $id=>$c) {
            if (isset($this->_dataCacheIgnoreVisible[$id])) {
                unset($this->_dataCacheIgnoreVisible[$id]);
            }
            $c->_freeMemory();
        }
        foreach ($this->_dataCacheIgnoreVisible as $id=>$c) {
            $c->_freeMemory();
        }
        $this->_dataCache = array();
        $this->_dataCacheIgnoreVisible = array();
        $this->_componentsByClassCache = null;
        $this->_componentsByDbIdCache = null;
        $this->_generatorsForClassesCache = array();
        //Kwf_Component_Generator_Abstract::clearInstances();
        Kwf_Model_Abstract::clearAllRows();

        if (function_exists('gc_collect_cycles')) gc_collect_cycles();
    }

    /**
     * @internal
     */
    public function __get($var)
    {
        if ($var == 'filename') {
            //hier ohne rawurlencode, ist bei tetss auf 'kwf/kwctest/...' gesetzt
            return $this->_filename;
        }
        return parent::__get($var);
    }

    /**
     * Returns data by fully qualified url
     *
     * @param string url including http:// and domain
     * @param string acceptLanguage as sent by browser (can be null if none was sent)
     * @param bool will be set to true if the url exactly matches the data url (and no redirect to the correcty url is needed)
     */
    public function getExpandedComponentId()
    {
        return $this->componentId;
    }

    /*
     * @return Kwf_Component_Data
     */
    public function getPageByUrl($url, $acceptLanguage, &$exactMatch = true)
    {
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['path'])) return null;
        if (!isset($parsedUrl['host'])) {
            throw new Kwf_Exception("Host is missing in url '$url'");
        }
        if (substr($parsedUrl['host'], 0, 4) == 'dev.') {
            $parsedUrl['host'] = 'www.'.substr($parsedUrl['host'], 4);
        }
        $cacheUrl = $parsedUrl['host'].$parsedUrl['path'];
        $cacheId = 'url-'.$cacheUrl;
        if ($page = Kwf_Cache_Simple::fetch($cacheId)) {
            $exactMatch = true;
            $ret = Kwf_Component_Data::kwfUnserialize($page);
        } else {
            $path = $this->getComponent()->formatPath($parsedUrl);
            if (is_null($path)) return null;
            $urlPrefix = Kwf_Config::getValue('kwc.urlPrefix');
            if ($urlPrefix) {
                if (substr($path, 0, strlen($urlPrefix)) != $urlPrefix) {
                    return null;
                } else {
                    $path = substr($path, strlen($urlPrefix));
                }
            }
            $path = trim($path, '/');
            $ret = $this->getComponent()->getPageByUrl($path, $acceptLanguage);
            if ($ret && rawurldecode($ret->url) == $parsedUrl['path']) { //nur cachen wenn kein redirect gemacht wird
                $exactMatch = true;
                if ($ret->isVisible()) {
                    Kwf_Cache_Simple::add($cacheId, $ret->kwfSerialize());

                    Kwf_Component_Cache::getInstance()->getModel('url')->import(Kwf_Model_Abstract::FORMAT_ARRAY,
                        array(array(
                            'url' => $cacheUrl,
                            'page_id' => $ret->componentId,
                            'expanded_page_id' => $ret->getExpandedComponentId()
                        )), array('replace'=>true, 'skipModelObserver'=>true));
                }
            } else {
                $exactMatch = false;
            }
        }
        return $ret;
    }

    /**
     * Returns a component data by it's componentId
     *
     * @param string componentId
     * @param array|Kwf_Component_Select additional contraint
     * @return Kwf_Component_Data
     */
    public function getComponentById($componentId, $select = array())
    {
        if (!$componentId) return null;

        if (is_array($select)) {
            $partTypes = array_keys($select);
        } else {
            $partTypes = $select->getPartTypes();
        }
        if (!$partTypes || $partTypes == array(Kwf_Component_Select::IGNORE_VISIBLE)) {
            if (isset($this->_dataCache[$componentId])) {
                return $this->_dataCache[$componentId];
            }
            if (is_array($select)) {
                if (isset($select[Kwf_Component_Select::IGNORE_VISIBLE])) {
                    $ignoreVisible = $select[Kwf_Component_Select::IGNORE_VISIBLE];
                } else {
                    $ignoreVisible = false;
                }
            } else {
                $ignoreVisible = $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE);
            }
            if ($ignoreVisible && isset($this->_dataCacheIgnoreVisible[$componentId])) {
                return $this->_dataCacheIgnoreVisible[$componentId];
            }
        }
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $ret = $this;
        $idParts = $this->_getIdParts($componentId);

        //Optimierung: wenn bereits ein parent der gesuchten komponente existiert, dieses direkt verwenden
        //hilft vorallem wenn das parent deserialisiert wurde da in dem fall die weiteren parents nicht erstellt werden müssen
        for($i=0; $i<count($idParts); ++$i) {
            $id = implode('', array_slice($idParts, 0, count($idParts)-$i-1));
            $found = false;
            if (isset($this->_dataCache[$id])) {
                $ret = $this->_dataCache[$id];
                $found = true;
            } else {
                if (is_array($select)) {
                    if (isset($select[Kwf_Component_Select::IGNORE_VISIBLE])) {
                        $ignoreVisible = $select[Kwf_Component_Select::IGNORE_VISIBLE];
                    } else {
                        $ignoreVisible = false;
                    }
                } else {
                    $ignoreVisible = $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE);
                }
                if ($ignoreVisible && isset($this->_dataCacheIgnoreVisible[$id])) {
                    $ret = $this->_dataCacheIgnoreVisible[$id];
                    $found = true;
                }
            }
            if ($found) {
                $idParts = array_slice($idParts, count($idParts)-$i-1);
                break;
            }
        }

        foreach ($idParts as $i=>$idPart) {
            if ($idPart == 'root') {
                $ret = $this;
            } else {
                if ($i+1 == count($idParts)) {
                    //nur bei letzem part select berücksichtigen
                    $select->whereId($idPart);
                    $s = $select;
                } else {
                    $s = array('id'=>$idPart);
                    if ($select->hasPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
                        //ignoreVisible doch mitnehmen damit wir unterkomponeten von unsichtbaren
                        //komponenten finden
                        $s['ignoreVisible'] = $select->getPart(Kwf_Component_Select::IGNORE_VISIBLE);
                    }
                    if ($select->hasPart(Kwf_Component_Select::WHERE_SUBROOT)) {
                        //ignoreVisible doch mitnehmen damit wir unterkomponeten von unsichtbaren
                        //komponenten finden
                        $s['subroot'] = $select->getPart(Kwf_Component_Select::WHERE_SUBROOT);
                    }
                    $s = new Kwf_Component_Select($s);
                }
                if ($i == 0 && !$found) { // Muss eine Page sein
                    $ret = null;
                    $generators = $this->getPageGenerators();
                    foreach ($generators as $generator) {
                        $ret = array_pop($generator->getChildData(null, $s));
                        if ($ret) break;
                    }
                } else {
                    $ret = $ret->getChildComponent($s);
                }
                if (!$ret) break;
            }
        }
        return $ret;
    }

    /**
     * Returns all Kwc_Root_Category_Generators used.
     *
     * the name of this method is a bit missleading
     *
     * @return Kwc_Root_Category_Generator[]
     */
    public function getPageGenerators()
    {
        if (!is_null($this->_pageGenerators)) return $this->_pageGenerators;

        $cacheId = $this->componentClass . '_pageGenerators';

        $generators = Kwf_Cache_SimpleStatic::fetch($cacheId);
        if (!$generators) {
            $generators = array();
            foreach (Kwc_Abstract::getComponentClasses() as $class) {
                foreach (Kwc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                    if (!isset($generator['class'])) {
                        throw new Kwf_Exception("no generator class set for generator '$key' in component '$class'");
                    }
                    if (is_instance_of($generator['class'], 'Kwc_Root_Category_Generator')) {
                        $generators[] = array('class' => $class, 'key' => $key, 'generator' => $generator);
                    }
                }
            }
            Kwf_Cache_SimpleStatic::add($cacheId, $generators);
        }

        $this->_pageGenerators = array();
        foreach ($generators as $g) {
            $this->_pageGenerators[] = Kwf_Component_Generator_Abstract::getInstance(
                $g['class'], $g['key'], $g['generator']
            );
        }
        return $this->_pageGenerators;
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

    /**
     * Searches for a component data by it's dbId
     *
     * As multiple can have the same dbId you will get an exception if mutiple are found.
     * To avoid that pass 'limit'=>1 as select
     *
     * @param string
     * @param array|Kwf_Component_Select
     * @return Kwf_Component_Data
     */
    public function getComponentByDbId($dbId, $select = array())
    {
        $components = $this->getComponentsByDbId($dbId, $select);
        $this->_checkSingleComponent($components);
        if (isset($components[0])) {
            return $components[0];
        }
        return null;
    }

    /**
     * Searches for component datas by it's dbId
     *
     * @param string
     * @param array|Kwf_Component_Select
     * @return Kwf_Component_Data[]
     */
    public function getComponentsByDbId($dbId, $select = array())
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }

        $cacheId = $dbId.$select->getHash();
        if (!isset($this->_componentsByDbIdCache[$cacheId])) {

            if (is_numeric(substr($dbId, 0, 1)) || substr($dbId, 0, 4)=='root') {
                $data = $this->getComponentById($dbId, $select);
                if ($data) {
                    return array($data);
                } else {
                    return array();
                }
            }

            if ($select->hasPart(Kwf_Component_Select::LIMIT_COUNT)) {
                $limitCount = $select->getPart(Kwf_Component_Select::LIMIT_COUNT);
            }
            $ret = array();
            foreach (Kwc_Abstract::getComponentClasses() as $class) {
                foreach (Kwc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                    if (isset($generator['dbIdShortcut'])
                            && substr($dbId, 0, strlen($generator['dbIdShortcut'])) == $generator['dbIdShortcut']) {
                        $idParts = $this->_getIdParts(substr($dbId, strlen($generator['dbIdShortcut']) - 1));
                        $generator = Kwf_Component_Generator_Abstract::getInstance($class, $key);
                        if (count($idParts) <= 1) {
                            $generatorSelect = clone $select;
                        } else {
                            $generatorSelect = new Kwf_Component_Select(); // Select erst bei letzten Part
                            if ($select->hasPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
                                $generatorSelect->ignoreVisible($select->getPart(Kwf_Component_Select::IGNORE_VISIBLE));
                            }
                            if ($select->hasPart(Kwf_Component_Select::WHERE_SUBROOT)) {
                                $generatorSelect->whereSubroot($select->getPart(Kwf_Component_Select::WHERE_SUBROOT));
                            }
                        }
                        if (isset($limitCount)) {
                            $generatorSelect->limit($limitCount - count($ret));
                        }
                        $generatorSelect->whereId($idParts[0]);
                        $data = $generator->getChildData(null, $generatorSelect);
                        unset($idParts[0]);
                        foreach ($data as $d) {
                            $componentId = $d->componentId . implode('', $idParts);
                            $data = $this->getComponentById($componentId, $select);
                            if ($data) {
                                $ret[] = $data;
                            }
                            if (isset($limitCount) && $limitCount - count($ret) <= 0) {
                                break 3;
                            }
                        }
                    }
                }
            }
            $this->_componentsByDbIdCache[$cacheId] = $ret;
        }
        return $this->_componentsByDbIdCache[$cacheId];
    }

    /**
     * Returns all components matching a component class (including classes inheriting that class)
     *
     * Use with care, this is only efficient if a few components exist.
     *
     * @see getComponentsBySameClass
     * @param string|array component class
     * @param array|Kwf_Component_Select
     * @return Kwf_Component_Data[]
     */
    public function getComponentsByClass($class, $select = array())
    {
        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        }
        $cacheId = (is_array($class) ? implode(',', $class) : $class).$select->getHash();
        if (!isset($this->_componentsByClassCache[$cacheId])) {

            $lookingForChildClasses = Kwc_Abstract::getComponentClassesByParentClass($class);
            foreach ($lookingForChildClasses as $c) {
                if (is_instance_of($c, 'Kwc_Root_Abstract')) {
                    return array($this);
                }
            }
            $ret = $this->getComponentsBySameClass($lookingForChildClasses, $select);
            $this->_componentsByClassCache[$cacheId] = $ret;

        }
        return $this->_componentsByClassCache[$cacheId];
    }

    /**
     * Returns all components exactly matching a component class
     *
     * Use with care, this is only efficient if a few components exist.
     *
     * @see getComponentsByClass
     * @param string|array component class
     * @param array|Kwf_Component_Select
     * @return Kwf_Component_Data[]
     */
    public function getComponentsBySameClass($lookingForChildClasses, $select = array())
    {
        if (!is_array($lookingForChildClasses) && $lookingForChildClasses == $this->componentClass) {
            return array($this);
        }

        if (!is_array($lookingForChildClasses)) {
            $lookingForChildClasses = array($lookingForChildClasses);
        }

        if (is_array($select)) {
            $select = new Kwf_Component_Select($select);
        } else {
            $select = clone $select;
        }
        $select->whereComponentClasses($lookingForChildClasses);

        if ($select->hasPart(Kwf_Component_Select::LIMIT_COUNT)) {
            $limitCount = $select->getPart(Kwf_Component_Select::LIMIT_COUNT);
        }

        $ret = array();
        foreach ($this->_getGeneratorsForClasses($lookingForChildClasses) as $generator) {
            foreach ($generator->getChildData(null, $select) as $data) {
                $ret[] = $data;
                if (isset($limitCount) && $limitCount - count($ret) <= 0) {
                    return $ret;
                }
            }
        }
        return $ret;
    }

    private function _getGeneratorsForClasses($lookingForClasses)
    {

        $cacheId = 'genForCls'.$this->getComponentClass().str_replace('.', '_', implode('', $lookingForClasses));
        if (isset($this->_generatorsForClassesCache[$cacheId])) {
        } else if (($generators = Kwf_Cache_SimpleStatic::fetch($cacheId)) !== false) {
            $ret = array();
            foreach ($generators as $g) {
                $ret[] = Kwf_Component_Generator_Abstract::getInstance($g[0], $g[1]);
            }
            $this->_generatorsForClassesCache[$cacheId] = $ret;
        } else {
            $generators = array();
            foreach (Kwc_Abstract::getComponentClasses() as $c) {
                foreach (Kwc_Abstract::getSetting($c, 'generators') as $key => $generator) {
                    if (is_array($generator['component'])) {
                        $childClasses = $generator['component'];
                    } else {
                        $childClasses = array($generator['component']);
                    }
                    foreach ($childClasses as $childClass) {
                        if (in_array($childClass, $lookingForClasses)) {
                            $generators[$c.$key] = array($c, $key);
                        }
                    }
                }
            }
            $generators = array_values($generators);
            Kwf_Cache_SimpleStatic::add($cacheId, $generators);
            $ret = array();
            foreach ($generators as $g) {
                $ret[] = Kwf_Component_Generator_Abstract::getInstance($g[0], $g[1]);
            }
            $this->_generatorsForClassesCache[$cacheId] = $ret;
        }
        return $this->_generatorsForClassesCache[$cacheId];
    }

    /**
     * Returns component by given component class
     *
     * If multiple are found you will get an exception,
     * To avoid that pass 'limit'=>1 as select
     *
     * @see getComponentsByClass
     * @param string component class
     * @param array|Kwf_Component_Select
     * @return Kwf_Component_Data
     */
    public function getComponentByClass($class, $select = array())
    {
        $components = $this->getComponentsByClass($class, $select);
        $this->_checkSingleComponent($components);
        if (isset($components[0])) {
            return $components[0];
        }
        return null;
    }

    /**
     * Returns component by given component class
     *
     * If multiple are found you will get an exception,
     * To avoid that pass 'limit'=>1 as select
     *
     * @see getComponentsBySameClass
     * @param string component class
     * @param array|Kwf_Component_Select
     * @return Kwf_Component_Data
     */
    public function getComponentBySameClass($class, $select = array())
    {
        $components = $this->getComponentsBySameClass($class, $select);
        $this->_checkSingleComponent($components);
        if (isset($components[0])) {
            return $components[0];
        }
        return null;
    }

    private function _checkSingleComponent($components)
    {
        if (count($components) > 1) {
            $ids = array();
            foreach ($components as $c) {
                $ids[] = $c->componentId;
            }
            $e = new Kwf_Exception('getComponentByXxx must not get more than one component but got these: ' . implode(', ', $ids));
            $e->logOrThrow();
        }
    }

    /**
     * @deprecated
     * @internal
     */
    public function setCurrentPage(Kwf_Component_Data $page)
    {
        $this->_currentPage = $page;
    }

    /**
     * @deprecated
     * @internal
     *
     * I will kick your ass if you use this
     */
    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    /**
     * @internal
     */
    public function setFilename($f)
    {
        $this->_filename = $f;
    }

    /**
     * @internal siehe Kwf_Component_Generator_Abstract
     *
     * für getComponentById
     */
    public function addToDataCache(Kwf_Component_Data $d, Kwf_Component_Select $select)
    {
        if ($select->getPart(Kwf_Component_Select::IGNORE_VISIBLE)) {
            $this->_dataCacheIgnoreVisible[$d->componentId] = $d;
        } else {
            $this->_dataCache[$d->componentId] = $d;
        }
    }

    /**
     * @internal siehe Kwf_Component_Generator_Abstract
     *
     * für Component_Data::kwfUnserialize
     */
    public function getFromDataCache($id)
    {
        if (isset($this->_dataCache[$id])) {
            return $this->_dataCache[$id];
        } else if (isset($this->_dataCacheIgnoreVisible[$id])) {
            return $this->_dataCacheIgnoreVisible[$id];
        }
        return null;
    }
}
?>
