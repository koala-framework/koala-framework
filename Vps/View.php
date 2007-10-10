<?php
abstract class Vps_View extends Zend_View_Abstract
{
    public function getConfig(Vpc_Abstract $component, $config = array(), $includeClass = true)
    {
        $setup = $this->getSetup($component);
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

    public function getControllerUrl(Vpc_Abstract $component)
    {
        return '/component/edit/' . get_class($component) . '/' . $component->getId() . '/';
    }

    public function getClass(Vpc_Abstract $component)
    {
        $setup = $this->getSetup($component);
        return $setup->getControllerClass();
    }

    public function getSetup($component)
    {
        return Vpc_Admin::getInstance(get_class($component));
    }

}
