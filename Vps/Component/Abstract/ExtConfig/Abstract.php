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
     * @param string componentClass
     * @param string welches setting verwendet werden soll, zB extConfigControllerIndex im Vps_Controller_Action_Auto_Vpc_Grid
     * @return $this
     */
    public static function getInstance($componentClass, $setting = 'extConfig')
    {
        static $instances = array();
        if (!isset($instances[$componentClass.'-'.$setting])) {
            $c = Vpc_Abstract::getSetting($componentClass, $setting);
            if (!$c) throw new Vps_Exception("extConfig not set");
            $instances[$componentClass.'-'.$setting] = new $c($componentClass);
        }
        return $instances[$componentClass.'-'.$setting];
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
            $pos = strpos($title, '.');
            if ($pos !== false && substr($title, $pos + 1, 1) !== ' ') $title = substr(strrchr($title, '.'), 1);
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

    public static function getEditConfigs($componentClass, Vps_Component_Generator_Abstract $gen, $idTemplate, $componentIdSuffix)
    {
        $ret = array(
            'componentConfigs' => array(),
            'contentEditComponents' => array(),
        );
        $cfg = Vpc_Admin::getInstance($componentClass)->getExtConfig();
        foreach ($cfg as $k=>$c) {
            $suffix = $componentIdSuffix;
            if (isset($c['componentIdSuffix'])) {
                $suffix .= $c['componentIdSuffix'];
                unset($c['componentIdSuffix']);
            }
            $ret['componentConfigs'][$componentClass.'-'.$k] = $c;
            $ret['contentEditComponents'][] = array(
                'componentClass' => $componentClass,
                'type' => $k,
                'idTemplate' => $idTemplate,
                'componentIdSuffix' => $suffix,
                'title' => $c['title'],
                'icon' => $c['icon']
            );
        }
        foreach ($gen->getGeneratorPlugins() as $plugin) {
            $cls = get_class($plugin);
            $cfg = Vpc_Admin::getInstance($cls)->getExtConfig();
            foreach ($cfg as $k=>$c) {
                $suffix = $componentIdSuffix;
                if (isset($c['componentIdSuffix'])) {
                    $suffix .= $c['componentIdSuffix'];
                    unset($c['componentIdSuffix']);
                }
                $ret['componentConfigs'][$cls.'-'.$k] = $c;
                $ret['contentEditComponents'][] = array(
                    'componentClass' => $cls,
                    'type' => $k,
                    'idTemplate' => $idTemplate,
                    'componentIdSuffix' => $suffix
                );
            }
        }
        if (Vpc_Abstract::hasSetting($componentClass, 'editComponents')) {
            $editComponents = Vpc_Abstract::getSetting($componentClass, 'editComponents');
            foreach ($editComponents as $c) {
                $childGen = Vps_Component_Generator_Abstract::getInstances($componentClass, array('componentKey'=>$c));
                $childGen = $childGen[0];
                $cls = Vpc_Abstract::getChildComponentClass($componentClass, null, $c);
                $edit = self::getEditConfigs($cls, $childGen,
                                               $idTemplate,
                                               $componentIdSuffix.$childGen->getIdSeparator().$c);
                $ret['componentConfigs'] = array_merge($ret['componentConfigs'], $edit['componentConfigs']);
                $ret['contentEditComponents'] = array_merge($ret['contentEditComponents'], $edit['contentEditComponents']);
            }
        }
        return $ret;
    }
}
