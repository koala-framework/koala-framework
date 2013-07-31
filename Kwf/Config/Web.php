<?php
require_once 'Kwf/Config/Ini.php';

class Kwf_Config_Web extends Kwf_Config_Ini
{
    private $_section;
    protected $_masterFiles = array();

    static private $_instances = array();
    public static function getInstance($section = null)
    {
        if (!$section) {
            require_once str_replace('_', '/', Kwf_Setup::$configClass).'.php';
            $section = call_user_func(array(Kwf_Setup::$configClass, 'getDefaultConfigSection'));
        }
        if (!isset(self::$_instances[$section])) {
            $cacheId = 'config_'.str_replace('-', '_', $section);
            $configClass = Kwf_Setup::$configClass;
            require_once str_replace('_', '/', $configClass).'.php';
            if (extension_loaded('apc')) {
                $apcCacheId = $cacheId.getcwd();
                $ret = apc_fetch($apcCacheId);
                if (!$ret) {
                    //two level cache
                    require_once 'Kwf/Config/Cache.php';
                    $cache = Kwf_Config_Cache::getInstance();
                    $ret = $cache->load($cacheId);
                    if ($ret) {
                        $mtime = $cache->test($cacheId);
                        foreach ($ret->getMasterFiles() as $f) {
                            if (filemtime($f) > $mtime) {
                                $ret = false;
                                break;
                            }
                        }
                    }
                    if(!$ret) {
                        $ret = new $configClass($section);
                        $cache->save($ret, $cacheId);
                    }
                    apc_add($apcCacheId, $ret);
                    apc_add($apcCacheId.'mtime', $cache->test($cacheId));
                }
            } else {
                require_once 'Kwf/Config/Cache.php';
                $cache = Kwf_Config_Cache::getInstance();
                if(!$ret = $cache->load($cacheId)) {
                    $ret = new $configClass($section);
                    $cache->save($ret, $cacheId);
                }
            }
            self::$_instances[$section] = $ret;
        }
        return self::$_instances[$section];
    }

    public static function clearInstances()
    {
        self::$_instances = array();
    }
    
    public static function reload()
    {
        $configClass = Kwf_Setup::$configClass;
        $config = new $configClass(Kwf_Setup::getConfigSection());
        $cacheId = 'config_'.str_replace('-', '_', Kwf_Setup::getConfigSection());
        Kwf_Config_Cache::getInstance()->save($config, $cacheId);

        Kwf_Config_Web::clearInstances();
        Kwf_Registry::set('config', $config);
        Kwf_Registry::set('configMtime', Kwf_Config_Cache::getInstance()->test($cacheId));
    }

    public static function getInstanceMtime($section)
    {
        if (extension_loaded('apc')) {
            $cacheId = 'config_'.str_replace('-', '_', $section);
            $cacheId .= getcwd();
            return apc_fetch($cacheId.'mtime');
        } else {
            require_once 'Kwf/Config/Cache.php';
            $cache = Kwf_Config_Cache::getInstance();
            return $cache->test('config_'.str_replace('-', '_', $section));
        }
    }

    public static function getDefaultConfigSection()
    {
        if (file_exists('config_section')) {
            return trim(file_get_contents('config_section'));
        } else {
            return 'production';
        }
    }

    public function __construct($section, $options = array())
    {
        $this->_section = $section;

        if (isset($options['kwfPath'])) {
            $kwfPath = $options['kwfPath'];
        } else {
            $kwfPath = KWF_PATH;
        }
        if (isset($options['webPath'])) {
            $webPath = $options['webPath'];
        } else {
            $webPath = '.';
        }

        $webSection = $this->_getWebSection($section, $webPath.'/config.ini');
        $kwfSection = $this->_getKwfSection($section, $webPath, $kwfPath);
        if (!$kwfSection) {
            require_once 'Kwf/Exception.php';
            throw new Kwf_Exception("Add either '$section' to kwf/config.ini or set kwfConfigSection in web config.ini");
        }

        $this->_masterFiles[] = $kwfPath.'/config.ini';
        parent::__construct($kwfPath.'/config.ini', $kwfSection,
                        array('allowModifications'=>true));

        $this->_mergeWebConfig($section, $webPath);

        if (!$this->libraryPath) {
            $p = trim(file_get_contents(KWF_PATH.'/include_path'));
            if (preg_match('#(.*)/zend/%version%$#', $p, $m)) {
                $this->libraryPath = $m[1];
            } else {
                require_once 'Kwf/Exception.php';
                throw new Kwf_Exception("Can't detect libraryPath");
            }
        }

        foreach ($this->path as $k=>$i) {
            $this->path->$k = str_replace(array('%libraryPath%', '%kwfPath%'),
                                            array($this->libraryPath, $kwfPath),
                                            $i);
        }
        foreach ($this->includepath as $k=>$i) {
            $this->includepath->$k = str_replace(array('%libraryPath%', '%kwfPath%'),
                                            array($this->libraryPath, $kwfPath),
                                            $i);
        }
        foreach ($this->externLibraryPath as $k=>$i) {
            $this->externLibraryPath->$k = str_replace(array('%libraryPath%', '%kwfPath%'),
                                            array($this->libraryPath, $kwfPath),
                                            $i);
        }
        foreach ($this->assets->dependencies as $k=>$i) {
            $this->assets->dependencies->$k = str_replace(array('%libraryPath%', '%kwfPath%'),
                                            array($this->libraryPath, $kwfPath),
                                            $i);
        }
    }

    public function getMasterFiles()
    {
        return $this->_masterFiles;
    }

    public function getSection()
    {
        return $this->_section;
    }

    protected function _getWebSection($section, $configFile)
    {
        $webConfigSections = array_keys(parse_ini_file($configFile, true));
        foreach ($webConfigSections as $i) {
            if ($i == $section
                || substr($i, 0, strlen($section)+1)==$section.' '
                || substr($i, 0, strlen($section)+1)==$section.':'
            ) {
                return $section;
            }
        }
        return 'production';
    }

    protected function _getKwfSection($section, $webPath, $kwfPath)
    {
        $kwfSection = false;
        $webSection = $this->_getWebSection($section, $webPath.'/config.ini');
        $webConfig = parse_ini_file($webPath.'/config.ini', true);
        foreach ($webConfig as $i=>$cfg) {
            if ($i == $webSection
                || substr($i, 0, strlen($webSection)+1)==$webSection.' '
                || substr($i, 0, strlen($webSection)+1)==$webSection.':'
            ) {
                if (isset($cfg['kwfConfigSection'])) {
                    $kwfSection = $cfg['kwfConfigSection'];
                }
                break;
            }
        }
        if (!$kwfSection) {
            $kwfConfigFull = array_keys(parse_ini_file($kwfPath.'/config.ini', true));
            foreach ($kwfConfigFull as $i) {
                if ($i == $section
                    || substr($i, 0, strlen($section)+1)==$section.' '
                    || substr($i, 0, strlen($section)+1)==$section.':'
                ) {
                    $kwfSection = $section;
                    break;
                }
            }
        }
        return $kwfSection;
    }

    protected function _mergeWebConfig($section, $webPath)
    {
        $webSection = $this->_getWebSection($section, $webPath.'/config.ini');

        //merge theme config.ini
        $ini = new Zend_Config_Ini($webPath.'/config.ini', $webSection);
        if ($ini->kwc && $t = $ini->kwc->theme) {
            foreach (explode(PATH_SEPARATOR, get_include_path()) as $ip) {
                if (file_exists($ip.'/'.str_replace('_', '/', $t).'.php')) {
                    $dir = $ip.'/'.str_replace('_', '/', substr($t, 0, strpos($t, '_')));
                    self::mergeConfigs($this, new Kwf_Config_Ini($dir.'/config.ini', 'production'));
                }
            }
        }

        $this->_masterFiles[] = $webPath.'/config.ini';
        self::mergeConfigs($this, new Kwf_Config_Ini($webPath.'/config.ini', $webSection));
        if (file_exists($webPath.'/config.local.ini') && filesize($webPath.'/config.local.ini')) {
            $webSection = $this->_getWebSection($section, $webPath.'/config.local.ini');
            $this->_masterFiles[] = $webPath.'/config.local.ini';
            self::mergeConfigs($this, new Kwf_Config_Ini($webPath.'/config.local.ini', $webSection));
        }
    }

    /**
     * Diesen Merge sollte eigentlich das Zend machen, aber das merged nicht so
     * wie wir das erwarten. Beispiel:
     *
     * Main Config:
     * bla.blubb[] = x
     * bla.blubb[] = y
     * bla.blubb[] = z
     *
     * Merge Config:
     * bla.blubb[] = a
     * bla.blubb[] = b
     *
     * Nach den Config-Section regeln würde man erwarten, dass nach dem mergen nur mehr
     * a und b drin steht. Tatsächlich merget Zend aber so, dass a, b, z überbleibt.
     * Zend überschreibt die Werte, was wir nicht wollen, deshalb dieses
     * händische mergen hier.
     */
    public static function mergeConfigs(Zend_Config $main, Zend_Config $merge)
    {
        // check if all keys are of type 'integer' and if so, only use merge config
        $everyKeyIsInteger = true;
        foreach($merge as $key => $item) {
            if (!is_int($key)) {
                $everyKeyIsInteger = false;
                break;
            }
        }
        if ($everyKeyIsInteger) {
            return $merge;
        }

        foreach($merge as $key => $item) {
            if(isset($main->$key)) {
                if($item instanceof Zend_Config && $main->$key instanceof Zend_Config) {
                    $main->$key = Kwf_Config_Web::mergeConfigs(
                        $main->$key,
                        new Zend_Config($item->toArray(), !$main->readOnly())
                    );
                } else {
                    $main->$key = $item;
                }
            } else {
                if($item instanceof Zend_Config) {
                    $main->$key = new Zend_Config($item->toArray(), !$main->readOnly());
                } else {
                    $main->$key = $item;
                }
            }
        }
        return $main;
    }
}
