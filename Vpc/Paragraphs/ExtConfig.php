<?php
class Vpc_Paragraphs_ExtConfig extends Vps_Component_Abstract_ExtConfig_Abstract
{
    private function _componentNameToArray($name, $component, &$componentList)
    {
        $names = explode('.', $name, 2);
        if (count($names) > 1) {
            $this->_componentNameToArray($names[1], $component, $componentList[$names[0]]);
        } else {
            $componentList[$name] = $component;
        }
    }

    protected function _getConfig()
    {
        $componentList = array();
        $componentIcons = array();
        foreach ($this->_getComponents() as $component) {
            if (!Vpc_Abstract::hasSetting($component, 'componentName')) continue;
            $name = Vpc_Abstract::getSetting($component, 'componentName');
            $icon = Vpc_Abstract::getSetting($component, 'componentIcon');
            if ($icon) {
                $icon = $icon->__toString();
            }
            if ($name) {
                $this->_componentNameToArray($name, $component, $componentList);
                $componentIcons[$component] = $icon;
            }
        }

        $config = $this->_getStandardConfig('vpc.paragraphs');
        $config['components'] = $componentList;
        $config['componentIcons'] = $componentIcons;
        $config['needsComponentPanel'] = true;
        return array(
            'paragraphs' => $config
        );
    }

    protected function _getComponents()
    {
        return Vpc_Abstract::getChildComponentClasses($this->_class, 'paragraphs');
    }
}
