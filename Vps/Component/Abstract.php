<?php
class Vps_Component_Abstract
{
    private static $_settings = null;

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
            throw new Vps_Exception("No Settings for component '$class' found; it is probably not in allComponentClasses. Requested setting: $setting");
        }
        return array_key_exists($setting, $s[$class]);
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
                if (!array_key_exists($setting, $settings)) {
                    throw new Vps_Exception("Couldn't find required setting '$setting' for $class.");
                }
                if ($setting == 'generators') {
                    $classes = array($class);
                    $classes = array_merge($classes, self::getSetting($class, 'parentClasses', $useSettingsCache));
                    $cc = array();
                    if (isset(Vps_Registry::get('config')->vpc->childComponents)) {
                        $cc = Vps_Registry::get('config')->vpc->childComponents->toArray();
                    }
                    foreach ($classes as $c) {
                        if (isset($cc[$c])) {
                            if (!isset($settings['configChildComponentsGenerator'])) {
                                throw new Vps_Exception("configChildComponentsGenerator setting not set for '$class'");
                            }
                            $gen = $settings['configChildComponentsGenerator'];
                            if (!isset($settings['generators'][$gen])) {
                                throw new Vps_Exception("invalid configChildComponentsGenerator for '$class'");
                            }
                            if (!is_array($settings['generators'][$gen]['component'])) {
                                throw new Vps_Exception("component must be an array for generator '$gen' for '$class'");
                            }
                            foreach ($cc[$c] as $componentKey=>$componentClass) {
                                $settings['generators'][$gen]['component'][$componentKey] = $componentClass;
                            }
                        }
                    }
                }
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
        // man könnte hier isset() machen, nur wenn das setting NULL ist, gibt es false zurück... scheis PHP :)
        if (!array_key_exists($setting, $s[$class])) {
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

    //wenn root geändert wird muss der cache hier gelöscht werden können
    public static function resetSettingsCache()
    {
        self::$_settings = null;
    }

    private static function &_getSettingsCached()
    {
        if (!self::$_settings) {
            $cache = new Vps_Assets_Cache(array('checkComponentSettings' => false));
            $cacheId = 'componentSettings'.Vps_Registry::get('trl')->getTargetLanguage()
                                .'_'.Vps_Component_Data_Root::getComponentClass();
            self::$_settings = $cache->load($cacheId);
            if (!self::$_settings) {
                self::$_settings = array();
                self::$_settings['mtimeFiles'] = array();
                $incPaths = explode(PATH_SEPARATOR, get_include_path());
                foreach (self::getComponentClasses(false/*don't use settings cache*/) as $c) {
                    self::$_settings[$c] = call_user_func(array($c, 'getSettings'));

                    //generators �ber getSetting holen, da dort noch die aus der config dazugemixt werden
                    self::$_settings[$c]['generators'] = self::getSetting($c, 'generators', false/*don't use settings cache*/);

                    try {
                        call_user_func(array($c, 'validateSettings'), self::$_settings[$c], $c);
                    } catch (Vps_Exception $e) {
                        throw new Vps_Exception("$c: ".$e->getMessage());
                    }

                    //*** templates
                    self::$_settings[$c]['templates'] = array(
                        'Master' => Vpc_Admin::getComponentFile($c, 'Master', 'tpl'),
                        'Component' => Vpc_Admin::getComponentFile($c, 'Component', 'tpl'),
                        'Partial' => Vpc_Admin::getComponentFile($c, 'Partial', 'tpl')
                    );

                    //*** parentClasses
                    self::$_settings[$c]['parentClasses'] = self::getSetting($c, 'parentClasses', false/*don't use settings cache*/);

                    //*** processedCssClass
                    self::$_settings[$c]['processedCssClass'] = '';
                    if (isset(self::$_settings[$c]['cssClass'])) {
                        self::$_settings[$c]['processedCssClass'] .= self::$_settings[$c]['cssClass'].' ';
                    }
                    $cssClass = array(self::_formatCssClass($c));
                    $dirs = explode(PATH_SEPARATOR, get_include_path());
                    foreach (self::$_settings[$c]['parentClasses'] as $i) {
                        $file = str_replace('_', '/', $i);
                        if (substr($file, -10) != '/Component') {
                            $file .= '/Component';
                        }
                        foreach ($dirs as $dir) {
                            if (is_file($dir.'/'.$file.'.css') || is_file($dir.'/'.$file.'.printcss')) {
                                $cssClass[] = self::_formatCssClass($i);
                                break;
                            }
                        }
                    }
                    self::$_settings[$c]['processedCssClass'] .= implode(' ', array_reverse($cssClass));
                    self::$_settings[$c]['processedCssClass'] = trim(self::$_settings[$c]['processedCssClass']);

                    //*** mtimeFiles
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
                        self::$_settings['mtimeFiles'][] = $f;
                        self::$_settings['mtimeFiles'][] = $incPath.DIRECTORY_SEPARATOR.$file.'.css';
                    } while ($p = get_parent_class($p));
                }

                $cache->save(self::$_settings, $cacheId);
            }
        }
        return self::$_settings;
    }

    static private function _formatCssClass($cls)
    {
        if (substr($cls, -10) == '_Component') {
            $cls = substr($cls, 0, -10);
        }
        $cls = str_replace('_', '', $cls);
        return strtolower(substr($cls, 0, 1)) . substr($cls, 1);
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

    public static function validateSettings($settings, $componentClass)
    {
        if (isset($settings['ownModel']) && $settings['ownModel']) {
            try {
                $m = Vps_Model_Abstract::getInstance($settings['ownModel']);
                $pk = $m->getPrimaryKey();
            } catch (Exception $e) {}
            if (isset($pk) && $pk != 'component_id') {
                throw new Vps_Exception("ownModel for '$componentClass' must have 'component_id' as primary key");
            }
        }
        if (isset($settings['modelname'])) {
            throw new Vps_Exception("modelname for '$componentClass' is set - please rename into ownModel or childModel");
        }
        if (isset($settings['model'])) {
            throw new Vps_Exception("model for '$componentClass' is set - please rename into ownModel or childModel");
        }
        if (isset($settings['formModel'])) {
            throw new Vps_Exception("formModel is no longer supported. Set the model in the FrontendForm.php. Component: '$componentClass'");
        }
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
                throw new Vps_Exception("table setting '$tablename' for generator in $class is not a Zend_Db_Table");
            }
            $tables[$class.'-'.$tablename] = new $tablename(array('componentClass'=>$class));
            if (!$tables[$class.'-'.$tablename] instanceof Zend_Db_Table_Abstract) {
                throw new Vps_Exception("table setting for generator in $class is not a Zend_Db_Table");
            }
        }
        return $tables[$class.'-'.$tablename];
    }

    /**
     * @deprecated
     */
    public static function createModel($class)
    {
        return self::createOwnModel($class);
    }

    public static function createOwnModel($class)
    {
        static $models = array();
        if (!array_key_exists($class, $models)) {
            if (Vpc_Abstract::hasSetting($class, 'tablename')) {
                $t = self::createTable($class);
                if (!$t instanceof Zend_Db_Table_Abstract) {
                    throw new Vps_Exception("table setting for generator in $class is not a Zend_Db_Table");
                }
                $models[$class] = new Vps_Model_Db(array(
                    'table' => $t
                ));
            } else if (Vpc_Abstract::hasSetting($class, 'ownModel')) {
                $modelName = Vpc_Abstract::getSetting($class, 'ownModel');
                $models[$class] = Vps_Model_Abstract::getInstance($modelName);
            } else {
                $models[$class] = null;
            }
        }
        return $models[$class];
    }

    public static function createChildModel($class)
    {
        static $models = array();
        if (!array_key_exists($class, $models)) {
            if (Vpc_Abstract::hasSetting($class, 'tablename')) {
                $t = self::createTable($class);
                if (!$t instanceof Zend_Db_Table_Abstract) {
                    throw new Vps_Exception("table setting for generator in $class is not a Zend_Db_Table");
                }
                $models[$class] = new Vps_Model_Db(array(
                    'table' => $t
                ));
            } else if (Vpc_Abstract::hasSetting($class, 'childModel')) {
                $modelName = Vpc_Abstract::getSetting($class, 'childModel');
                $models[$class] = Vps_Model_Abstract::getInstance($modelName);
            } else {
                $models[$class] = null;
            }
        }
        return $models[$class];
    }

    public static function createFormModel($class)
    {
        static $models = array();
        if (!array_key_exists($class, $models)) {
            if (Vpc_Abstract::hasSetting($class, 'formModel')) {
                $modelName = Vpc_Abstract::getSetting($class, 'formModel');
                $models[$class] = Vps_Model_Abstract::getInstance($modelName);
            } else {
                $models[$class] = null;
            }
        }
        return $models[$class];
    }

    /**
     * @deprecated
     */
    public function getModel()
    {
        return $this->getOwnModel();
    }

    public function getOwnModel()
    {
        return self::createOwnModel(get_class($this));
    }

    public function getChildModel()
    {
        return self::createChildModel(get_class($this));
    }

    public function getFormModel()
    {
        return self::createFormModel(get_class($this));
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
        if ($useSettingsCache) {
            $s =& self::_getSettingsCached();
            $ret = array_keys($s);
            unset($ret[array_search('mtime', $ret)]);
            unset($ret[array_search('mtimeFiles', $ret)]);
            return array_values($ret);
        }
        $root = Vps_Component_Data_Root::getComponentClass();
        if (!$root) return array();
        $componentClasses = array($root);
        self::_getChildComponentClasses($componentClasses, $root);
        return $componentClasses;
    }

    private static function _getChildComponentClasses(&$componentClasses, $class)
    {
        $classes = array();
        foreach (Vpc_Abstract::getSetting($class, 'generators', false) as $generator) {
            if (is_array($generator['component'])) {
                $classes = array_merge($classes, $generator['component']);
            } else {
                $classes[] = $generator['component'];
            }
        }
        $plugins = Vpc_Abstract::getSetting($class, 'plugins', false);
        if (is_array($plugins)) {
            $classes = array_merge($classes, $plugins);
        }
        foreach ($classes as $c) {
            if ($c&& !in_array($c, $componentClasses)) {
                if (!class_exists($c)) {
                    throw new Vps_Exception("Component Class '$c' does not exist, used in '$class'");
                }
                $componentClasses[] = $c;
                self::_getChildComponentClasses($componentClasses, $c);
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