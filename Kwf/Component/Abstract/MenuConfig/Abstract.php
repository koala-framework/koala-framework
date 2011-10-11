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

    public function getPriority()
    {
        return 10;
    }

    abstract public function addResources(Kwf_Acl $acl);
}
