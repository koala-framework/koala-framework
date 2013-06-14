<?php
class Kwf_Component_Abstract
{
    private static $_modelsCache = array(
        'own' => array(),
        'child' => array(),
        'form' => array(),
        'table' => array()
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

    public final static function hasSettings($class)
    {
        return Kwf_Component_Settings::hasSettings($class);
    }

    public final static function hasSetting($class, $setting)
    {
        return Kwf_Component_Settings::hasSetting($class, $setting);
    }

    public final static function getSetting($class, $setting)
    {
        return Kwf_Component_Settings::getSetting($class, $setting);
    }

    public final static function getSettingMtime()
    {
        return Kwf_Component_Settings::getSettingMtime();
    }

    //wenn root geändert wird muss der cache hier gelöscht werden können
    public final static function resetSettingsCache()
    {
        Kwf_Component_Settings::resetSettingsCache();
    }

    static final public function formatCssClass($c)
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
            'componentIcon' => new Kwf_Asset('paragraph_page'),
            'placeholder'   => array(),
            'plugins'       => array(),
            'generators'    => array(),
            'flags'         => array(),
            'extConfig'     => 'Kwf_Component_Abstract_ExtConfig_None'
        );
    }

    public static function validateSettings($settings, $componentClass)
    {
        if (isset($settings['ownModel']) && $settings['ownModel']) {
            if (Kwf_Setup::hasDb()) { //only check if db is set up correclty (might not during installation)
                try {
                    $m = Kwf_Model_Abstract::getInstance($settings['ownModel']);
                    $pk = $m->getPrimaryKey();
                } catch (Exception $e) {}
                if (isset($pk) && $pk != 'component_id') {
                    throw new Kwf_Exception("ownModel for '$componentClass' must have 'component_id' as primary key");
                }
            }
        }
        if (isset($settings['modelname'])) {
            throw new Kwf_Exception("modelname for '$componentClass' is set - please rename into ownModel or childModel");
        }
        if (isset($settings['model'])) {
            throw new Kwf_Exception("model for '$componentClass' is set - please rename into ownModel or childModel");
        }
        if (isset($settings['formModel'])) {
            throw new Kwf_Exception("formModel is no longer supported. Set the model in the FrontendForm.php. Component: '$componentClass'");
        }
    }

    /**
     * @deprecated
     * @internal
     */
    public function getTable($tablename = null)
    {
        return self::createTable($this->getData()->componentClass);
    }

    /**
     * @deprecated
     * @internal
     */
    public static function createTable($class, $tablename = null)
    {
        $tables = self::$_modelsCache['table'];
        if (!isset($tables[$class.'-'.$tablename])) {
            if (!$tablename) {
                $tablename = Kwc_Abstract::getSetting($class, 'tablename');
                if (!$tablename) {
                    throw new Kwc_Exception('No tablename in Setting defined: ' . $class);
                }
            }
            if (!is_instance_of($tablename, 'Zend_Db_Table_Abstract')) {
                throw new Kwf_Exception("table setting '$tablename' for generator in $class is not a Zend_Db_Table");
            }
            $tables[$class.'-'.$tablename] = new $tablename(array('componentClass'=>$class));
            if (!$tables[$class.'-'.$tablename] instanceof Zend_Db_Table_Abstract) {
                throw new Kwf_Exception("table setting for generator in $class is not a Zend_Db_Table");
            }
        }
        return $tables[$class.'-'.$tablename];
    }

    /**
     * @deprecated
     * @internal
     */
    public static function createModel($class)
    {
        return self::createOwnModel($class);
    }

    /**
     * @return Kwf_Model_Abstract
     */
    public static function createOwnModel($class)
    {
        if (!array_key_exists($class, self::$_modelsCache['own'])) {
            if (Kwc_Abstract::hasSetting($class, 'tablename')) {
                $t = self::createTable($class);
                if (!$t instanceof Zend_Db_Table_Abstract) {
                    throw new Kwf_Exception("table setting for generator in $class is not a Zend_Db_Table");
                }
                $model = new Kwf_Model_Db(array(
                    'table' => $t
                ));
            } else if (Kwc_Abstract::hasSetting($class, 'ownModel')) {
                $modelName = Kwc_Abstract::getSetting($class, 'ownModel');
                $model = Kwf_Model_Abstract::getInstance($modelName);
            } else {
                $model = null;
            }
            self::$_modelsCache['own'][$class] = $model;
        }
        return self::$_modelsCache['own'][$class];
    }

    /**
     * @return Kwf_Model_Abstract
     */
    public static function createChildModel($class)
    {
        if (!array_key_exists($class, self::$_modelsCache['child'])) {
            if (Kwc_Abstract::hasSetting($class, 'tablename')) {
                $t = self::createTable($class);
                if (!$t instanceof Zend_Db_Table_Abstract) {
                    throw new Kwf_Exception("table setting for generator in $class is not a Zend_Db_Table");
                }
                $model = new Kwf_Model_Db(array(
                    'table' => $t
                ));
            } else if (Kwc_Abstract::hasSetting($class, 'childModel')) {
                $modelName = Kwc_Abstract::getSetting($class, 'childModel');
                $model = Kwf_Model_Abstract::getInstance($modelName);
            } else {
                $model = null;
            }
            self::$_modelsCache['child'][$class] = $model;
        }
        return self::$_modelsCache['child'][$class];
    }

    /**
     * @return Kwf_Model_Abstract
     */
    public static function createFormModel($class)
    {
        if (!array_key_exists($class, self::$_modelsCache['form'])) {
            if (Kwc_Abstract::hasSetting($class, 'formModel')) {
                $modelName = Kwc_Abstract::getSetting($class, 'formModel');
                self::$_modelsCache['form'][$class] = Kwf_Model_Abstract::getInstance($modelName);
            } else {
                self::$_modelsCache['form'][$class] = null;
            }
        }
        return self::$_modelsCache['form'][$class];
    }

    /**
     * @internal
     */
    public static function clearModelInstances()
    {
        self::$_modelsCache = array(
            'own' => array(),
            'child' => array(),
            'form' => array(),
            'table' => array()
        );
    }

    /**
     * @deprecated
     */
    public function getModel()
    {
        return $this->getOwnModel();
    }

    /**
     * @return Kwf_Model_Abstract
     */
    public function getOwnModel()
    {
        return self::createOwnModel($this->getData()->componentClass);
    }

    /**
     * @return Kwf_Model_Abstract
     */
    public function getChildModel()
    {
        return self::createChildModel($this->getData()->componentClass);
    }

    /**
     * @return Kwf_Model_Abstract
     */
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

    /**
     * Returns flag for a given componentClass
     *
     * Shortcut for getting flag using ::getSettings
     *
     * @param string componentClass
     * @param string flag name
     * @return mixed
     */
    static final public function getFlag($class, $flag)
    {
        return Kwf_Component_Settings::getFlag($class, $flag);
    }

    /**
     * Returns all component classes used in this app
     *
     * Fast, result is cached
     *
     * @return string[]
     */
    public final static function getComponentClasses()
    {
        return Kwf_Component_Settings::getComponentClasses();
    }

    /***
     * doesn't exist anymore, use getFulltextContent instead
     * @internal
     */
    public final function modifyFulltextDocument(Zend_Search_Lucene_Document $doc) {}
}