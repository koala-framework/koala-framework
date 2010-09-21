<?php
abstract class Vps_Component_Abstract_ExtConfig_Abstract
{
    const TYPE_DEFAULT = 'default';
    const TYPE_SHARED = 'shared';

    protected $_class;

    public function __construct($componentClass)
    {
        $this->_class = $componentClass;
    }

    /**
     * @return $this
     */
    public static function getInstance($componentClass)
    {
        static $instances = array();
        if (!isset($instances[$componentClass])) {
            $c = Vpc_Abstract::getSetting($componentClass, 'extConfig');
            if (!$c) throw new Vps_Exception("extConfig not set");
            $instances[$componentClass] = new $c($componentClass);
        }
        return $instances[$componentClass];
    }


    protected final function _getSetting($name)
    {
        return Vpc_Abstract::getSetting($this->_class, $name);
    }

    //TODO code hierher verschieben
    public function getControllerUrl($class = 'Index')
    {
        return Vpc_Admin::getInstance($this->_class)->getControllerUrl($class);
    }

    protected final function _getAdmin()
    {
        return Vpc_Admin::getInstance($this->_class);
    }

    public final function getConfig($type)
    {
        if ($type == self::TYPE_SHARED) {
            if (Vpc_Abstract::getFlag($this->_class, 'sharedDataClass')) {
                return $this->_getConfig();
            }
        } else {
            if (!Vpc_Abstract::getFlag($this->_class, 'sharedDataClass')) {
                return $this->_getConfig();
            }
        }
        return array();
    }

    /**
     * Welche config direkt nach dem anlegen dieser Komponente geöffnet werden soll.
     *
     * Fragt der Paragraphs Controller ab.
     */
    public function getEditAfterCreateConfigKey()
    {
        $keys = array_keys($this->getConfig(self::TYPE_DEFAULT));
        if (!$keys) return null;
        return $keys[0];
    }

    abstract protected function _getConfig();

    protected final function _getStandardConfig($xtype, $controllerName = 'Index', $title = null, $icon = null)
    {
        if (!$title) {
            if (!Vpc_Abstract::hasSetting($this->_class, 'componentName')
                || !Vpc_Abstract::getSetting($this->_class, 'componentName'))
            {
                throw new Vps_Exception("Component '$this->_class' does have no componentName but must have one for editing");
            }
            $title = $this->_getSetting('componentName');
            if (strpos($title, '.') !== false) $title = substr(strrchr($title, '.'), 1);
        }

        if (!$icon) $icon = $this->_getSetting('componentIcon');
        if ($icon instanceof Vps_Asset) $icon = $icon->__toString();
        $ret = array(
            'xtype' => $xtype,
            'title' => $title,
            'icon' => $icon
        );
        if ($controllerName) {
            $ret['controllerUrl'] = $this->getControllerUrl($controllerName);
        }
        return $ret;
    }
}
