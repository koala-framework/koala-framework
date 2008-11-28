<?php
class Vps_Component_ComponentModel extends Vps_Model_Data_Abstract
{
    protected $_columns = array('id', 'name', 'domain');

    protected function _init()
    {
        $this->_data = array();
        $generators = Vps_Component_Data_Root::getInstance()->getPageGenerators();
        $classes = array();
        foreach ($generators as $generator) {
            $domain = $generator->getDomain();
            if (!isset($classes[$domain])) $classes[$domain] = array();
            $classes[$domain] = array_merge($classes[$domain], $generator->getChildComponentClasses());
        }
        foreach ($classes as $domain => $c) {
            foreach ($c as $component=>$class) {
                $name = Vpc_Abstract::getSetting($class, 'componentName');
                if ($name) {
                    $name = str_replace('.', ' ', $name);
                    $this->_data[] = array('id' => $component, 'name' => $name, 'domain' => $domain);
                }
            }
        }
        parent::_init();
    }
}
