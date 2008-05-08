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

    public static function getSetting($class, $setting)
    {
        if (!Vps_Loader::classExists($class)) {
            $class = substr($class, 0, strrpos($class, '_')) . '_Component';
        }

        if (class_exists($class)) {
            $settings = call_user_func(array($class, 'getSettings'));
            if (!isset($settings[$setting])) {
                throw new Vps_Exception("Setting '$setting' does not exist for Component $class");
            }
            return isset($settings[$setting]) ? $settings[$setting] : null ;
        } else {
            throw new Vps_Exception("Component '$class' does not exist");
        }
    }

    public static function getSettings()
    {
        return array(
            'assets'        => array('files'=>array(), 'dep'=>array()),
            'assetsAdmin'   => array('files'=>array(), 'dep'=>array()),
            'componentIcon' => new Vps_Asset('paragraph_page'),
            'placeholder'   => array()
        );
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

    public static function getComponentClasses($class = null)
    {
        static $componentClasses = array();
        if (!$componentClasses) { $componentClasses = array(); }
        if (!$class) {
            if ($componentClasses) return $componentClasses;
            $classes = array();
            $classes[] = 'Vpc_Root_Component';
            foreach (Zend_Registry::get('config')->vpc->pageClasses as $c) {
                if ($c->class && $c->text) {
                    $classes[] = $c->class;
                }
            }
        } else {
            $classes = Vpc_Abstract::getSetting($class, 'childComponentClasses');
            if (!is_array($classes)) return;
        }
        if (!is_array($componentClasses)) d($componentClasses);
        foreach ($classes as $class) {
            if ($class && !in_array($class, $componentClasses)) {
                $componentClasses[] = $class;
                self::getComponentClasses($class);
            }
        }
        return $componentClasses;
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