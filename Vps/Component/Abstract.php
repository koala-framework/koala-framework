<?php
class Vps_Component_Abstract
{
    public function __construct()
    {
        $this->_init();
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
        static $settingsCache;
        if (is_null($settingsCache)) $settingsCache = Vps_Registry::get('config')->debug->settingsCache;
        if (!$settingsCache) {
            //um endlosschleife in settingsCache zu verhindern
            if (!class_exists($class)) {
                throw new Vps_Exception("Invalid component '$class'");
            }
            $settings = call_user_func(array($class, 'getSettings'));
            return isset($settings[$setting]);
        }
        //& für performance
        $s =& self::_getSettingsCached();
        if (!isset($s[$class])) {
            throw new Vps_Exception("No Settings for component '$class' found; it is probably not in allComponentClasses.");
        }
        return isset($s[$class][$setting]);
    }

    public static function getSetting($class, $setting, $useSettingsCache = true)
    {
        static $settingsCache;
        if (is_null($settingsCache)) $settingsCache = Vps_Registry::get('config')->debug->settingsCache;
        if (!$useSettingsCache || !$settingsCache) {
            //um endlosschleife in settingsCache zu verhindern
            if (!class_exists($class)) {
                throw new Vps_Exception("Invalid component '$class'");
            }
            if ($setting == 'parentClasses') {
                $ret = array();
                $p = $class;
                while ($p) {
                    $p = get_parent_class($p);
                    if ($p) $ret[] = $p;
                }
                return $ret;
            } else {
                $settings = call_user_func(array($class, 'getSettings'));
                return $settings[$setting];
            }

        }
        //& für performance
        $s =& self::_getSettingsCached();
        if (!is_string($class)) {
            throw new Vps_Exception("Invalid component '$class'");
        }
        if (!isset($s[$class])) {
            throw new Vps_Exception("No Settings for component '$class' found; it is probably not in allComponentClasses.");
        }
        if (!isset($s[$class][$setting])) {
            throw new Vps_Exception("Setting '$setting' does not exist for Component '$class'");
        }
        return $s[$class][$setting];
    }

    public static function getSettingMtime()
    {
        if (!Vps_Registry::get('config')->vpc->rootComponent) return 0;
        $s =& self::_getSettingsCached();
        return $s['mtime'];
    }

    private static function &_getSettingsCached()
    {
        static $settings = null;
        if (!$settings) {
            $cache = new Vps_Assets_Cache(array('checkComponentSettings' => false));
            $cacheId = 'componentSettings'.Vps_Registry::get('trl')->getTargetLanguage();
            $settings = $cache->load($cacheId);
            if (!$settings) {
                $settings = array();
                $settings['mtimeFiles'] = array();
                $incPaths = explode(PATH_SEPARATOR, get_include_path());
                foreach (self::getComponentClasses(false/*don't use settings cache*/) as $c) {
                    $settings[$c] = call_user_func(array($c, 'getSettings'));
                    $p = $c;
                    $settings[$c]['parentClasses'] = array();
                    do {
                        $settings[$c]['parentClasses'][] = $p;
                    } while ($p = get_parent_class($p));
                    $p = $c;
                    do {
                        $file = str_replace('_', DIRECTORY_SEPARATOR, $p);
                        $f = false;
                        foreach ($incPaths as $incPath) {
                            if (file_exists($incPath.DIRECTORY_SEPARATOR.$file . '.php')) {
                                $f = $incPath.DIRECTORY_SEPARATOR.$file . '.php';
                                break;
                            }
                        }
                        if (!$f) { throw new Vps_Exception("File $file not found"); }
                        $settings['mtimeFiles'][] = $f;
                        $settings['mtimeFiles'][] = $incPath.DIRECTORY_SEPARATOR.$file.'.css';
                    } while ($p = get_parent_class($p));
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
            'generators'    => array(),
            'flags'         => array()
        );
    }

    public function getTable($tablename = null)
    {
        return self::createTable(get_class($this));
    }

    public static function createTable($class, $tablename = null)
    {
        static $tables = array();
        if (!isset($tables[$class.'-'.$tablename])) {
            if (!$tablename) {
                $tablename = Vpc_Abstract::getSetting($class, 'tablename');
                if (!$tablename) {
                    throw new Vpc_Exception('No tablename in Setting defined: ' . $class);
                }
            }
            if (!is_instance_of($tablename, 'Zend_Db_Table_Abstract')) {
                throw new Vps_Exception("table setting for generator in $class is not a Zend_Db_Table");
            }
            $tables[$class.'-'.$tablename] = new $tablename(array('componentClass'=>$class));
            if (!$tables[$class.'-'.$tablename] instanceof Zend_Db_Table_Abstract) {
                throw new Vps_Exception("table setting for generator in $class is not a Zend_Db_Table");
            }
        }
        return $tables[$class.'-'.$tablename];
    }

    public static function createModel($class)
    {
        static $models = array();
        if (!isset($models[$class])) {
            if (Vpc_Abstract::hasSetting($class, 'model')) {
                $models[$class] = Vpc_Abstract::getSetting($class, 'model');
            } else if (Vpc_Abstract::hasSetting($class, 'tablename')) {
                $t = self::createTable($class);
                if (!$t instanceof Zend_Db_Table_Abstract) {
                    throw new Vps_Exception("table setting for generator in $class is not a Zend_Db_Table");
                }
                $models[$class] = new Vps_Model_Db(array(
                    'table' => $t
                ));
            } else if (Vpc_Abstract::hasSetting($class, 'modelname')) {
                $modelName = Vpc_Abstract::getSetting($class, 'modelname');
                $models[$class] = Vps_Model_Abstract::getInstance($modelName);
            } else {
                throw new Vps_Exception("tablename and modelname not set for '$class'");
            }
        }
        return $models[$class];
    }

    public function getModel()
    {
        return self::createModel(get_class($this));
    }

    protected function _getSetting($setting)
    {
        return self::getSetting(get_class($this), $setting);
    }

    protected function _hasSetting($setting)
    {
        return self::hasSetting(get_class($this), $setting);
    }

    static public function getFlag($class, $flag)
    {
        $flags = self::getSetting($class, 'flags');
        if (!isset($flags[$flag])) return false;
        return $flags[$flag];
    }

    public static function getComponentClasses($useSettingsCache = true)
    {
        static $componentClasses = null;
        static $usedRoot = null;
        if ($usedRoot != Vps_Component_Data_Root::getComponentClass()) {
            //wg. unit-tests wo sich die root ändern kann
            $usedRoot = Vps_Component_Data_Root::getComponentClass();
            $componentClasses = null;
        }
        if ($componentClasses) return $componentClasses;
        if (!$usedRoot) return array();
        $componentClasses = array($usedRoot);
        self::_getChildComponentClasses($componentClasses, $usedRoot, $useSettingsCache);
        return $componentClasses;
    }

    private static function _getChildComponentClasses(&$componentClasses, $class, $useSettingsCache)
    {
        $classes = array();
        foreach (Vpc_Abstract::getSetting($class, 'generators', $useSettingsCache) as $generator) {
            if (is_array($generator['component'])) {
                $classes = array_merge($classes, $generator['component']);
            } else {
                $classes[] = $generator['component'];
            }
        }
        $plugins = Vpc_Abstract::getSetting($class, 'plugins', $useSettingsCache);
        if (is_array($plugins)) {
            $classes = array_merge($classes, $plugins);
        }
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