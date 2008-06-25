<?php
class Vps_Component_Abstract
{
    private static $_settingsCache = array();
    public function __construct()
    {
        $GLOBALS['componentCounter']++;
        $this->_init();
    }

    /**
     * Wird nach dem Konstruktor aufgerufen. Initialisierungscode in Unterklassen ist hier richtig.
     */
    protected function _init()
    {
    }

    public static function hasSetting($class, $setting)
    {
        //$class = self::_normalizeClass($class);
        if (!isset(self::$_settingsCache[$class])) {
            self::$_settingsCache[$class] = call_user_func(array($class, 'getSettings'));
        }
        return isset(self::$_settingsCache[$class][$setting]);
    }

    public static function getSetting($class, $setting)
    {
        //$class = self::_normalizeClass($class);
        if (!isset(self::$_settingsCache[$class])) {
            self::$_settingsCache[$class] = call_user_func(array($class, 'getSettings'));
        }
        if (!isset(self::$_settingsCache[$class][$setting])) {
            throw new Vps_Exception("Setting '$setting' does not exist for Component $class");
        }
        return self::$_settingsCache[$class][$setting];
    }

    private static function _normalizeClass($class)
    {
        if (is_object($class)) $class = get_class($class);
        if (!Vps_Loader::classExists($class)) {
            $class = substr($class, 0, strrpos($class, '_')) . '_Component';
        }
        if (!class_exists($class)) {
            throw new Vps_Exception("Component '$class' does not exist");
        }
        return $class;
    }


    public static function getSettings()
    {
        return array(
            'assets'        => array('files'=>array(), 'dep'=>array()),
            'assetsAdmin'   => array('files'=>array(), 'dep'=>array()),
            'componentIcon' => new Vps_Asset('paragraph_page'),
            'placeholder'   => array(),
            'childComponentClasses' => array()
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

    protected function _getClassFromSetting($setting, $parentClass) {
        $classes = $this->_getSetting('childComponentClasses');
        if (!isset($classes[$setting])) {
            throw new Vpc_Exception(trlVps("ChildComponentClass {0} is not defined in settings.", $setting));
        }
        $class = $classes[$setting];
        if ($class != $parentClass && !is_subclass_of($class, $parentClass)) {
            throw new Vpc_Exception(trlVps("{0} '{1}' must be a subclass of {2}.",array($setting, $class, $parentClass)));
        }
        return $class;
    }

    public static function getComponentClasses()
    {
        static $componentClasses;
        if (isset($componentClasses)) return $componentClasses;
        $c = Vps_Registry::get('config')->vpc->rootComponent;
        $componentClasses = array($c);
        self::_getChildComponentClasses($componentClasses, $c);
        return $componentClasses;
    }

    //bitte nicht mehr in eine funktion zusammenführen :D
    //rekursive aufrufe sind viel einfacher mit einsprungs-fkt und rekursiv-fkt
    private static function _getChildComponentClasses(&$componentClasses, $class)
    {
        $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
        foreach ($classes as $class) {
            if ($class && !in_array($class, $componentClasses)) {
                $componentClasses[] = $class;
                self::_getChildComponentClasses($componentClasses, $class);
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