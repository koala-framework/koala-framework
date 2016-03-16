<?php
class Kwf_Config_Web extends Kwf_Config_Ini
{
    private $_section;

    static private $_instances = array();
    public static function getInstance($section = null)
    {
        if (!$section) {
            $section = call_user_func(array(Kwf_Setup::$configClass, 'getDefaultConfigSection'));
        }
        if (!isset(self::$_instances[$section])) {
            $cacheId = 'config_'.str_replace(array('-', '.'), '_', $section);
            $configClass = Kwf_Setup::$configClass;
            if (extension_loaded('apc')) {
                $apcCacheId = $cacheId.getcwd();
                $ret = apc_fetch($apcCacheId);
                if (!$ret) {
                    //two level cache
                    $cache = Kwf_Config_Cache::getInstance();
                    $ret = $cache->load($cacheId);
                    if(!$ret) {
                        $ret = new $configClass($section);
                        $cache->save($ret, $cacheId);
                    }
                    apc_add($apcCacheId, $ret);
                    apc_add($apcCacheId.'mtime', $cache->test($cacheId));
                }
            } else {
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
        $cacheId = 'config_'.str_replace(array('-', '.'), '_', Kwf_Setup::getConfigSection());
        Kwf_Config_Cache::getInstance()->save($config, $cacheId);
        if (extension_loaded('apc')) {
            $apcCacheId = $cacheId.getcwd();
            apc_delete($apcCacheId);
            apc_delete($apcCacheId.'mtime');
        }

        Kwf_Config_Web::clearInstances();
        Kwf_Registry::set('config', $config);
    }

    public static function getInstanceMtime($section)
    {
        if (extension_loaded('apc')) {
            $cacheId = 'config_'.str_replace(array('-', '.'), '_', $section);
            $cacheId .= getcwd();
            return apc_fetch($cacheId.'mtime');
        } else {
            $cache = Kwf_Config_Cache::getInstance();
            return $cache->test('config_'.str_replace(array('-', '.'), '_', $section));
        }
    }

    public static function getDefaultConfigSection()
    {
        if (file_exists('config_section')) {
            $ret = trim(file_get_contents('config_section'));
            if ($ret) return $ret;
        }
        return 'production';
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
            throw new Kwf_Exception("Add either '$section' to kwf/config.ini or set kwfConfigSection in web config.ini");
        }

        parent::__construct($kwfPath.'/config.ini', $kwfSection,
                        array('allowModifications'=>true));

        $this->_mergeWebConfig($section, $webPath);
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

    public static function findThemeConfigIni($theme)
    {
        //theme added using composer
        $ns = require 'vendor/composer/autoload_namespaces.php';
        foreach ($ns as $k=>$paths) {
            if (substr($theme, 0, strlen($k)) == $k) {
                if (count($paths) != 1) throw new Kwf_Exception("Failed merging theme config, only one path to theme supported");
                return $paths[0].'/'.str_replace('_', '/', substr($theme, 0, strpos($theme, '_'))).'/config.ini';
            }
        }
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $ip) {
            if (file_exists($ip.'/'.str_replace('_', '/', $theme).'.php')) {
                return $ip.'/'.str_replace('_', '/', substr($theme, 0, strpos($theme, '_'))).'/config.ini';
            }
        }
        return null;
    }

    private function _mergeThemeConfig($section, $webPath)
    {
        $webSection = $this->_getWebSection($section, $webPath.'/config.ini');

        //merge theme config.ini
        $ini = new Zend_Config_Ini($webPath.'/config.ini', $webSection);
        if ($ini->kwc && $t = $ini->kwc->theme) {
            $ini = self::findThemeConfigIni($t);
            if ($ini) {
                self::mergeConfigs($this, new Kwf_Config_Ini($ini, 'production'));
            }
        }
    }

    protected function _mergeWebConfig($section, $webPath)
    {
        $this->_mergeThemeConfig($section, $webPath);

        $webSection = $this->_getWebSection($section, $webPath.'/config.ini');

        self::mergeConfigs($this, new Kwf_Config_Ini($webPath.'/config.ini', $webSection));
        if (file_exists($webPath.'/config.local.ini') && filesize($webPath.'/config.local.ini')) {
            $webSection = $this->_getWebSection($section, $webPath.'/config.local.ini');
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
