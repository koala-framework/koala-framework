<?php
abstract class Kwf_Component_Abstract_ExtConfig_Abstract
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
     * @param string welches setting verwendet werden soll, zB extConfigControllerIndex im Kwf_Controller_Action_Auto_Kwc_Grid
     * @return $this
     */
    public static function getInstance($componentClass, $setting = 'extConfig')
    {
        static $instances = array();
        if (!isset($instances[$componentClass.'-'.$setting])) {
            $c = Kwc_Abstract::getSetting($componentClass, $setting);
            if (!$c) throw new Kwf_Exception("extConfig not set");
            $instances[$componentClass.'-'.$setting] = new $c($componentClass);
        }
        return $instances[$componentClass.'-'.$setting];
    }


    protected final function _getSetting($name)
    {
        return Kwc_Abstract::getSetting($this->_class, $name);
    }

    //TODO code hierher verschieben
    public function getControllerUrl($class = 'Index')
    {
        return Kwc_Admin::getInstance($this->_class)->getControllerUrl($class);
    }

    protected final function _getAdmin()
    {
        return Kwc_Admin::getInstance($this->_class);
    }

    public final function getConfig($type)
    {
        if ($type == self::TYPE_SHARED) {
            if (Kwc_Abstract::getFlag($this->_class, 'sharedDataClass')) {
                return $this->_getConfig();
            }
        } else {
            if (!Kwc_Abstract::getFlag($this->_class, 'sharedDataClass')) {
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
            if (!Kwc_Abstract::hasSetting($this->_class, 'componentName')
                || !Kwc_Abstract::getSetting($this->_class, 'componentName'))
            {
                throw new Kwf_Exception("Component '$this->_class' does have no componentName but must have one for editing");
            }
            $title = Kwf_Trl::getInstance()->trlStaticExecute($this->_getSetting('componentName'));
            $pos = strpos($title, '.');
            if ($pos !== false && substr($title, $pos + 1, 1) !== ' ') $title = substr(strrchr($title, '.'), 1);
        }

        if (!$icon) $icon = new Kwf_Asset($this->_getSetting('componentIcon'));
        if ($icon instanceof Kwf_Asset) $icon = $icon->__toString();
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

    public static function getEditConfigs($componentClass, Kwf_Component_Generator_Abstract $gen, $idTemplate = null, $componentIdSuffix = '')
    {
        $ret = array(
            'componentConfigs' => array(),
            'contentEditComponents' => array(),
        );
        if (is_null($idTemplate)) {
            if ($gen->hasSetting('dbIdShortcut')) {
                $idTemplate = $gen->getSetting('dbIdShortcut') . '{0}';
            } else {
                $idTemplate = '{componentId}'.$gen->getIdSeparator().'{0}';
            }
        }
        $cfg = Kwc_Admin::getInstance($componentClass)->getExtConfig();
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
                'idSeparator' => $gen->getIdSeparator(),
                'componentIdSuffix' => $suffix,
                'title' => $c['title'],
                'icon' => $c['icon']
            );
        }
        foreach ($gen->getGeneratorPlugins() as $plugin) {
            $cls = get_class($plugin);
            $cfg = Kwc_Admin::getInstance($cls)->getExtConfig();
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
        if (Kwc_Abstract::hasSetting($componentClass, 'editComponents')) {
            $editComponents = Kwc_Abstract::getSetting($componentClass, 'editComponents');
            foreach ($editComponents as $c) {
                $childGen = Kwf_Component_Generator_Abstract::getInstances($componentClass, array('componentKey'=>$c));
                if (!$childGen) {
                    throw new Kwf_Exception("editComponents '$c' doesn't exist in '$componentClass'");
                }
                $childGen = $childGen[0];
                $cls = Kwc_Abstract::getChildComponentClass($componentClass, null, $c);
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
