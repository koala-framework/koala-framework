<?php
abstract class Kwf_Component_MasterLayout_Abstract
{
    protected $_class;
    protected $_settings;
    public function __construct($class, array $settings)
    {
        $this->_class = $class;
        $this->_settings = $settings;
        $this->_init();
    }

    protected function _init()
    {
    }

    protected function _getSetting($name)
    {
        return Kwc_Abstract::getSetting($this->_class, $name);
    }

    protected function _hasSetting($name)
    {
        return Kwc_Abstract::hasSetting($this->_class, $name);
    }

    public function getInstance($class)
    {
        static $i = array();
        if (!isset($i[$class])) {
            if (!Kwc_Abstract::hasSetting($class, 'masterLayout')) {
                throw new Kwf_Exception("No masterLayout set for '$class'");
            }
            $layout = Kwc_Abstract::getSetting($class, 'masterLayout');
            $class = $layout['class'];
            unset($layout['class']);
            $i[$class] = new $class($class, $layout);
        }
        return $i[$class];
    }

    abstract public function getContexts(Kwf_Component_Data $data);
    abstract public function getContentWidth(Kwf_Component_Data $data);
}
