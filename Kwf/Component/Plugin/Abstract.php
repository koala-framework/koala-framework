<?php
abstract class Kwf_Component_Plugin_Abstract extends Kwf_Component_Abstract
{
    protected $_componentId;
    static private $_instances = array();

    public function __construct($componentId = null)
    {
        $this->_componentId = $componentId;
        parent::__construct($componentId);
    }

    public static function getInstance($pluginClass, $componentId)
    {
        if (!isset(self::$_instances[$pluginClass][$componentId])) {
            self::$_instances[$pluginClass][$componentId] = new $pluginClass($componentId);
        }
        return self::$_instances[$pluginClass][$componentId];
    }

    protected function _getSetting($setting)
    {
        return self::getSetting(get_class($this), $setting);
    }

    protected function _hasSetting($setting)
    {
        return self::hasSetting(get_class($this), $setting);
    }
}
