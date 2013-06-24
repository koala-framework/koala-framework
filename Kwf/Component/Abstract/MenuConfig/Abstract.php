<?php
abstract class Kwf_Component_Abstract_MenuConfig_Abstract
{
    protected $_class;

    public function __construct($componentClass)
    {
        $this->_class = $componentClass;
    }

    /**
     * @param string componentClass
     * @return $this
     */
    public static function getInstance($componentClass)
    {
        static $instances = array();
        if (!isset($instances[$componentClass])) {
            $c = Kwc_Abstract::getSetting($componentClass, 'menuConfig');
            if (!$c) throw new Kwf_Exception("extConfig not set");
            $instances[$componentClass] = new $c($componentClass);
        }
        return $instances[$componentClass];
    }

    protected final function _getSetting($name)
    {
        return Kwc_Abstract::getSetting($this->_class, $name);
    }

    // integer, higher number means it's called after all with lower number, default 0
    public function getPriority()
    {
        return 0;
    }

    abstract public function addResources(Kwf_Acl $acl);

    public function getEventsClass()
    {
        return null;
    }
}
