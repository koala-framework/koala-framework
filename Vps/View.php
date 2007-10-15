<?php
abstract class Vps_View extends Zend_View_Abstract
{
    public function getConfig(Vpc_Abstract $component, $config = array(), $includeClass = true)
    {
        $setup = $this->getAdmin($component);
        $config = array_merge($config, $setup->getControllerConfig($component, $this));
        if (!isset($config['controllerUrl'])) {
            $config['controllerUrl'] = $this->getControllerUrl($component);
        }
        if ($includeClass) {
            $return['config'] = $config;
            $return['class'] = $this->getClass($component);
            return $return;
        } else {
            return $config;
        }
    }

    public function getControllerUrl(Vpc_Abstract $component, $class = '')
    {
        if ($class == '') { $class = get_class($component); }
        return '/component/edit/' . $class . '/' . $component->getId() . '/';
    }

    public function getClass(Vpc_Abstract $component)
    {
        $setup = $this->getAdmin($component);
        return $setup->getControllerClass();
    }

    public function getAdmin($component)
    {
        return Vpc_Admin::getInstance(get_class($component));
    }

}
