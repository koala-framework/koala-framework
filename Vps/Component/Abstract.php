<?php
class Vps_Component_Abstract
{
    private static $_settings = null;
    private static $_rebuildingSettings = false;
    private static $_modelsCache = array(
        'own' => array(),
        'child' => array(),
        'form' => array()
    );

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
        if (self::$_rebuildingSettings || !$settingsCache) {
            //um endlosschleife in settingsCache zu verhindern
            $c = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
            if (!class_exists($c)) {
                throw new Vps_Exception("Invalid component '$class'");
            }
            $param = strpos($class, '.') ? substr($class, strpos($class, '.')+1) : null;
            $settings = call_user_func(array($c, 'getSettings'), $param);
            return isset($settings[$setting]);
        }
        //& für performance
        $s =& self::_getSettingsCached();
        if (!isset($s[$class])) {
            throw new Vps_Exception("No Settings for component '$class' found; it is probably not in allComponentClasses. Requested setting: $setting");
        }
        return array_key_exists($setting, $s[$class]);
    }

    public static function getSetting($class, $setting)
    {
        static $settingsCache;
        if (is_null($settingsCache)) $settingsCache = Vps_Registry::get('config')->debug->settingsCache;
        if (self::$_rebuildingSettings || !$settingsCache) {
            //um endlosschleife in settingsCache zu verhindern
            if (!class_exists(strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class)) {
                throw new Vps_Exception("Invalid component '$class'");
            }
            if ($setting == 'parentClasses') {
                $p = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
                $ret = array();
                do {
                    $ret[] = $p;
                } while ($p = get_parent_class($p));
                return $ret;
            } else {
                $c = strpos($class, '.') ? substr($class, 0, strpos($class, '.')) : $class;
                $param = strpos($class, '.') ? substr($class, strpos($class, '.')+1) : null;
                $settings = call_user_func(array($c, 'getSettings'), $param);
                if (!array_key_exists($setting, $settings)) {
                    throw new Vps_Exception("Couldn't find required setting '$setting' for $c.");
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
            $cacheId = 'componentSettings'.Vps_Trl::getInstance()->getTargetLanguage()
                                .'_'.Vps_Component_Data_Root::getComponentClass();
            self::$_settings = $cache->load($cacheId);
            if (!self::$_settings) {
                self::$_rebuildingSettings = true;
                self::$_settings = array();
                self::$_settings['mtimeFiles'] = array();
                $incPaths = explode(PATH_SEPARATOR, get_include_path());
                foreach (self::getComponentClasses(false/*don't use settings cache*/) as $c) {
                    $realCls = strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c;
                    $param = strpos($c, '.') ? substr($c, strpos($c, '.')+1) : null;
                    self::$_settings[$c] = call_user_func(array($realCls, 'getSettings'), $param);

                    //generators �ber getSetting holen, da dort noch die aus der config dazugemixt werden
                    self::$_settings[$c]['generators'] = self::getSetting($c, 'generators', false/*don't use settings cache*/);

                    try {
                        call_user_func(array($realCls, 'validateSettings'), self::$_settings[$c], $c);
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
                        if ($i == $c) continue;
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
                    $p = strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c;
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
                self::$_rebuildingSettings = false;

                $cache->save(self::$_settings, $cacheId);
            }
        }
        return self::$_settings;
    }

    static private function _formatCssClass($c)
    {
        $c = strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c;
        if (substr($c, -10) == '_Component') {
            $c = substr($c, 0, -10);
        }
        $c = str_replace('_', '', $c);
        return strtolower(substr($c, 0, 1)) . substr($c, 1);
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
        return self::createTable($this->getData()->componentClass);
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
        if (!array_key_exists($class, self::$_modelsCache['own'])) {
            if (Vpc_Abstract::hasSetting($class, 'tablename')) {
                $t = self::createTable($class);
                if (!$t instanceof Zend_Db_Table_Abstract) {
                    throw new Vps_Exception("table setting for generator in $class is not a Zend_Db_Table");
                }
                $model = new Vps_Model_Db(array(
                    'table' => $t
                ));
            } else if (Vpc_Abstract::hasSetting($class, 'ownModel')) {
                $modelName = Vpc_Abstract::getSetting($class, 'ownModel');
                $model = Vps_Model_Abstract::getInstance($modelName);
            } else {
                $model = null;
            }
            self::$_modelsCache['own'][$class] = $model;
        }
        return self::$_modelsCache['own'][$class];
    }

    public static function createChildModel($class)
    {
        if (!array_key_exists($class, self::$_modelsCache['child'])) {
            if (Vpc_Abstract::hasSetting($class, 'tablename')) {
                $t = self::createTable($class);
                if (!$t instanceof Zend_Db_Table_Abstract) {
                    throw new Vps_Exception("table setting for generator in $class is not a Zend_Db_Table");
                }
                $model = new Vps_Model_Db(array(
                    'table' => $t
                ));
            } else if (Vpc_Abstract::hasSetting($class, 'childModel')) {
                $modelName = Vpc_Abstract::getSetting($class, 'childModel');
                $model = Vps_Model_Abstract::getInstance($modelName);
            } else {
                $model = null;
            }
            self::$_modelsCache['child'][$class] = $model;
        }
        return self::$_modelsCache['child'][$class];
    }

    public static function createFormModel($class)
    {
        if (!array_key_exists($class, self::$_modelsCache['form'])) {
            if (Vpc_Abstract::hasSetting($class, 'formModel')) {
                $modelName = Vpc_Abstract::getSetting($class, 'formModel');
                self::$_modelsCache['form'][$class] = Vps_Model_Abstract::getInstance($modelName);
            } else {
                self::$_modelsCache['form'][$class] = null;
            }
        }
        return self::$_modelsCache['form'][$class];
    }

    public static function clearModelInstances()
    {
        self::$_modelsCache = array(
            'own' => array(),
            'child' => array(),
            'form' => array()
        );
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
        return self::createOwnModel($this->getData()->componentClass);
    }

    public function getChildModel()
    {
        return self::createChildModel($this->getData()->componentClass);
    }

    public function getFormModel()
    {
        return self::createFormModel($this->getData()->componentClass);
    }

    protected function _getSetting($setting)
    {
        return self::getSetting($this->getData()->componentClass, $setting);
    }

    protected function _hasSetting($setting)
    {
        return self::hasSetting($this->getData()->componentClass, $setting);
    }

    static public function getFlag($class, $flag)
    {
        $flags = self::getSetting($class, 'flags');
        if (!isset($flags[$flag])) return false;
        return $flags[$flag];
    }

    public static function getComponentClasses()
    {
        if (!self::$_rebuildingSettings) {
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
        if (Vpc_Abstract::hasSetting($class, 'inheritComponentClass', false)) {
            $classes[] = Vpc_Abstract::getSetting($class, 'inheritComponentClass', false);
        }
        foreach ($classes as $c) {
            if ($c&& !in_array($c, $componentClasses)) {
                if (!class_exists(strpos($c, '.') ? substr($c, 0, strpos($c, '.')) : $c)) {
                    throw new Vps_Exception("Component Class '$c' does not exist, used in '$class'");
                }
                $componentClasses[] = $c;
                self::_getChildComponentClasses($componentClasses, $c);
            }
        }
    }
}