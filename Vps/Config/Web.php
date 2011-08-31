<?php
require_once 'Vps/Config/Ini.php';

class Vps_Config_Web extends Vps_Config_Ini
{
    static private $_instances = array();
    public static function getInstance($section = null)
    {
        if (!$section) {
            require_once str_replace('_', '/', Vps_Setup::$configClass).'.php';
            $section = call_user_func(array(Vps_Setup::$configClass, 'getConfigSection'));
        }
        if (!isset(self::$_instances[$section])) {
            require_once 'Vps/Config/Cache.php';
            $cache = Vps_Config_Cache::getInstance();
            $cacheId = 'config_'.str_replace('-', '_', $section);
            $configClass = Vps_Setup::$configClass;
            require_once str_replace('_', '/', $configClass).'.php';
            if(!$ret = $cache->load($cacheId)) {
                $ret = new $configClass($section);
                $mtime = time();
                $cache->save($ret, $cacheId);
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
        require_once 'Vps/Config/Cache.php';
        $cache = Vps_Config_Cache::getInstance();
        return $cache->test('config_'.str_replace('-', '_', $section));
    }

    public static function getConfigSection()
    {
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';

        //www abschneiden damit www.test und www.preview usw auch funktionieren
        if (substr($host, 0, 4)== 'www.') $host = substr($host, 4);

        if (php_sapi_name() == 'cli') {
            //wenn über kommandozeile aufgerufen
            $path = getcwd();
        } else {
            $path = $_SERVER['SCRIPT_FILENAME'];
        }
        if (file_exists('application/config_section')) {
            return trim(file_get_contents('application/config_section'));
        } else if (substr($host, 0, 9)=='dev.test.') {
            return 'devtest';
        } else if (substr($host, 0, 4)=='dev.') {
            return 'dev';
        } else if (substr($host, 0, 5)=='test.') {
            return 'test';
        } else if (substr($host, 0, 8)=='preview.') {
            return 'preview';
        } else {
            return 'production';
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

        $webSection = $this->_getWebSection($section, $webPath);
        $vpsSection = $this->_getVpsSection($section, $webPath, $vpsPath);
        if (!$vpsSection) {
            require_once 'Vps/Exception.php';
            throw new Vps_Exception("Add either '$section' to vps/config.ini or set vpsConfigSection in web config.ini");
        }

        parent::__construct($vpsPath.'/config.ini', $vpsSection,
                        array('allowModifications'=>true));

        $this->_mergeWebConfig($section, $webPath);

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
    }

    protected function _getWebSection($section, $webPath)
    {
        $webConfigSections = array_keys(parse_ini_file($webPath.'/application/config.ini', true));
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

    protected function _getVpsSection($section, $webPath, $vpsPath)
    {
        $vpsSection = false;
        $webSection = $this->_getWebSection($section, $webPath);
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
        return $vpsSection;
    }

    protected function _mergeWebConfig($section, $webPath)
    {
        $webSection = $this->_getWebSection($section, $webPath);
        self::mergeConfigs($this, new Vps_Config_Ini($webPath.'/application/config.ini', $webSection));
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
}
