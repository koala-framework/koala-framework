<?php
class Vps_Component_Abstract
{
    public function __construct()
    {
        $this->_init();
        Vps_Benchmark::count('components');
    }

    /**
     * Wird nach dem Konstruktor aufgerufen. Initialisierungscode in Unterklassen ist hier richtig.
     */
    protected function _init()
    {
    }

    public static function hasSettings($class)
    {
        //& für performance
        $s =& self::_getSettingsCached();
        return isset($s[$class]);
    }

    public static function hasSetting($class, $setting)
    {
        //& für performance
        $s =& self::_getSettingsCached();
        if (!isset($s[$class])) {
            throw new Vps_Exception("No Settings for component '$class' found; it is probably not in allComponentClasses.");
        }
        return isset($s[$class][$setting]);
    }

    public static function getSetting($class, $setting, $useSettingsCache = true)
    {
        if (!$useSettingsCache) {
            //um endlosschleife in settingsCache zu verhindern
            $settings = call_user_func(array($class, 'getSettings'));
            return $settings[$setting];
        }
        //& für performance
        $s =& self::_getSettingsCached();
        if (!isset($s[$class])) {
            throw new Vps_Exception("No Settings for component '$class' found; it is probably not in allComponentClasses.");
        }
        if (!isset($s[$class][$setting])) {
            throw new Vps_Exception("Setting '$setting' does not exist for Component '$class'");
        }
        return $s[$class][$setting];
    }

    private static function &_getSettingsCached()
    {
        static $settings = null;
        if (!$settings) {
            $frontendOptions = array(
                'lifetime' => null,
                'automatic_serialization' => true
            );
            $backendOptions = array(
                'cache_dir' => 'application/cache/config/'
            );
            $cache = Zend_Cache::factory('Core', 'File', $frontendOptions, $backendOptions);
            $cacheId = 'componentSettings'.Vps_Registry::get('trl')->getTargetLanguage();
            $settings = $cache->load($cacheId);

            if ($settings && Vps_Registry::get('config')->debug->componentCache->checkComponentModification) {
                foreach ($settings as $s) {
                    if (!isset($s['parentFiles'])) {
                        Vps_Benchmark::info("Settings-Cache regenerated (checkComponentModification changed)");
                        $settings = false;
                        break;
                    }
                    foreach ($s['parentFiles'] as $f) {
                        if (filemtime($f) > $s['mtime']) {
                            Vps_Benchmark::info("Settings-Cache regenerated ($f modified)");
                            $settings = false;
                            break;
                        }
                    }
                    if (!$settings) break;
                }
            } else if (!$settings) {
                Vps_Benchmark::info('Settings-Cache regenerated (was empty)');
            }

            if (!$settings) {
                $settings = array();
                $incPaths = explode(PATH_SEPARATOR, get_include_path());
                foreach (self::getComponentClasses(false/*don't use settings cache*/) as $c) {
                    $settings[$c] = call_user_func(array($c, 'getSettings'));
                    $p = $c;
                    $settings[$c]['parentClasses'] = array();
                    do {
                        $settings[$c]['parentClasses'][] = $p;
                    } while ($p = get_parent_class($p));
                    if (Vps_Registry::get('config')->debug->componentCache->checkComponentModification) {
                        $settings[$c]['parentFiles'] = array();
                        $settings[$c]['mtime'] = 0;
                        $p = $c;
                        do {
                            $file = str_replace('_', DIRECTORY_SEPARATOR, $p) . '.php';
                            $f = false;
                            foreach ($incPaths as $incPath) {
                                if (file_exists($incPath.DIRECTORY_SEPARATOR.$file)) {
                                    $f = $incPath.DIRECTORY_SEPARATOR.$file;
                                    break;
                                }
                            }
                            if (!$f) { throw new Vps_Exception("File $file not found"); }
                            $settings[$c]['parentFiles'][] = $f;
                            $settings[$c]['mtime'] = max($settings[$c]['mtime'], filemtime($f));
                        } while ($p = get_parent_class($p));
                    }
                }
                $cache->save($settings, $cacheId);
            }
        }
        return $settings;
    }

    public static function getParentClasses($c)
    {
        //im prinzip das gleiche wie while() { get_parent_class() } wird aber so
        //in settings-cache gecached
        return self::getSetting($c, 'parentClasses');
    }



    public static function getSettings()
    {
        return array(
            'assets'        => array('files'=>array(), 'dep'=>array()),
            'assetsAdmin'   => array('files'=>array(), 'dep'=>array()),
            'componentIcon' => new Vps_Asset('paragraph_page'),
            'placeholder'   => array(),
            'plugins'       => array(),
            'generators'    => array()

        );
    }

    public static function createTable($class, $tablename = null)
    {
        if (!$tablename) {
            $tablename = Vpc_Abstract::getSetting($class, 'tablename');
            if (!$tablename) {
                throw new Vpc_Exception('No tablename in Setting defined: ' . $class);
            }
        }
        return new $tablename(array('componentClass'=>$class));
    }
    
    protected function _getSetting($setting)
    {
        return self::getSetting(get_class($this), $setting);
    }

    public static function getComponentClasses($useSettingsCache = true)
    {
        static $componentClasses = null;
        if ($componentClasses) return $componentClasses;
        $componentClasses = array();
        $c = Vps_Registry::get('config')->vpc->rootComponent;
        $componentClasses[] = $c;
        self::_getChildComponentClasses($componentClasses, $c, $useSettingsCache);
        foreach (Vps_Registry::get('config')->vpc->masterComponents as $c) {
            $componentClasses[] = $c;
            self::_getChildComponentClasses($componentClasses, $c, $useSettingsCache);
        }
        return $componentClasses;
    }

    private static function _getChildComponentClasses(&$componentClasses, $class, $useSettingsCache)
    {
        $classes = Vpc_Abstract::getChildComponentClasses($class, null, $useSettingsCache);
        foreach ($classes as $class) {
            if ($class && !in_array($class, $componentClasses)) {
                $componentClasses[] = $class;
                self::_getChildComponentClasses($componentClasses, $class, $useSettingsCache);
            }
        }
    }

    protected function _getPlaceholder($name)
    {
        $s = $this->_getSetting('placeholder');
        if (!isset($s[$name])) {
            throw new Vps_Exception("Unknown placeholder '$name'");
        }
        return $s[$name];
    }
}