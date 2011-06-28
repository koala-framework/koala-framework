<?php
class Vps_Component_Data_Root extends Vps_Component_Data
{
    private static $_instance;
    private static $_rootComponentClass;
    private $_hasChildComponentCache;
    private $_componentsByClassCache;
    private $_componentsByDbIdCache;
    private $_generatorsForClassesCache = array();
    private $_currentPage;
    private $_pageGenerators;

    //caches fuer getComponentById
    private $_dataCache = array();
    private $_dataCacheIgnoreVisible = array();

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
     * @return Vps_Component_Data_Root
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

    public static function getComponentClass()
    {
        if (is_null(self::$_rootComponentClass)) {
            if (Vps_Registry::get('config')->vpc->rootComponent) {
                self::$_rootComponentClass = Vps_Registry::get('config')->vpc->rootComponent;
            } else {
                self::$_rootComponentClass = false;
            }
        }
        return self::$_rootComponentClass;
    }

    public static function setComponentClass($componentClass)
    {
        self::$_rootComponentClass = $componentClass;
        self::reset();
    }

    public static function reset($resetCache = true)
    {
        self::$_instance = null;
        Vps_Component_Generator_Abstract::clearInstances();
        Vps_Component_Abstract::clearModelInstances();
        if ($resetCache) Vps_Component_Abstract::resetSettingsCache();
    }

    public function __get($var)
    {
        if ($var == 'filename') {
            //hier ohne rawurlencode, ist bei tetss auf 'vps/vpctest/...' gesetzt
            return $this->_filename;
        }
        return parent::__get($var);
    }

    /**
     * @param string Die Uri incl Protokoll und Domain
     * @param string acceptLanguage falls verfügbar (kann null sein)
     * @param bool wird auf false gesetzt falls die url nicht exakt passte und ein redirekt auf die korrekte gemacht werden sollte
     * @return Vps_Component_Data
     */
    public function getPageByUrl($url, $acceptLangauge, &$exactMatch = true)
    {
        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['path'])) return null;
        if (!isset($parsedUrl['host'])) {
            throw new Vps_Exception("Host is missing in url '$url'");
        }
        if (substr($parsedUrl['host'], 0, 8) == 'preview.') {
            $parsedUrl['host'] = 'www.'.substr($parsedUrl['host'], 8);
        } else if (substr($parsedUrl['host'], 0, 4) == 'dev.') {
            $parsedUrl['host'] = 'www.'.substr($parsedUrl['host'], 4);
        }
        //TODO: acceptLanguage berücksichtigen?
        $cacheUrl = $parsedUrl['host'].$parsedUrl['path'];
        static $prefix;
        if (!isset($prefix)) $prefix = Vps_Cache::getUniquePrefix();
        $cacheId = $prefix.'url-'.$cacheUrl;
        if ($page = apc_fetch($cacheId)) {
            $exactMatch = true;
            $ret = Vps_Component_Data::vpsUnserialize($page);
        } else {
            $path = $this->getComponent()->formatPath($parsedUrl);
            if (is_null($path)) return null;
            $urlPrefix = Vps_Registry::get('config')->vpc->urlPrefix;
            if ($urlPrefix) {
                if (substr($path, 0, strlen($urlPrefix)) != $urlPrefix) {
                    return null;
                } else {
                    $path = substr($path, strlen($urlPrefix));
                }
            }
            $path = trim($path, '/');
            $ret = $this->getComponent()->getPageByUrl($path, $acceptLangauge);
            if ($ret && rawurldecode($ret->url) == $parsedUrl['path']) { //nur cachen wenn kein redirect gemacht wird
                $exactMatch = true;
                apc_add($cacheId, $ret->vpsSerialize());

                Vps_Component_Cache::getInstance()->getModel('url')->import(Vps_Model_Abstract::FORMAT_ARRAY,
                    array(array(
                        'url' => $cacheUrl,
                        'page_id' => $ret->componentId
                    )), array('replace'=>true));

                $m = Vps_Component_Cache::getInstance()->getModel('urlParents');
                $s = new Vps_Model_Select();
                $s->whereEquals('page_id', $ret->componentId);
                $m->deleteRows($s);

                $c = $ret;
                while($c = $c->parent) {
                    if (isset($c->generator) && $c->generator->getGeneratorFlag('table')) {
                        $m->import(Vps_Model_Abstract::FORMAT_ARRAY,
                            array(array(
                                'page_id' => $ret->componentId,
                                'parent_page_id' => $c->componentId
                            )), array('buffer'=>true));
                    }
                }
            }
        }
        return $ret;
    }

    /**
     * @return Vps_Component_Data
     */
    public function getComponentById($componentId, $select = array())
    {
        if (is_array($select)) {
            $partTypes = array_keys($select);
        } else {
            $partTypes = $select->getPartTypes();
        }
        if (!$partTypes || $partTypes == array(Vps_Component_Select::IGNORE_VISIBLE)) {
            if (isset($this->_dataCache[$componentId])) {
                return $this->_dataCache[$componentId];
            }
            if (is_array($select)) {
                if (isset($select[Vps_Component_Select::IGNORE_VISIBLE])) {
                    $ignoreVisible = $select[Vps_Component_Select::IGNORE_VISIBLE];
                } else {
                    $ignoreVisible = false;
                }
            } else {
                $ignoreVisible = $select->getPart(Vps_Component_Select::IGNORE_VISIBLE);
            }
            if ($ignoreVisible && isset($this->_dataCacheIgnoreVisible[$componentId])) {
                return $this->_dataCacheIgnoreVisible[$componentId];
            }
        }
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
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
                    if (isset($select[Vps_Component_Select::IGNORE_VISIBLE])) {
                        $ignoreVisible = $select[Vps_Component_Select::IGNORE_VISIBLE];
                    } else {
                        $ignoreVisible = false;
                    }
                } else {
                    $ignoreVisible = $select->getPart(Vps_Component_Select::IGNORE_VISIBLE);
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
                    if ($select->hasPart(Vps_Component_Select::IGNORE_VISIBLE)) {
                        //ignoreVisible doch mitnehmen damit wir unterkomponeten von unsichtbaren
                        //komponenten finden
                        $s['ignoreVisible'] = $select->getPart(Vps_Component_Select::IGNORE_VISIBLE);
                    }
                    if ($select->hasPart(Vps_Component_Select::WHERE_SUBROOT)) {
                        //ignoreVisible doch mitnehmen damit wir unterkomponeten von unsichtbaren
                        //komponenten finden
                        $s['subroot'] = $select->getPart(Vps_Component_Select::WHERE_SUBROOT);
                    }
                    $s = new Vps_Component_Select($s);
                }

                if ($i == 0 && !$found) { // Muss eine Page sein
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

    public function getPageGenerators()
    {
        if (!is_null($this->_pageGenerators)) return $this->_pageGenerators;

        static $cache = null;
        if (!$cache) {
            $cache = Vps_Cache::factory('Core', 'Apc', array(
                'lifetime'=>null,
                'automatic_cleaning_factor' => false,
                'automatic_serialization'=>true));
        }
        $cacheId = $this->componentClass . '_pageGenerators';

        $generators = $cache->load($cacheId);
        if (!$generators) {
            $generators = array();
            foreach (Vpc_Abstract::getComponentClasses() as $class) {
                foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                    if (!isset($generator['class'])) {
                        throw new Vps_Exception("no generator class set for generator '$key' in component '$class'");
                    }
                    if (is_instance_of($generator['class'], 'Vpc_Root_Category_Generator')) {
                        $generators[] = array('class' => $class, 'key' => $key, 'generator' => $generator);
                    }
                }
            }
            $cache->save($generators, $cacheId);
        }

        $this->_pageGenerators = array();
        foreach ($generators as $g) {
            $this->_pageGenerators[] = Vps_Component_Generator_Abstract::getInstance(
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
     * @return Vps_Component_Data
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

    public function getComponentsByDbId($dbId, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
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

            if ($select->hasPart(Vps_Component_Select::LIMIT_COUNT)) {
                $limitCount = $select->getPart(Vps_Component_Select::LIMIT_COUNT);
            }
            $ret = array();
            foreach (Vpc_Abstract::getComponentClasses() as $class) {
                foreach (Vpc_Abstract::getSetting($class, 'generators') as $key => $generator) {
                    if (isset($generator['dbIdShortcut'])
                            && substr($dbId, 0, strlen($generator['dbIdShortcut'])) == $generator['dbIdShortcut']) {
                        $idParts = $this->_getIdParts(substr($dbId, strlen($generator['dbIdShortcut']) - 1));
                        $generator = Vps_Component_Generator_Abstract::getInstance($class, $key);
                        if (count($idParts) <= 1) {
                            $generatorSelect = clone $select;
                        } else {
                            $generatorSelect = new Vps_Component_Select(); // Select erst bei letzten Part
                            if ($select->hasPart(Vps_Component_Select::IGNORE_VISIBLE)) {
                                $generatorSelect->ignoreVisible($select->getPart(Vps_Component_Select::IGNORE_VISIBLE));
                            }
                            if ($select->hasPart(Vps_Component_Select::WHERE_SUBROOT)) {
                                $generatorSelect->whereSubroot($select->getPart(Vps_Component_Select::WHERE_SUBROOT));
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
    public function getComponentsByClass($class, $select = array())
    {
        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $cacheId = (is_array($class) ? implode(',', $class) : $class).$select->getHash();
        if (!isset($this->_componentsByClassCache[$cacheId])) {

            $lookingForChildClasses = Vpc_Abstract::getComponentClassesByParentClass($class);
            foreach ($lookingForChildClasses as $c) {
                if (is_instance_of($c, 'Vpc_Root_Abstract')) {
                    return array($this);
                }
            }
            $ret = $this->getComponentsBySameClass($lookingForChildClasses, $select);
            $this->_componentsByClassCache[$cacheId] = $ret;

        }
        return $this->_componentsByClassCache[$cacheId];
    }

    public function getComponentsBySameClass($lookingForChildClasses, $select = array())
    {
        if (!is_array($lookingForChildClasses) &&
            is_instance_of($lookingForChildClasses, 'Vpc_Root_Abstract')
        ) {
            return array($this);
        }

        if (!is_array($lookingForChildClasses)) {
            $lookingForChildClasses = array($lookingForChildClasses);
        }

        if (is_array($select)) {
            $select = new Vps_Component_Select($select);
        }
        $select->whereComponentClasses($lookingForChildClasses);

        if ($select->hasPart(Vps_Component_Select::LIMIT_COUNT)) {
            $limitCount = $select->getPart(Vps_Component_Select::LIMIT_COUNT);
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

        static $cache = null;
        if (!$cache) {
            $cache = Vps_Cache::factory('Core', 'Apc', array(
                'lifetime'=>null,
                'automatic_cleaning_factor' => false,
                'automatic_serialization'=>true));
        }

        $cacheId = 'genForCls'.$this->getComponentClass().str_replace('.', '_', implode('', $lookingForClasses));
        if (isset($this->_generatorsForClassesCache[$cacheId])) {
        } else if (($generators = $cache->load($cacheId)) !== false) {
            $ret = array();
            foreach ($generators as $g) {
                $ret[] = Vps_Component_Generator_Abstract::getInstance($g[0], $g[1]);
            }
            $this->_generatorsForClassesCache[$cacheId] = $ret;
        } else {
            $generators = array();
            foreach (Vpc_Abstract::getComponentClasses() as $c) {
                foreach (Vpc_Abstract::getSetting($c, 'generators') as $key => $generator) {
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
            $cache->save($generators, $cacheId);
            $ret = array();
            foreach ($generators as $g) {
                $ret[] = Vps_Component_Generator_Abstract::getInstance($g[0], $g[1]);
            }
            $this->_generatorsForClassesCache[$cacheId] = $ret;
        }
        return $this->_generatorsForClassesCache[$cacheId];
    }

    public function getComponentByClass($class, $select = array())
    {
        $components = $this->getComponentsByClass($class, $select);
        $this->_checkSingleComponent($components);
        if (isset($components[0])) {
            return $components[0];
        }
        return null;
    }

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
            $e = new Vps_Exception('getComponentByXxx must not get more than one component but got these: ' . implode(', ', $ids));
            $e->logOrThrow();
        }
    }

    public function setCurrentPage(Vps_Component_Data $page)
    {
        $this->_currentPage = $page;
    }

    public function getCurrentPage()
    {
        return $this->_currentPage;
    }

    public function setFilename($f)
    {
        $this->_filename = $f;
    }

    /**
     * @internal siehe Vps_Component_Generator_Abstract
     *
     * für getComponentById
     */
    public function addToDataCache(Vps_Component_Data $d, Vps_Component_Select $select)
    {
        if ($select->getPart(Vps_Component_Select::IGNORE_VISIBLE)) {
            $this->_dataCacheIgnoreVisible[$d->componentId] = $d;
        } else {
            $this->_dataCache[$d->componentId] = $d;
        }
    }

    /**
     * @internal siehe Vps_Component_Generator_Abstract
     *
     * für Component_Data::vpsUnserialize
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
