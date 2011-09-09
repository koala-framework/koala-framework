<?php
require_once 'Vps/Config/Ini.php';

class Vps_Config_Web extends Vps_Config_Ini
{
    static private $_instances = array();
    public static function getInstance($section)
    {
        if (!isset(self::$_instances[$section])) {
            $cacheId = 'config_'.str_replace('-', '_', $section);
            $configClass = Vps_Setup::$configClass;
            require_once str_replace('_', '/', $configClass).'.php';
            if (extension_loaded('apc')) {
                $apcCacheId = $cacheId.getcwd();
                $ret = apc_fetch($apcCacheId);
                if ($ret && $ret->debug->componentCache->checkComponentModification) {
                    $masterFiles = array(
                        'application/config.ini',
                        VPS_PATH . '/config.ini'
                    );
                    if (file_exists('application/vps_branch')) $masterFiles[] = 'application/vps_branch';
                    $mtime = apc_fetch($apcCacheId.'mtime');
                    foreach ($masterFiles as $f) {
                        if (filemtime($f) > $mtime) {
                            apc_delete($apcCacheId);
                            apc_delete($apcCacheId.'mtime');
                            $ret = false;
                            break;
                        }
                    }
                }
                if (!$ret) {
                    //two level cache
                    require_once 'Vps/Config/Cache.php';
                    $cache = Vps_Config_Cache::getInstance();
                    if(!$ret = $cache->load($cacheId)) {
                        $ret = new $configClass($section);
                        $cache->save($ret, $cacheId);
                    }
                    apc_add($apcCacheId, $ret);
                    apc_add($apcCacheId.'mtime', time());
                }
            } else {
                require_once 'Vps/Config/Cache.php';
                $cache = Vps_Config_Cache::getInstance();
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

    public static function getInstanceMtime($section)
    {
        if (extension_loaded('apc')) {
            $cacheId = 'config_'.str_replace('-', '_', $section);
            $cacheId .= getcwd();
            return apc_fetch($cacheId.'mtime');
        } else {
            require_once 'Vps/Config/Cache.php';
            $cache = Vps_Config_Cache::getInstance();
            return $cache->test('config_'.str_replace('-', '_', $section));
        }
    }

    public function __construct($section, $options = array())
    {
        if (isset($options['vpsPath'])) {
            $vpsPath = $options['vpsPath'];
        } else {
            $vpsPath = VPS_PATH;
        }
        if (isset($options['webPath'])) {
            $webPath = $options['webPath'];
        } else {
            $webPath = '.';
        }

        $vpsSection = false;

        $webSection = $this->_getWebSection($webPath.'/application/config.ini', $section);
        $webConfig = parse_ini_file($webPath.'/application/config.ini', true);
        foreach ($webConfig as $i=>$cfg) {
            if ($i == $webSection
                || substr($i, 0, strlen($webSection)+1)==$webSection.' '
                || substr($i, 0, strlen($webSection)+1)==$webSection.':'
            ) {
                if (isset($cfg['vpsConfigSection'])) {
                    $vpsSection = $cfg['vpsConfigSection'];
                }
                break;
            }
        }
        if (!$vpsSection) {
            $vpsConfigFull = array_keys(parse_ini_file($vpsPath.'/config.ini', true));
            foreach ($vpsConfigFull as $i) {
                if ($i == $section
                    || substr($i, 0, strlen($section)+1)==$section.' '
                    || substr($i, 0, strlen($section)+1)==$section.':'
                ) {
                    $vpsSection = $section;
                    break;
                }
            }
        }
        if (!$vpsSection) {
            require_once 'Vps/Exception.php';
            throw new Vps_Exception("Add either '$section' to vps/config.ini or set vpsConfigSection in web config.ini");
        }

        parent::__construct($vpsPath.'/config.ini', $vpsSection,
                        array('allowModifications'=>true));

        $fixes = array();
        if ($webSection != $section && ($this->server->host == 'vivid' || $this->server->host == 'vivid-test-server')) {
            $fixes = array(
                'libraryPath' => $this->libraryPath,
                'uploads' => $this->uploads,
                'serverUser' => $this->server->user,
                'serverHost' => $this->server->host,
                'serverDir' => $this->server->dir,
                'serverDomain' => $this->server->domain
            );
        }

        $this->_mergeWebConfig($webPath.'/application/config.ini', $section);

        if ($fixes) {
            $this->libraryPath = $fixes['libraryPath'];
            $this->uploads = $fixes['uploads'];
            $this->server->user = $fixes['serverUser'];
            $this->server->host = $fixes['serverHost'];
            $this->server->dir = $fixes['serverDir'];
            $this->server->domain = $fixes['serverDomain'];
        }

        foreach ($this->path as $k=>$i) {
            $this->path->$k = str_replace(array('%libraryPath%', '%vpsPath%'),
                                            array($this->libraryPath, $vpsPath),
                                            $i);
        }
        foreach ($this->includepath as $k=>$i) {
            $this->includepath->$k = str_replace(array('%libraryPath%', '%vpsPath%'),
                                            array($this->libraryPath, $vpsPath),
                                            $i);
        }

        $this->server->dir = str_replace('%id%', $this->application->id, $this->server->dir);
        $this->server->domain = str_replace('%id%', $this->application->id, $this->server->domain);
        $this->server->mongo->database = str_replace('%id%', $this->application->id, $this->server->mongo->database);
        $this->uploads = str_replace('%id%', $this->application->id, $this->uploads);
    }

    private function _getWebSection($file, $section)
    {
        $webConfigSections = array_keys(parse_ini_file($file, true));
        foreach ($webConfigSections as $i) {
            if ($i == $section
                || substr($i, 0, strlen($section)+1)==$section.' '
                || substr($i, 0, strlen($section)+1)==$section.':'
            ) {
                return $section;
            }
        }
        foreach ($webConfigSections as $i) {
            if ($i == $section
                || substr($i, 0, 6)=='vivid '
                || substr($i, 0, 6)=='vivid:'
            ) {
                return 'vivid';
            }
        }
        return 'production';
    }

    protected function _mergeWebConfig($path, $section)
    {
        $this->_mergeFile($path, $section);
    }

    protected final function _mergeFile($file, $section)
    {
        return self::mergeConfigs($this, new Vps_Config_Ini($file, $this->_getWebSection($file, $section)));
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
                    $main->$key = Vps_Config_Web::mergeConfigs(
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

    public static function getValueArray($var)
    {
        require_once 'Vps/Cache/Simple.php';

        $cacheId = 'configAr-'.$var;
        $ret = Vps_Cache_Simple::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $cfg = Vps_Registry::get('config');
        foreach (explode('.', $var) as $i) {
            $cfg = $cfg->$i;
        }
        $ret = $cfg->toArray();

        Vps_Cache_Simple::add($cacheId, $ret);

        return $ret;
    }

    public static function getValue($var)
    {
        require_once 'Vps/Cache/Simple.php';

        $cacheId = 'config-'.$var;
        $ret = Vps_Cache_Simple::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $cfg = Vps_Registry::get('config');
        foreach (explode('.', $var) as $i) {
            $cfg = $cfg->$i;
        }
        $ret = $cfg;
        if (is_object($ret)) {
            throw new Vps_Exception("this would return an object, use getValueArray instead");
        }

        Vps_Cache_Simple::add($cacheId, $ret);

        return $ret;
    }

    public static function clearValueCache()
    {
        Vps_Cache_Simple::clear('config-');
        Vps_Cache_Simple::clear('configAr-');
    }
}
